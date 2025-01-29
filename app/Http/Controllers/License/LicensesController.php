<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\LicensesRepository; 
use App\Repositories\License\LicenseTypesRepository;
use App\Repositories\License\ApplicantsRepository;
use App\Repositories\License\SpeciesRepository; 
use App\Repositories\Reference\IslandsRepository;
use App\Models\License\License;
use App\Models\License\LicenseType;
use App\Models\License\Applicant;
// use App\Models\License\Species;
use App\Models\License\LicenseItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Illuminate\Support\Facades\Log;
use App\Models\License\Species;
use App\Models\License\SpeciesIslandQuota;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Activitylog\Models\Activity;
// use Barryvdh\DomPDF\Facade\Pdf; // Or use Browsershot
use Intervention\Image\Facades\Image;
use App\Repositories\License\SpeciesIslandQuotaRepository; 




use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use DataTables;
use PDF;

use App\Mail\InvoiceEmail;
use Illuminate\Support\Facades\Mail;

class LicensesController extends Controller
{
    protected $licensesRepository;
    protected $applicantsRepository;
    protected $licenseTypesRepository;
    protected $speciesRepository;
    protected $islandsRepository;
    protected $speciesIslandQuotaRepository;

    /**
     * LicensesController constructor.
     *
     * @param LicensesRepository $licensesRepository
     */
    public function __construct(SpeciesIslandQuotaRepository $speciesIslandQuotaRepository,IslandsRepository $islandsRepository,SpeciesRepository $speciesRepository,LicensesRepository $licensesRepository,ApplicantsRepository $applicantsRepository, LicenseTypesRepository $licenseTypesRepository)
    {
        $this->licensesRepository = $licensesRepository;
        $this->applicantsRepository = $applicantsRepository;
        $this->licenseTypesRepository = $licenseTypesRepository;
        $this->speciesRepository = $speciesRepository;
        $this->islandsRepository = $islandsRepository;
        $this->speciesIslandQuotaRepository = $speciesIslandQuotaRepository;
    }

    /**
     * Get DataTable of licenses.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->licensesRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of licenses.
     *
     * @return Response
     */
    public function index()
    {
        $licenses = License::all();

    // Pass the licenses to the view
    return view('license.license.index', compact('licenses'));
    }

    /**
     * Show the form for creating a new license.
     *
     * @return Response
     */
    // LicensesController.php

    public function create()
    {
        $applicantId = session('temp_applicant_id');
        $applicant = $this->applicantsRepository->getById($applicantId);
    
        if (!$applicant) {
            return redirect()->route('license.applicants.create')
                            ->with('error', 'Please submit applicant information first.');
        }
    
        $licenseTypes = $this->licenseTypesRepository->pluck();
        $islands = $this->islandsRepository->pluck();
        $speciesByLicenseType = [];
        $availableQuotas = [];
    
        $currentYear = date('Y');
    
        foreach ($licenseTypes as $id => $name) {
            $speciesByLicenseType[$id] = $this->speciesRepository->getByLicenseType($id);
    
            foreach ($speciesByLicenseType[$id] as $species) {
                foreach ($islands as $islandId => $islandName) {
                    // Fetch quota with less strict year matching
                    $quota = SpeciesIslandQuota::where('species_id', $species->id)
                        ->where('island_id', $islandId)
                        ->whereNotNull('remaining_quota')  // Make sure remaining_quota exists
                        ->orderBy('year', 'desc')  // Get the most recent year
                        ->first();  // Get the full record
    
                    if ($quota) {
                        $availableQuotas[$species->id][$islandId] = $quota->remaining_quota;
                    } else {
                        $availableQuotas[$species->id][$islandId] = 0;
                    }
                }
            }
        }
    
        // Debug information
        \Log::info('Available Quotas:', $availableQuotas);
        \Log::info('Species by License Type:', $speciesByLicenseType);
    
        return view('license.license.create', compact(
            'applicant',
            'licenseTypes',
            'speciesByLicenseType',
            'islands',
            'availableQuotas'
        ));
    }
    
    
    // Add this method if you don't already have it
    private function getAvailableQuota($speciesId, $islandId)
    {
        $currentYear = date('Y');
    
        // Use the correct property name
        $quotaRecord = $this->speciesIslandQuotaRepository->getModelInstance()->where([
            'species_id' => $speciesId,
            'island_id' => $islandId,
            'year' => $currentYear,
        ])->first();
    
        return $quotaRecord ? $quotaRecord->remaining_quota : 0;
    }
    


