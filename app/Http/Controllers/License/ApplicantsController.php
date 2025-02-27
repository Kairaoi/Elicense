<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\ApplicantsRepository;
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
use App\Mail\ApplicantXMLMail;



class ApplicantsController extends Controller
{
    protected $applicantsRepository;
    protected $licensesRepository;
    
    /**
     * ApplicantsController constructor.
     *
     */
    public function __construct(ApplicantsRepository $applicantsRepository, LicensesRepository $licensesRepository)
    {
        $this->applicantsRepository = $applicantsRepository;
        $this->licensesRepository = $licensesRepository;
        $this->middleware('throttle:3,1')->only('store');
    }

    /**
     * Get DataTable of license applicants.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $applicants = Applicant::with('licenses');

        return DataTables::of($applicants)
            ->addColumn('has_pending_license', function ($applicant) {
                return $applicant->hasPendingLicenses();
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function getPendingCount()
    {
        $pendingCount = License::where('status', 'pending')->count();
        return response()->json(['pendingCount' => $pendingCount]);
    }

    /**
     * Display a listing of license applicants.
     *
     * @return Response
     */
    public function index()
{
    $pendingCount = \DB::table('licenses')
        ->where('status', 'pending')
        ->count();

    return view('license.applicant.index', compact('pendingCount'));
}


    /**
     * Show the form for creating a new license applicant.
     *
     * @return Response
     */
    public function create()
    {
        return view('license.applicant.create');
    }

    /**
     * Store a newly created license applicant in storage.
     *
     * @param Request $request
     * @return Response
     */

     public function store(StoreApplicantRequest $request)
     {
         try {
             $data = $request->validated();
             $data['created_by'] = auth()->check() ? auth()->id() : 0;
             $data['status'] = 'pending';
             $data['submitted_at'] = now();
             $data['ip_address'] = $request->ip();
     
             $application = $this->applicantsRepository->create($data);
     
             activity()
                 ->performedOn($application)
                 ->causedBy(auth()->user())
                 ->withProperties([
                     'ip_address' => $request->ip(),
                     'user_agent' => $request->userAgent()
                 ])
                 ->log('created');
     
             $password = Str::random(8);
             $user = User::create([
                 'name' => $data['first_name'] . ' ' . $data['last_name'],
                 'email' => $data['email'],
                 'password' => Hash::make($password),
                 'applicant_id' => $application->id
             ]);
     
             // Create applicant role if it doesn't exist
             $applicantRole = Role::firstOrCreate(['name' => 'applicant']);
     
             // Create view-applicants permission if it doesn't exist
             $viewApplicantsPermission = Permission::firstOrCreate(['name' => 'view.applicants']);
     
             // Assign permission to role if not already assigned
             if (!$applicantRole->hasPermissionTo('view.applicants')) {
                 $applicantRole->givePermissionTo($viewApplicantsPermission);
             }
     
             // Assign role to user
             $user->assignRole($applicantRole);
     
             \Log::info('Generated password for applicant', [
                 'user_id' => $user->id,
                 'applicant_id' => $application->id,
                 'password' => $password,
             ]);
     
             \Log::info('User account created for applicant', [
                 'user_id' => $user->id,
                 'applicant_id' => $application->id,
             ]);
     
             session(['temp_applicant_id' => $application->id]);
     
             // Create XML structure
             $xml = new \SimpleXMLElement('<applicant/>');
             $xml->addChild('first_name', $data['first_name']);
             $xml->addChild('last_name', $data['last_name']);
             $xml->addChild('username', $data['email']);
             $xml->addChild('password', $password);
             $xml->addChild('applicant_id', $application->id);
             $xml->addChild('status', 'pending');
             $xml->addChild('submitted_at', $data['submitted_at']);
     
             // Format XML
             $dom = new \DOMDocument('1.0', 'UTF-8');
             $dom->preserveWhiteSpace = false;
             $dom->formatOutput = true;
             $domXml = dom_import_simplexml($xml);
             $domXml = $dom->importNode($domXml, true);
             $dom->appendChild($domXml);
     
             // Add a comment at the top
             $comment = $dom->createComment(' Please do not share this file with anyone. ');
             $dom->insertBefore($comment, $dom->documentElement);
     
             // Save XML as string
             $xmlString = $dom->saveXML();
     
             // Save XML to a temporary file
             $xmlFilePath = storage_path('app/temp_applicant_' . $application->id . '.xml');
             file_put_contents($xmlFilePath, $xmlString);
     
             // Send XML file as email attachment
             Mail::to($user->email)->send(new ApplicantXMLMail($xmlFilePath));
     
             // Redirect to license creation page
             return redirect()->route('license.licenses.create')
                 ->with('success', 'Application submitted successfully.');
     
         } catch (\Exception $e) {
             \Log::error('Application submission failed', [
                 'error' => $e->getMessage(),
                 'trace' => $e->getTraceAsString(),
             ]);
     
             return redirect()->back()
                 ->withInput()
                 ->with('error', 'There was a problem submitting your application. Please try again.');
         }
     }
     

