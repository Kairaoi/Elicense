<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\ApplicantsDetailsRepository;
use App\Repositories\License\LicensesRepository; 
use Illuminate\Http\Request;
use App\Http\Requests\License\StoreApplicantRequest;
use App\Http\Requests\License\UpdateApplicantRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Models\License\License;
use App\Models\License\LicenseType;
use App\Models\License\Applicant;
use App\Models\License\Species;
use App\Models\License\LicenseItem;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; 
use Illuminate\Support\Facades\Auth;

use App\Models\License\SpeciesIslandQuota;

use App\Repositories\License\LicenseTypesRepository;
use App\Repositories\License\ApplicantsRepository;
use App\Repositories\License\SpeciesRepository; 
use App\Repositories\Reference\IslandsRepository;
use App\Repositories\License\SpeciesIslandQuotaRepository; 

use Illuminate\Support\Facades\DB;


class ApplicantsDetailsController extends Controller
{
    protected $applicantsDetailsRepository;
    protected $licensesRepository;
    protected $applicantsRepository;
    protected $licenseTypesRepository;
    protected $speciesRepository;
    protected $islandsRepository;
    protected $speciesIslandQuotaRepository;
    
    
    /**
     * ApplicantsController constructor.
     *
     */
    public function __construct(ApplicantsDetailsRepository $applicantsDetailsRepository, SpeciesIslandQuotaRepository $speciesIslandQuotaRepository,IslandsRepository $islandsRepository,SpeciesRepository $speciesRepository,LicensesRepository $licensesRepository,ApplicantsRepository $applicantsRepository, LicenseTypesRepository $licenseTypesRepository)
    {
        $this->applicantsDetailsRepository = $applicantsDetailsRepository;
         $this->middleware('throttle:3,1')->only('store');

         $this->licensesRepository = $licensesRepository;
        $this->applicantsRepository = $applicantsRepository;
        $this->licenseTypesRepository = $licenseTypesRepository;
        $this->speciesRepository = $speciesRepository;
        $this->islandsRepository = $islandsRepository;
        $this->speciesIslandQuotaRepository = $speciesIslandQuotaRepository;
    }

    /**
     * Get DataTable of license applicants.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->applicantsDetailsRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function index()
    {
        return view('license.applicantdetails.index');
    }

    public function create()
{
    // Get only the authenticated user's applicant record
    $authenticatedApplicantId = Auth::user()->applicant->id ?? null;
    
    if (!$authenticatedApplicantId) {
        return redirect()->back()->with('error', 'No applicant profile found for your account.');
    }
    
    // Instead of fetching all applicants, create an array with just the authenticated one
    $applicant = Applicant::find($authenticatedApplicantId);
    $applicants = [$applicant->id => $applicant->first_name . ' ' . $applicant->last_name];
    
    // Fetch license types and islands as before
    $licenseTypes = $this->licenseTypesRepository->pluck();
    $islands = $this->islandsRepository->pluck();

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
    return view('license.applicantdetails.create', compact(
        'applicants',
        'licenseTypes',
        'speciesByLicenseType',
        'islands',
        'availableQuotas'
    ));
}
    
    public function store(Request $request)
{
    // dd($request->all());
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
            return redirect()->route('applicantdetails.applicantdetails.index')
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


  

}
