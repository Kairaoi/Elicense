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
use Illuminate\Support\Facades\Storage;

// use Barryvdh\DomPDF\Facade\Pdf;




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
    public function getDataTables(Request $request) {
        $search = $request->input('search.value', '');
        $query = License::join('license_types', 'licenses.license_type_id', '=', 'license_types.id')
                        ->join('applicants', 'licenses.applicant_id', '=', 'applicants.id')
                        ->select(
                            'licenses.id',
                            'licenses.issue_date',
                            'licenses.expiry_date',
                            'licenses.status',
                            'licenses.total_fee',
                            'license_types.name as license_type_name',
                            'applicants.first_name',
                            'applicants.last_name',
                            'applicants.company_name',
                            DB::raw("CASE 
                                WHEN applicants.company_name IS NOT NULL AND applicants.company_name != '' 
                                THEN CONCAT(applicants.first_name, ' ', applicants.last_name, ' (', applicants.company_name, ')') 
                                ELSE CONCAT(applicants.first_name, ' ', applicants.last_name) 
                            END as applicant_name")
                        );
    
        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereRaw("LOWER(CONCAT(applicants.first_name, ' ', applicants.last_name)) LIKE ?", ['%' . strtolower($search) . '%'])
                  ->orWhere('license_types.name', 'LIKE', '%' . strtolower($search) . '%')
                  ->orWhere('applicants.company_name', 'LIKE', '%' . strtolower($search) . '%');
            });
        }
    
        // Apply filters
        if ($request->filled('applicant_name')) {
            $query->whereRaw("LOWER(CONCAT(applicants.first_name, ' ', applicants.last_name)) LIKE ?", ['%' . strtolower($request->input('applicant_name')) . '%']);
        }
    
        if ($request->filled('company_name')) {
            $query->whereRaw("LOWER(applicants.company_name) LIKE ?", ['%' . strtolower($request->input('company_name')) . '%']);
        }
    
        if ($request->filled('license_type')) {
            $query->where('license_types.name', $request->input('license_type'));
        }
    
        if ($request->filled('issue_date')) {
            $query->whereDate('issue_date', $request->input('issue_date'));
        }
    
        if ($request->filled('expiry_date')) {
            $query->whereDate('expiry_date', $request->input('expiry_date'));
        }
    
        if ($request->filled('status')) {
            $query->where('licenses.status', $request->input('status'));
        }
        
        // Check if applicant filter is applied from the blade form
        if ($request->filled('applicant')) {
            $query->where(function($q) use ($request) {
                $search = strtolower($request->input('applicant'));
                $q->whereRaw("LOWER(CONCAT(applicants.first_name, ' ', applicants.last_name)) LIKE ?", ['%' . $search . '%'])
                  ->orWhere('applicants.company_name', 'LIKE', '%' . $search . '%');
            });
        }
    
        return DataTables::of($query)
            ->rawColumns(['applicant_name'])
            ->make(true);
    }




    /**
     * Display a listing of licenses.
     *
     * @return Response
     */
    public function index()
{
    $licenses = License::all();
    $licenseTypes = LicenseType::select('name')->distinct()->get();

    return view('license.license.index', compact('licenses', 'licenseTypes'));
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
        // Fetch the existing license
        $license = $this->licensesRepository->getById($id);
    
        // Null checks with fallback to empty collections
        $applicants = $this->applicantsRepository->pluck() ?? collect();
        $licenseTypes = $this->licenseTypesRepository->pluck() ?? collect();
        $islands = $this->islandsRepository->pluck() ?? collect();
    
        // Additional null checks
        if (!$license) {
            abort(404, 'License not found');
        }
    
        // Initialize arrays to store species by license type and available quotas
        $speciesByLicenseType = [];
        $availableQuotas = [];
        
        // Safely get selected species
        $selectedSpecies = $license->species ? $license->species->pluck('id')->toArray() : [];
    
        // Additional check to prevent errors if license types are empty
        if ($licenseTypes->isEmpty()) {
            \Log::error('No license types found in edit method');
            return back()->with('error', 'No license types available');
        }
    
        // Loop through each license type to get species and available quotas
        foreach ($licenseTypes as $id => $name) {
            // Fetch species for each license type
            $speciesForLicenseType = $this->speciesRepository->getByLicenseType($id) ?? collect();
            
            if ($speciesForLicenseType->isEmpty()) {
                \Log::info("No species found for license type: $id");
                continue;
            }
    
            $speciesByLicenseType[$id] = $speciesForLicenseType;
    
            // Loop through each species for the current license type
            foreach ($speciesForLicenseType as $species) {
                foreach ($islands as $islandId => $islandName) {
                    // Fetch quota for the species on the island, prioritizing the most recent year
                    $quota = SpeciesIslandQuota::where('species_id', $species->id)
                        ->where('island_id', $islandId)
                        ->whereNotNull('remaining_quota')
                        ->orderBy('year', 'desc')
                        ->first();
    
                    // Store the available quota for the species on the island
                    $availableQuotas[$species->id][$islandId] = $quota ? $quota->remaining_quota : 0;
                }
            }
        }

        $licenseItems = $license->licenseItems()->get();
$existingQuotas = [];

foreach ($licenseItems as $item) {
    // Store the requested quota for each species and island
    $existingQuotas[$item->species_id][$item->island_id] = $item->requested_quota;
}
    
        // Optional: Log the available quotas and species by license type for debugging
        \Log::info('Available Quotas:', $availableQuotas);
        \Log::info('Species by License Type:', $speciesByLicenseType);
        $selectedIslands = $license->islands->pluck('id')->toArray();
        // Return the view with the necessary data
        return view('license.license.edit', compact(
            'license',
            'applicants',
            'licenseTypes',
            'speciesByLicenseType',
            'islands',
            'availableQuotas',
            'selectedIslands',
            'selectedSpecies',
            'licenseItems', 'existingQuotas'
        ));
    }


    /**
     * Update the specified license in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
{
    $license = License::findOrFail($id);
    
    // Validate incoming request data
    $data = $request->validate([
        'applicant_id' => 'required|exists:applicants,id',
        'license_type_id' => 'required|exists:license_types,id',
        'islands' => 'required|array',
        'islands.*' => 'exists:islands,id',
        'species_quota' => 'required|array',
    ]);

    // Update basic license information
    $license->applicant_id = $data['applicant_id'];
    $license->license_type_id = $data['license_type_id'];
    $license->updated_by = Auth::id() ?? null;

    // Sync islands
    $license->islands()->sync($data['islands']);

    // Initialize total fee
    $totalLicenseFee = 0;

    // Delete existing license items
    $license->licenseItems()->delete();

    // Process species quotas
    foreach ($data['species_quota'] as $speciesId => $islandQuotas) {
        $species = Species::find($speciesId);
        
        foreach ($islandQuotas as $islandId => $quantity) {
            if (empty($quantity)) continue;
            
            // Calculate total price for the species
            $speciesFee = $quantity * $species->unit_price;
            $totalLicenseFee += $speciesFee;

            // Create new license items
            $license->licenseItems()->create([
                'species_id' => $speciesId,
                'island_id' => $islandId,
                'requested_quota' => $quantity,
                'unit_price' => $species->unit_price,
                'total_price' => $speciesFee,
                'created_by' => Auth::id(),
                'creator_type' => 'user',
            ]);
        }
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
        'total_fee' => $license->total_fee,
        'vat_amount' => $license->vat_amount,
        'total_with_vat' => $license->total_amount_with_vat
    ]);

    // Check if the user is authenticated
    if (Auth::check()) {
        // For authenticated users, redirect to the index page
        return redirect()->route('license.licenses.index')
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

   /**
 * Get currency details based on license type
 * 
 * @param string $licenseTypeName
 * @return array
 */