    /**
     * Display the specified license applicant.
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
        return view('license.applicant.show', compact(
            'applicant',
            'licenses', // Now passing multiple licenses
            'licenseTypes',
            'species',
            'requestedQuotas'
        ));
    }

    /**
     * Show the form for editing the specified license applicant.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $applicant = $this->applicantsRepository->getById($id);

        if (!$applicant) {
            return redirect()->route('license.applicant.index')->with('error', 'Applicant not found.');
        }

        return view('license.applicant.edit', compact('applicant'));
    }

    /**
     * Update the specified license applicant in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateApplicantRequest $request, $id) 
{     
    \Log::info('Update method called', [
        'id' => $id,
        'request_data' => $request->all(),
        'validator_data' => $request->validated()
    ]);

    try {
        // Log the applicant retrieval attempt
        \Log::info('Fetching applicant', ['id' => $id]);
        
        $applicant = $this->applicantsRepository->getById($id);
        
        if (!$applicant) {
            \Log::warning('Applicant not found', ['id' => $id]);
            return redirect()
                ->route('license.applicants.index')
                ->with('error', 'Applicant not found.');
        }

        \Log::info('Found applicant', ['applicant' => $applicant->toArray()]);
        
        // Store old values before update
        $oldValues = $applicant->getAttributes();
        
        // Get validated data from request object
        $data = $request->validated();
        
        \Log::info('Validated data', ['data' => $data]);
        
        // Add updater ID
        $data['updated_by'] = auth()->id();

        // Log the update attempt
        \Log::info('Attempting to update applicant', [
            'id' => $id,
            'data' => $data
        ]);
        
        // Update applicant
        $updated = $this->applicantsRepository->update($id, $data);
        
        if (!$updated) {
            \Log::error('Failed to update applicant in repository', ['id' => $id]);
            return redirect()
                ->route('license.applicants.index')
                ->with('error', 'Failed to update applicant.');
        }

        \Log::info('Applicant updated successfully', ['id' => $id]);
        
        // Log the update with old and new values
        activity()
            ->performedOn($applicant)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldValues,
                'new' => $data,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('updated');

        \Log::info('Activity logged successfully');
        
        // Check for associated license
        $license = $applicant->licenses()->first();
        
        if ($license) {
            \Log::info('Associated license found', ['license_id' => $license->id]);
            return redirect()
                ->route('license.licenses.edit', $license->id)
                ->with('success', 'Applicant updated successfully. You can now edit the associated license.');
        }

        \Log::info('Update completed successfully, redirecting to index');
        
        return redirect()
        ->route('applicant.applicants.index')
        ->with('error', 'Applicant not found.');
    
            
    } catch (\Exception $e) {
        \Log::error('Exception caught during applicant update', [
            'id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
        
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'There was a problem updating the applicant: ' . $e->getMessage());
    }
}
    /**
     * Remove the specified license applicant from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $applicant = $this->applicantsRepository->getById($id);
            
            if ($applicant) {
                activity()
                    ->performedOn($applicant)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'deleted_record' => $applicant->toArray(),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent()
                    ])
                    ->log('deleted');

                $this->applicantsRepository->deleteById($id);
            }

            return response()->json(['message' => 'Applicant deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete applicant'], 500);
        }
    }

    // Add method to view activity logs
    public function activityLog($id)
    {
        $applicant = $this->applicantsRepository->getById($id);
        
        if (!$applicant) {
            return redirect()->route('license.applicants.index')
                ->with('error', 'Applicant not found.');
        }

        $activities = Activity::where('subject_type', get_class($applicant))
            ->where('subject_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('license.applicant.activity-log', compact('applicant', 'activities'));
    }

    /**
 * Update the specified license status to reviewed.
 *
 * @param int $id
 * @return Response
 */
public function review($id)
{
    // Find the license associated with the applicant
    $license = $this->licensesRepository->getById($id);

    if (!$license) {
        return redirect()->route('license.applicants.index')->with('error', 'License not found.');
    }

    // Check if the license status is 'pending'
    if ($license->status === 'pending') {
        // Update the license status
        $license->status = 'reviewed';
        $license->updated_by = auth()->id(); // Track who updated it
        $license->save(); // Save the changes

        // Optional: Log the review action
        Log::info('License reviewed', ['license_id' => $license->id, 'updated_by' => auth()->id()]);

        return redirect()->route('license.licenses.index')->with('success', 'License status updated to reviewed successfully.');
    }

    // If the status is not 'pending', show a message
    return redirect()->route('license.licenses.index')->with('info', 'License is already marked as reviewed or cannot be reviewed.');
}

public function downloadPDF($id)
{
    // Find the applicant
    $applicant = Applicant::findOrFail($id);
    
    // Get licenses and related data
    $licenses = License::where('applicant_id', $id)
        ->with(['licenseType'])
        ->get();
    
    $licenseTypes = LicenseType::all();
    $species = Species::all();
    
    // Get quotas for each license
    $requestedQuotas = [];
    foreach ($licenses as $license) {
        $requestedQuotas[$license->id] = LicenseItem::where('license_id', $license->id)
            ->with('species')
            ->get();
    }
    
    // Generate PDF
    $pdf = PDF::loadView('license.applicant.pdf', compact(
        'applicant',
        'licenses',
        'licenseTypes',
        'species',
        'requestedQuotas'
    ));
    
    // Download PDF with custom filename
    return $pdf->download('applicant-'.$applicant->id.'-details.pdf');
}

public function getApplicantsData(Request $request)
    {
        $applicants = Applicant::query();

        // Count pending applications
        $pendingCount = License::where('status', 'pending')->count();

        return DataTables::of($applicants)
            ->addColumn('full_name', function ($applicant) {
                return $applicant->first_name . ' ' . $applicant->last_name;
            })
            ->addColumn('actions', function ($applicant) {
                return view('partials.applicant_actions', compact('applicant'))->render();
            })
            ->with(['pendingCount' => $pendingCount])
            ->make(true);
    }

    
   


}