    public function create2()
{
    // Fetch applicants, license types, and islands
    $applicants = $this->applicantsRepository->pluck(); // Assuming you need 'name' as value and 'id' as key
    $licenseTypes = $this->licenseTypesRepository->pluck(); // Assuming you need 'name' as value and 'id' as key
    $islands = $this->islandsRepository->pluck(); // Assuming you need 'name' as value and 'id' as key

    // Initialize arrays to store species by license type and available quotas
    $speciesByLicenseType = [];
    $availableQuotas = [];

    // Loop through each license type to get species and available quotas
    foreach ($licenseTypes as $id => $name) {
        // Fetch species for each license type
        $speciesByLicenseType[$id] = $this->speciesRepository->getByLicenseType($id);

        // Loop through each species for the current license type
        foreach ($speciesByLicenseType[$id] as $species) {
            foreach ($islands as $islandId => $islandName) {
                // Fetch quota for the species on the island, prioritizing the most recent year
                $quota = SpeciesIslandQuota::where('species_id', $species->id)
                    ->where('island_id', $islandId)
                    ->whereNotNull('remaining_quota')  // Ensure remaining_quota exists
                    ->orderBy('year', 'desc')  // Get the most recent year
                    ->first();  // Fetch the first record

                // Store the available quota for the species on the island
                if ($quota) {
                    $availableQuotas[$species->id][$islandId] = $quota->remaining_quota;
                } else {
                    $availableQuotas[$species->id][$islandId] = 0;  // No quota available
                }
            }
        }
    }

    // Optional: Log the available quotas and species by license type for debugging
    \Log::info('Available Quotas:', $availableQuotas);
    \Log::info('Species by License Type:', $speciesByLicenseType);

    // Return the view with the necessary data
    return view('license.license.create2', compact(
        'applicants',
        'licenseTypes',
        'speciesByLicenseType',
        'islands',
        'availableQuotas'
    ));
}



    
public function store(Request $request)
{
    try {
        DB::beginTransaction();
        
        // Log incoming request data
        Log::info('Incoming license request data:', $request->all());

        // Modified validation rules to allow empty quotas
        $validated = $request->validate([
            'license_type_id' => 'required|exists:license_types,id',
            'selected_islands' => 'required|array',
            'selected_islands.*' => 'exists:islands,id',
            'quotas' => 'array',
            'quotas.*.*' => 'nullable|numeric|min:0',
        ]);

        // Get applicant ID from session
        $applicantId = session('temp_applicant_id');
        if (!$applicantId) {
            throw new \Exception('No applicant information found.');
        }

        // Create new license
        $license = new License();
        $license->applicant_id = $applicantId;
        $license->license_type_id = $validated['license_type_id'];
        $license->created_by = Auth::id() ?? null;
        $license->status = 'pending';
        $license->invoice_date = now(); // Set invoice date to current date

        // Generate invoice number
        $licenseType = LicenseType::find($validated['license_type_id']);
        $date = now()->format('Ymd');
        $latestLicense = License::whereDate('created_at', today())->latest()->first();
        $sequence = $latestLicense ? (intval(substr($latestLicense->invoice_number, -4)) + 1) : 1;
        $license->invoice_number = $date . '-' . strtoupper(substr($licenseType->name, 0, 3)) . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $license->save();

        // Initialize total fee
        $totalLicenseFee = 0;

        // Process quotas for each species and island if quotas exist
        if (isset($validated['quotas']) && is_array($validated['quotas'])) {
            foreach ($validated['quotas'] as $speciesId => $islandQuotas) {
                $species = Species::findOrFail($speciesId);

                foreach ($islandQuotas as $islandId => $requestedQuota) {
                    // Skip if quota is empty or 0
                    if (empty($requestedQuota)) {
                        continue;
                    }

                    // Convert to float and check if greater than 0
                    $requestedQuota = floatval($requestedQuota);
                    if ($requestedQuota <= 0) {
                        continue;
                    }

                    // Verify available quota
                    $quota = SpeciesIslandQuota::where('species_id', $speciesId)
                        ->where('island_id', $islandId)
                        ->whereNotNull('remaining_quota')
                        ->orderBy('year', 'desc')
                        ->first();

                    if (!$quota || $quota->remaining_quota < $requestedQuota) {
                        throw new \Exception("Insufficient quota available for species ID {$speciesId} on island ID {$islandId}");
                    }

                    // Calculate fee for this item
                    $speciesFee = $requestedQuota * $species->unit_price;
                    $totalLicenseFee += $speciesFee;

                    // Create license item only for non-empty quotas
                    $license->licenseItems()->create([
                        'species_id' => $speciesId,
                        'island_id' => $islandId,
                        'requested_quota' => $requestedQuota,
                        'unit_price' => $species->unit_price,
                        'total_price' => $speciesFee,
                        'created_by' => Auth::id() ?? null,
                        'creator_type' => Auth::check() ? 'user' : 'guest',
                    ]);

                    // Update remaining quota
                    $quota->remaining_quota -= $requestedQuota;
                    $quota->save();

                    Log::info("Created license item", [
                        'license_id' => $license->id,
                        'species_id' => $speciesId,
                        'island_id' => $islandId,
                        'requested_quota' => $requestedQuota,
                        'total_price' => $speciesFee
                    ]);
                }
            }
        }

        // Calculate and update fees
        $vatRate = 0.125;
        $vatAmount = $totalLicenseFee * $vatRate;
        $totalAmountWithVat = $totalLicenseFee + $vatAmount;

        // Update license with fees and other details
        $license->total_fee = $totalLicenseFee;
        $license->vat_amount = $vatAmount;
        $license->total_amount_with_vat = $totalAmountWithVat;
        $license->updated_by = Auth::id() ?? null;
        $license->save();

        DB::commit();

        if (Auth::check()) {
            return redirect()->route('license.licenses.index')
                           ->with('success', 'License created successfully.');
        } else {
            return redirect('/')
                   ->with('showThankYouModal', true)
                   ->with('success', 'Your application has been submitted successfully.');
        }

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('License creation failed: ' . $e->getMessage());
        return back()->with('error', 'An error occurred while processing your request: ' . $e->getMessage());
    }
}

public function store2(Request $request)
{
    try {
        DB::beginTransaction();

        $validated = $request->validate([
            'license_type_id' => 'required|exists:license_types,id',
            'selected_islands' => 'required|array',
            'selected_islands.*' => 'exists:islands,id',
            'quotas' => 'required|array',
            'quotas.*.*' => 'numeric|min:0',
            'applicant_id' => 'required|exists:applicants,id',
        ]);

        // Debug: Log all validation details
        Log::info('Validation data:', [
            'applicant_id' => $request->input('applicant_id'),
            'applicants_table_check' => DB::table('applicants')->where('id', $request->input('applicant_id'))->exists()
        ]);

       

        // Additional debug checks before creating license
        $applicant = Applicant::find($validated['applicant_id']);
        if (!$applicant) {
            Log::error('Specific applicant not found', [
                'applicant_id' => $validated['applicant_id'],
                'all_applicant_ids' => Applicant::pluck('id')
            ]);
            throw new \Exception("No applicant information found.");
        }
        // Log incoming request data
        Log::info('Incoming license request data (store2):', $request->all());

      
        // Get applicant ID from request
        $applicantId = $validated['applicant_id'];

        $applicant = Applicant::find($applicantId);
if (!$applicant) {
    Log::error('Applicant not found', ['applicant_id' => $applicantId]);
    return back()->with('error', 'Applicant not found');
}

        // Create new license
        $license = new License();
        $license->applicant_id = $applicantId;
        $license->license_type_id = $validated['license_type_id'];
        $license->created_by = Auth::id() ?? null;

        // Generate invoice number
        $licenseType = LicenseType::find($validated['license_type_id']);
        $date = now()->format('Ymd');
        $latestLicense = License::whereDate('created_at', today())->latest()->first();
        $sequence = $latestLicense ? (intval(substr($latestLicense->invoice_number, -4)) + 1) : 1;
        $license->invoice_number = $date . '-' . strtoupper(substr($licenseType->name, 0, 3)) . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $license->save();

        // Initialize total fee
        $totalLicenseFee = 0;

        // Process quotas for each species and island
        foreach ($validated['quotas'] as $speciesId => $islandQuotas) {
            $species = Species::findOrFail($speciesId);

            foreach ($islandQuotas as $islandId => $requestedQuota) {
                // Skip if quota is 0 or empty
                if (empty($requestedQuota) || $requestedQuota <= 0) {
                    continue;
                }

                // Verify available quota
                $quota = SpeciesIslandQuota::where('species_id', $speciesId)
                    ->where('island_id', $islandId)
                    ->whereNotNull('remaining_quota')
                    ->orderBy('year', 'desc')
                    ->first();

                if (!$quota || $quota->remaining_quota < $requestedQuota) {
                    throw new \Exception("Insufficient quota available for species ID {$speciesId} on island ID {$islandId}");
                }

                // Calculate fee for this item
                $speciesFee = $requestedQuota * $species->unit_price;
                $totalLicenseFee += $speciesFee;

                // Create license item
                $license->licenseItems()->create([
                    'species_id' => $speciesId,
                    'island_id' => $islandId,
                    'requested_quota' => $requestedQuota,
                    'unit_price' => $species->unit_price,
                    'total_price' => $speciesFee,
                    'created_by' => Auth::id() ?? null,
                    'creator_type' => Auth::check() ? 'user' : 'guest',
                ]);

                // Update remaining quota
                $quota->remaining_quota -= $requestedQuota;
                $quota->save();

                Log::info("Created license item", [
                    'license_id' => $license->id,
                    'species_id' => $speciesId,
                    'island_id' => $islandId,
                    'requested_quota' => $requestedQuota,
                    'total_price' => $speciesFee
                ]);
            }
        }

        // Calculate and update fees
        $vatRate = 0.125;
        $vatAmount = $totalLicenseFee * $vatRate;
        $totalAmountWithVat = $totalLicenseFee + $vatAmount;

        $license->total_fee = $totalLicenseFee;
        $license->vat_amount = $vatAmount;
        $license->total_amount_with_vat = $totalAmountWithVat;
        $license->save();

        // Log activity
        activity()
            ->performedOn($license)
            ->causedBy(auth()->user())
            ->withProperties([
                'license_number' => $license->license_number,
                'applicant_name' => $license->applicant->first_name . ' ' . $license->applicant->last_name,
                'license_type' => $license->licenseType->name ?? 'N/A',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('License created');

        DB::commit();

        // Redirect based on authentication status
        if (Auth::check()) {
            return redirect()->route('license.licenses.index')
                           ->with('success', 'License created successfully.');
        } else {
            return redirect('/')
                   ->with('showThankYouModal', true)
                   ->with('success', 'Your application has been submitted successfully.');
        }

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('License creation failed: ' . $e->getMessage());
        return back()->with('error', 'An error occurred while processing your request: ' . $e->getMessage());
    }
}


    /**
     * Display the specified license.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        // Find the applicant by ID or fail if not found
        $applicant = Applicant::findOrFail($id);
    
        // Retrieve ALL licenses for the applicant
        $licenses = License::where('applicant_id', $id)
            ->with(['licenseType']) // Eager load license type
            ->get();
    
        // Get all license types and species
        $licenseTypes = LicenseType::all();
        $species = Species::all();
    
        // Initialize an array to store quotas for each license
        $requestedQuotas = [];
    
        // For each license, get its requested quotas
        foreach ($licenses as $license) {
            $requestedQuotas[$license->id] = LicenseItem::where('license_id', $license->id)
                ->with('species')
                ->get();
        }
    
        // Return the view with the relevant data
        return view('license.license.show', compact(
            'applicant',
            'licenses', // Now passing multiple licenses
            'licenseTypes',
            'species',
            'requestedQuotas'
        ));
    }

    /**
     * Show the form for editing the specified license.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
{
    $license = $this->licensesRepository->getById($id);
    
    if (!$license) {
        return redirect()->route('licenses.index')->with('error', 'License not found.');
    }

    $applicant = $this->applicantsRepository->getById($license->applicant_id);

    if (!$applicant) {
        return redirect()->route('applicants.create')->with('error', 'Please submit applicant information first.');
    }

    // Fetch applicants and license types for dropdowns if needed
    $applicants = $this->applicantsRepository->pluck();
    $licenseTypes = $this->licenseTypesRepository->pluck();
    $speciesList = $this->speciesRepository->pluck();

    return view('license.license.edit', compact('license', 'applicant', 'licenseTypes', 'speciesList'));
}


    /**
     * Update the specified license in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, License $license)
{
    // Validate incoming request data
    $data = $request->validate([
        'applicant_id' => 'required|exists:applicants,id',
        'license_type_id' => 'required|exists:license_types,id',
        'species' => 'required|array',
        'species.*.id' => 'required|exists:species,id',
        'species.*.requested_quota' => 'required|numeric|min:0',
    ]);

    // Log validated request data
    Log::info('License update request data:', $data);

    // Update basic license information
    $license->applicant_id = $data['applicant_id'];
    $license->license_type_id = $data['license_type_id'];
    $license->updated_by = Auth::id() ?? null;

    // Initialize total fee
    $totalLicenseFee = 0;

    // Delete existing license items
    $license->licenseItems()->delete();

    // Handle species
    foreach ($data['species'] as $speciesInput) {
        $species = Species::find($speciesInput['id']);
        $quantity = $speciesInput['requested_quota'];

        // Calculate total price for the species
        $speciesFee = $quantity * $species->unit_price;
        $totalLicenseFee += $speciesFee;

        // Log species details
        Log::info('Processing updated species:', [
            'species_id' => $species->id,
            'quantity' => $quantity,
            'unit_price' => $species->unit_price,
            'total_price' => $speciesFee
        ]);

        $updatedBy = Auth::check() ? Auth::id() : null;
        $creatorType = Auth::check() ? 'user' : 'guest';
        
        // Create new license items
        $license->licenseItems()->create([
            'species_id' => $species->id,
            'requested_quota' => $quantity,
            'unit_price' => $species->unit_price,
            'total_price' => $speciesFee,
            'created_by' => $updatedBy,
            'creator_type' => $creatorType,
        ]);
    }

    // Calculate VAT (12.5%)
    $vatRate = 0.125;
    $vatAmount = $totalLicenseFee * $vatRate;
    
    // Calculate total amount including VAT
    $totalAmountWithVat = $totalLicenseFee + $vatAmount;

    // Update license with fees
    $license->total_fee = $totalLicenseFee;
    $license->vat_amount = $vatAmount;
    $license->total_amount_with_vat = $totalAmountWithVat;
    $license->save();

    // Log the activity
    activity()
        ->performedOn($license)
        ->causedBy(auth()->user())
        ->withProperties([
            'license_number' => $license->license_number,
            'applicant_name' => $license->applicant->first_name . ' ' . $license->applicant->last_name,
            'license_type' => $license->licenseType->name ?? 'N/A',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ])
        ->log('License updated');

    // Log total fee update
    Log::info('Total license fee updated:', [
        'license_id' => $license->id,
        'total_fee' => $license->total_fee
    ]);

    // Check if the user is authenticated
    if (Auth::check()) {
        // For authenticated users, redirect to the invoice page
        return redirect()->route('license.licenses.index', $license->id)
               ->with('success', 'License updated successfully.');
    } else {
        // For non-authenticated users, redirect to home with thank you modal
        return redirect('/')
               ->with('showThankYouModal', true)
               ->with('success', 'Your application has been updated successfully.');
    }
}

    /**
     * Remove the specified license from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->licensesRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'License not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'License deleted successfully']);
    }

    

    public function showInvoice(License $license)
{
   
    return view('license.license.invoice', ['license' => $license, 'isPdfDownload' => false]);
}


public function downloadInvoice($id)
{
    $license = License::with(['licenseType', 'applicant', 'licenseItems.species'])->findOrFail($id);

    $pdf = PDF::loadView('license.license.invoice', ['license' => $license, 'isPdfDownload' => true]);
    
    // Kanakoa te email ma te PDF attachment
//    Mail::to($license->applicant->email)->send(new InvoiceEmail($license, $pdf));

    // Kaoka te PDF download
    return $pdf->download('invoice_' . $license->invoice_number . '.pdf');
}

public function issueLicense(Request $request, License $license)
{
    Log::info('Request Method: ' . $request->method());
    Log::info('Request URL: ' . $request->url());

    // Validate the request
    $validator = Validator::make($request->all(), [
        'issue_date' => 'required|date',
        'expiry_date' => 'required|date|after:issue_date',
    ]);

    if ($validator->fails()) {
        Log::error('Validation failed: ' . json_encode($validator->errors()));
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Generate the license number
    $licenseNumber = $this->generateLicenseNumber($license);
    Log::info('Generated license number: ' . $licenseNumber);

    // Update the license with the status `licensed_issued`
    $updateData = [
        'issue_date' => $request->issue_date,
        'expiry_date' => $request->expiry_date,
        'license_number' => $licenseNumber,
        'issued_by' => auth()->id(),
        'status' => 'license_issued', // Correct enum value
    ];
    

    Log::info('Update data: ' . json_encode($updateData));

    DB::beginTransaction();
    try {
        DB::enableQueryLog();
        $updated = $this->licensesRepository->update($license->id, $updateData);
        Log::info('SQL Query: ' . json_encode(DB::getQueryLog()));
        Log::info('Update result: ' . ($updated ? 'success' : 'failed'));

        if (!$updated) {
            throw new \Exception('Failed to update license in database.');
        }

        // Fetch the updated license with applicant and license type information
        $license = $this->licensesRepository->getById($license->id, [
            'id', 'license_number', 'issue_date', 'expiry_date'
        ])->load(['applicant', 'licenseType']);

        Log::info('Updated License: ' . json_encode($license));

        // Uncomment if you have a method to generate the license document
        // $licenseDocument = $this->generateLicenseDocument($license);

        DB::commit();
        return redirect()->route('license.licenses.index')->with('success', 'License issued successfully.');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error updating license: ' . $e->getMessage());
        return redirect()->back()->with('error', 'An error occurred while issuing the license: ' . $e->getMessage());
    }
}


private function generateLicenseNumber($license)
{
    // Get the current year
    $year = date('Y');

    // Get the license type code (you might need to add a 'code' column to your license_types table)
    $licenseTypeCode = $license->licenseType->code ?? 'LIC';

    // Get the next sequence number for this year and license type
    $sequence = License::where('license_number', 'like', "{$year}-{$licenseTypeCode}-%")
                       ->max('license_number');
    
    if ($sequence) {
        $sequence = intval(substr($sequence, -4)) + 1;
    } else {
        $sequence = 1;
    }

    // Format the license number
    $licenseNumber = sprintf("%s-%s-%04d", $year, $licenseTypeCode, $sequence);

    Log::info('Generated License Number: ' . $licenseNumber);

    return $licenseNumber;
}

public function showIssueLicenseForm(License $license)
{
    return view('license.license.issue', compact('license'));
}

public function downloadLicense(License $license)
{
    $license->load(['applicant', 'licenseType']);

    Log::info('License Type: ' . $license->licenseType->name);

    $view = $this->getLicenseTemplateView($license->licenseType->name);

    Log::info('View path: ' . $view);

    if (!View::exists($view)) {
        Log::error('View does not exist: ' . $view);
        return redirect()->back()->with('error', 'No template found for this license type.');
    }

    $pdf = PDF::loadView($view, compact('license'));
    return $pdf->download('license-' . $license->license_number . '.pdf');
}

private function getLicenseTemplateView($licenseTypeName)
{
    Log::info('License Type Name: ' . $licenseTypeName);

    // Normalize the string
    $licenseTypeName = strtolower(trim($licenseTypeName));

    $view = match ($licenseTypeName) {
        'export license for seacucumber' => 'license.license.seacucumber',
        'export license for petfish' => 'license.license.aquarium',
        'export license for lobster' => 'license.license.lobster',
        'export license for shark fin' => 'license.license.shark_fin',
        default => 'license.license.default',
    };

    Log::info('Selected View: ' . $view);

    return $view;
}


// Add method to view activity logs
public function activityLog($id)
{
    $license = License::findOrFail($id);
    
    $activities = Activity::where('subject_type', License::class)
        ->where('subject_id', $id)
        ->with('causer')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('license.license.activity-log', compact('license', 'activities'));
}

 /**
     * Review all licenses associated with an applicant.
     *
     * @param int $applicantId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function review($applicantId, $licenseId, Request $request) 
    {
        // dd($request->all());
        // Retrieve the specific license associated with the applicant
        $license = $this->licensesRepository->getById($licenseId);
    
        if (!$license || $license->applicant_id != $applicantId) {
            return redirect()->route('license.applicants.index')->with('error', 'License not found for this applicant.');
        }
    
        // Process the license if its status is 'pending'
        if ($license->status === 'pending') {
            $license->status = 'reviewed';
            $license->updated_by = auth()->id(); // Track the updater
            $license->save();
    
            // Log the review action
            Log::info('License reviewed', [
                'license_id' => $license->id,
                'updated_by' => auth()->id()
            ]);
    
            return redirect()->route('license.licenses.index')
                ->with('success', 'The license has been reviewed successfully.');
        }
    
        return redirect()->route('license.licenses.index')
            ->with('info', 'No pending licenses to review.');
    }
    
     /**
     * Revoke a license.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeLicense(Request $request, License $license)
    {
        try {
            // Log the incoming request to ensure the reason is being sent correctly
            \Log::info('Revoke License Request:', $request->all());
    
            // Validate input
            $validator = Validator::make($request->all(), [
                'revocation_reason' => 'required|string|min:3|max:1000'
            ], [
                'revocation_reason.required' => 'Please provide a reason for revoking the license.',
                'revocation_reason.min' => 'The revocation reason must be at least 3 characters.',
                'revocation_reason.max' => 'The revocation reason cannot exceed 1000 characters.'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Ensure the license is in the correct state
            if ($license->status !== 'license_issued') {
                return response()->json([
                    'message' => 'This license cannot be revoked from its current status.',
                    'errors' => ['status' => ['Only issued licenses can be revoked.']]
                ], 422);
            }
    
            DB::beginTransaction();
    
            // Update license status and details
            $license->update([
                'status' => 'license_revoked',
                'revocation_reason' => $request->revocation_reason,
                'revocation_date' => now(),
                'revoked_by' => auth()->id()
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'License revoked successfully.',
                'download_url' => route('license.licenses.download-revoked', $license->id)
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('License revocation error:', [
                'error' => $e->getMessage(),
                'license_id' => $license->id,
                'user_id' => auth()->id()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while revoking the license. Please try again later.'
            ], 500);
        }
    }
    
    

    
    

    public function downloadRevokedLicense(License $license)
    {
        // Check if the license is revoked
        if ($license->status !== 'license_revoked') {
            return redirect()->back()->withErrors('This license is not revoked and cannot be downloaded.');
        }

        // Path to the revoked license file
        $filePath = 'revoked_licenses/' . $license->id . '.pdf';

        // Check if the file exists
        if (!Storage::exists($filePath)) {
            return redirect()->back()->withErrors('The revoked license file does not exist.');
        }

        // Return the file as a download response
        return Storage::download($filePath, 'revoked_license_' . $license->id . '.pdf');
    }
    

// Add this new private method to get specific revoke text based on license type
private function getRevokeText($licenseTypeName)
{
    return match (strtolower(trim($licenseTypeName))) {
        'export license for seacucumber' => 'SEACUCUMBER LICENSE REVOKED',
        'export license for petfish' => 'PETFISH LICENSE REVOKED',
        'export license for lobster' => 'LOBSTER LICENSE REVOKED',
        'export license for shark fin' => 'SHARK FIN LICENSE REVOKED',
        default => 'LICENSE REVOKED',
    };
}

}