private function getCurrencyDetails($licenseTypeName)
{
    // Normalize the license type name to handle case sensitivity
    $licenseTypeName = trim($licenseTypeName);
    
    switch ($licenseTypeName) {
        case 'Export License for Seacucumber':
            return [
                'symbol' => 'AUD',
                'name' => 'Australian',
                'full_name' => 'Australian Dollars (AUD)'
            ];
        case 'Export License for Petfish':
            return [
                'symbol' => 'USD',
                'name' => 'US',
                'full_name' => 'US Dollars (USD)'
            ];
        default:
            return [
                'symbol' => 'USD',
                'name' => 'US',
                'full_name' => 'US Dollars (USD)'
            ];
    }
}

/**
 * Show invoice
 *
 * @param License $license
 * @return Response
 */
public function showInvoice(License $license)
{
    // Load the license type relationship if not already loaded
    if (!$license->relationLoaded('licenseType')) {
        $license->load('licenseType');
    }
    
    $currencyDetails = $this->getCurrencyDetails($license->licenseType->name);
    
    // Add logging to debug currency details
    Log::info('License Type: ' . $license->licenseType->name);
    Log::info('Currency Details: ' . json_encode($currencyDetails));
    
    return view('license.license.invoice', [
        'license' => $license, 
        'isPdfDownload' => false,
        'currencyDetails' => $currencyDetails
    ]);
}

/**
 * Download invoice
 *
 * @param int $id
 * @return Response
 */
public function downloadInvoice($id)
{
    $license = License::with(['licenseType', 'applicant', 'licenseItems.species'])->findOrFail($id);
    
    // Ensure we have the correct currency details
    $currencyDetails = $this->getCurrencyDetails($license->licenseType->name);
    
    // Debug file path
    $imagePath = public_path('images/logos.png');
    Log::info("Checking image path: " . $imagePath);

    // Add image handling
    $logoBase64 = '';
    if (file_exists($imagePath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath));
        Log::info("Image successfully loaded: " . substr($logoBase64, 0, 50) . '...'); // Debug only first 50 chars
    } else {
        Log::error("LOGO NOT FOUND: " . $imagePath);
    }

    $pdf = PDF::loadView('license.license.invoice', [
        'license' => $license, 
        'isPdfDownload' => true,
        'currencyDetails' => $currencyDetails,
        'logoBase64' => $logoBase64
    ]);

    // Send email with PDF attachment
    Mail::to($license->applicant->email)->send(new InvoiceEmail($license, $pdf));
    
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

    // Add image handling
    $imagePath = public_path('images/coat_of_arms.png');
    $imageData = '';
    if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
    }
    
    $pdf = PDF::loadView($view, [
        'license' => $license,
        'imageData' => $imageData
    ]);
    
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
    public function revokeLicense(Request $request, $id)
{
    \Log::info('Revoke License Request Data:', $request->all()); // Log request

    $validator = Validator::make($request->all(), [
        'revocation_reason' => 'required|string|min:3|max:1000'
    ], [
        'revocation_reason.required' => 'Please provide a reason for revoking this license.',
    ]);

    if ($validator->fails()) {
        \Log::warning('Revocation validation failed:', $validator->errors()->toArray()); // Log validation errors
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $license = License::findOrFail($id);

    if ($license->status !== 'license_issued') {
        return response()->json([
            'success' => false,
            'message' => 'This license cannot be revoked from its current status.',
        ], 422);
    }

    $license->update([
        'status' => 'license_revoked',
        'revocation_reason' => trim($request->revocation_reason),
        'revocation_date' => now(),
        'revoked_by' => auth()->id()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'License has been revoked successfully.',
        'download_url' => route('license.licenses.download-revoked', $license->id)
    ]);
}


    
    

public function downloadRevokedLicense(License $license)
{
    // Detailed logging
    \Log::info('Attempting to download revoked license', [
        'license_id' => $license->id,
        'license_status' => $license->status
    ]);

    // Check license status
    if ($license->status !== 'license_revoked') {
        \Log::warning('Attempted to download non-revoked license', [
            'license_id' => $license->id,
            'current_status' => $license->status
        ]);
        return redirect()->back()->withErrors('This license is not revoked and cannot be downloaded.');
    }

    // Construct file path
    $filePath = storage_path('app/revoked_licenses/' . $license->id . '.pdf');

    // Comprehensive file checks
    if (!file_exists($filePath)) {
        // Attempt to regenerate the PDF if it's missing
        try {
            $this->generateRevokedLicensePdf($license);
        } catch (\Exception $e) {
            \Log::error('Failed to regenerate revoked license PDF', [
                'license_id' => $license->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->withErrors('Unable to generate the revoked license file.');
        }
    }

    // Verify file exists after potential regeneration
    if (!file_exists($filePath)) {
        \Log::error('Revoked license file still not found after regeneration', [
            'license_id' => $license->id,
            'file_path' => $filePath
        ]);
        return redirect()->back()->withErrors('The revoked license file does not exist.');
    }

    // Attempt to download
    try {
        return response()->download($filePath, 'revoked_license_' . $license->id . '.pdf');
    } catch (\Exception $e) {
        \Log::error('Error downloading revoked license', [
            'license_id' => $license->id,
            'error_message' => $e->getMessage()
        ]);
        return redirect()->back()->withErrors('An error occurred while downloading the license.');
    }
}
protected function generateRevokedLicensePdf(License $license)
{
    // Ensure the directory exists
    $directory = storage_path('app/revoked_licenses');
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }

    // Determine the appropriate view based on license type
    $viewPath = $this->getLicenseTemplatePath($license->licenseType->name);

    // Get specific revoke text based on license type
    $revokeText = $this->getRevokeText($license->licenseType->name);

    // Generate PDF
    $pdf = PDF::loadView($viewPath, [
        'license' => $license,
        'isRevoked' => true,  // Pass a flag to show revocation watermark
        'revokeText' => $revokeText  // Pass custom revoke text
    ]);

    // Save PDF
    $filePath = $directory . '/' . $license->id . '.pdf';
    $pdf->save($filePath);

    \Log::info('Revoked license PDF generated', [
        'license_id' => $license->id,
        'file_path' => $filePath,
        'revoke_text' => $revokeText,
        'template' => $viewPath
    ]);

    return $filePath;
}

// Method to get appropriate template path
private function getLicenseTemplatePath($licenseTypeName)
{
    $licenseTypeMapping = [
        'export license for seacucumber' => 'license.license.secucumber',
        'export license for petfish' => 'license.license.aquarium',
        'export license for lobster' => 'license.license.lobster',
        'export license for shark fin' => 'license.license.sharkfin',
    ];

    return $licenseTypeMapping[strtolower(trim($licenseTypeName))] 
           ?? 'license.license.default'; // Default to sea cucumber template
}

// Method to get specific revoke text
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