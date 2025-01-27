<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Models\License\HarvesterLicense;  // Make sure this is imported
use App\Repositories\License\HarvesterLicensesRepository;
use App\Repositories\License\HarvesterApplicantsRepository;
use App\Repositories\Reference\IslandsRepository;
use App\Repositories\License\SpeciesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use PDF;  // Make sure you have the PDF facade imported
use DataTables;
use DB;
class HarvesterLicensesController extends Controller
{
    protected $harvesterLicensesRepository;
    protected $harvesterApplicantsRepository;
    protected $islandsRepository;
    protected $speciesRepository;

    /**
     * HarvesterLicensesController constructor.
     */
    public function __construct(SpeciesRepository $speciesRepository,
        HarvesterLicensesRepository $harvesterLicensesRepository,
        HarvesterApplicantsRepository $harvesterApplicantsRepository,
        IslandsRepository $islandsRepository
    ) {
        $this->harvesterLicensesRepository = $harvesterLicensesRepository;
        $this->harvesterApplicantsRepository = $harvesterApplicantsRepository;
        $this->islandsRepository = $islandsRepository;
        $this->speciesRepository = $speciesRepository;
    }

    /**
     * Get DataTable of harvester licenses.
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->harvesterLicensesRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of harvester licenses.
     */
    public function index()
    {
        return view('license.harvester_license.index');
    }

    /**
     * Show the form for creating a new harvester license.
     */
    public function create() {
        $applicants = $this->harvesterApplicantsRepository->pluck()->map(function($item) {
            return (object) $item;
        });
        $islands = $this->islandsRepository->pluck();
        
        // Get license types
        $licenseTypes = DB::table('license_types')->pluck('name', 'id');
        
        // Initialize array to store species by license type
        $speciesByLicenseType = [];
        
        // Fetch species for each license type
        foreach ($licenseTypes as $id => $name) {
            $speciesByLicenseType[$id] = $this->speciesRepository->getByLicenseType($id);
        }
    
        return view('license.harvester_license.create', compact(
            'islands', 
            'applicants', 
            'licenseTypes',
            'speciesByLicenseType'
        ));
    }
    

    public function store(Request $request)
    {
        Log::info('Store method called.');
    
        try {
            // Validate request data
            $data = $request->validate([
                'harvester_applicant_id' => 'required|exists:harvester_applicants,id',
                'island_id' => 'required|exists:islands,id',
                'license_type_id' => 'required|exists:license_types,id',
                'species' => 'required|array|min:1',
                'species.*' => 'required|exists:species,id',
                'issue_date' => 'nullable|date',
                'expiry_date' => 'nullable|date|after:issue_date',
                'payment_receipt_no' => 'required|string|max:255|unique:harvester_licenses,payment_receipt_no',
                'group_members' => 'required_if:is_group,true|array|max:5',
                'group_members.*.name' => 'required_if:is_group,true|string|max:255',
                'group_members.*.national_id' => 'required_if:is_group,true|string|max:50|distinct',
            ]);
    
            Log::info('Received request data:', ['request_data' => $request->all()]);
    
            if ($data['harvester_applicant_id'] == '0') {
                throw new \Exception('The selected harvester applicant ID is invalid.');
            }
    
            // Get applicant details
            $applicant = $this->harvesterApplicantsRepository->getById($data['harvester_applicant_id']);
    
            if (!$applicant) {
                throw new \Exception('Applicant not found');
            }
    
            if (!empty($data['expiry_date']) && empty($data['issue_date'])) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors('Error: Issue date is required when expiry date is provided.');
            }
    
            $data['fee'] = $applicant->is_group ? 500.00 : 25.00;
            $data['created_by'] = auth()->id();
    
            DB::beginTransaction();
    
            $licenseNumber = HarvesterLicense::generateLicenseNumber($data['license_type_id']);
    
            $license = $this->harvesterLicensesRepository->create([
                'license_number' => $licenseNumber,
                'harvester_applicant_id' => $data['harvester_applicant_id'],
                'island_id' => $data['island_id'],
                'license_type_id' => $data['license_type_id'],
                'issue_date' => $data['issue_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'payment_receipt_no' => $data['payment_receipt_no'],
                'fee' => $data['fee'],
                'created_by' => $data['created_by'],
            ]);
    
            foreach ($data['species'] as $speciesId) {
                DB::table('harvester_license_species')->insert([
                    'harvester_license_id' => $license->id,
                    'species_id' => $speciesId,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
    
            if ($applicant->is_group && !empty($data['group_members'])) {
                $this->harvesterLicensesRepository->saveGroupMembers($license->id, $data['group_members']);
            }
    
            DB::commit();
    
            Log::info('License created:', [
                'license_id' => $license->id,
                'license_number' => $licenseNumber,
                'is_group' => $applicant->is_group,
                'fee' => $data['fee'],
                'species_count' => count($data['species']),
            ]);
    
            return redirect()
                ->route('harvester.licenses.index')
                ->with('success', 'Harvester license created successfully. License Number: ' . $licenseNumber . ', Fee: $' . number_format($data['fee'], 2));
        } catch (\Exception $e) {
            DB::rollBack();
    
            Log::error('Error creating license:', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
            ]);
    
            return redirect()
                ->back()
                ->withInput()
                ->withErrors('Error: ' . $e->getMessage());
        }
    }
    

public function edit($id)
{
    // Retrieve the existing license data
    $harvesterLicense = $this->harvesterLicensesRepository->getById($id)->load('groupMembers'); // Eager load group members
    
    if (!$harvesterLicense) {
        abort(404, 'Harvester License not found.');
    }

    // Fetch applicants
    $applicants = $this->harvesterApplicantsRepository->pluck()->map(function ($item) {
        return (object) $item;
    });

    // Fetch islands
    $islands = $this->islandsRepository->pluck();

    // Get license types
    $licenseTypes = DB::table('license_types')->pluck('name', 'id');

    // Initialize array to store species by license type
    $speciesByLicenseType = [];

    // Fetch species for each license type
    foreach ($licenseTypes as $id => $name) {
        $speciesByLicenseType[$id] = $this->speciesRepository->getByLicenseType($id);
    }

    // Pass the harvester license data along with other dropdowns to the view
    return view('license.harvester_license.edit', compact(
        'harvesterLicense',
        'islands',
        'applicants',
        'licenseTypes',
        'speciesByLicenseType'
    ));
}


public function update(Request $request, $id)
{
    Log::info('Update method called.');

    try {
        // Validate request data
        $data = $request->validate([
            'harvester_applicant_id' => 'required|exists:harvester_applicants,id',
            'island_id' => 'required|exists:islands,id',
            'license_type_id' => 'required|exists:license_types,id',
            'species' => 'required|array|min:1',
            'species.*' => 'required|exists:species,id',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'payment_receipt_no' => 'required|string|max:255|unique:harvester_licenses,payment_receipt_no,' . $id,
            'group_members' => 'required_if:is_group,true|array|max:5',
            'group_members.*.name' => 'required_if:is_group,true|string|max:255',
            'group_members.*.national_id' => 'required_if:is_group,true|string|max:50|distinct',
        ]);

        Log::info('Received request data for update:', ['request_data' => $request->all()]);

        if ($data['harvester_applicant_id'] == '0') {
            throw new \Exception('The selected harvester applicant ID is invalid.');
        }

        // Get the license to be updated
        $license = $this->harvesterLicensesRepository->getById($id);

        if (!$license) {
            throw new \Exception('License not found.');
        }

        // Get applicant details
        $applicant = $this->harvesterApplicantsRepository->getById($data['harvester_applicant_id']);

        if (!$applicant) {
            throw new \Exception('Applicant not found.');
        }

        if (!empty($data['expiry_date']) && empty($data['issue_date'])) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors('Error: Issue date is required when expiry date is provided.');
        }

        $data['fee'] = $applicant->is_group ? 500.00 : 25.00;
        $data['updated_by'] = auth()->id();

        DB::beginTransaction();

        // Update license details
        $this->harvesterLicensesRepository->update($id, [
            'harvester_applicant_id' => $data['harvester_applicant_id'],
            'island_id' => $data['island_id'],
            'license_type_id' => $data['license_type_id'],
            'issue_date' => $data['issue_date'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'payment_receipt_no' => $data['payment_receipt_no'],
            'fee' => $data['fee'],
            'updated_by' => $data['updated_by'],
        ]);

        // Update species associated with the license
        DB::table('harvester_license_species')->where('harvester_license_id', $id)->delete();

        foreach ($data['species'] as $speciesId) {
            DB::table('harvester_license_species')->insert([
                'harvester_license_id' => $id,
                'species_id' => $speciesId,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($applicant->is_group && !empty($data['group_members'])) {
            $this->harvesterLicensesRepository->updateGroupMembers($id, $data['group_members']);
        }

        DB::commit();

        Log::info('License updated successfully:', [
            'license_id' => $id,
            'is_group' => $applicant->is_group,
            'fee' => $data['fee'],
            'species_count' => count($data['species']),
        ]);

        return redirect()
            ->route('harvester.licenses.index')
            ->with('success', 'Harvester license updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Error updating license:', [
            'error' => $e->getMessage(),
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->withErrors('Error: ' . $e->getMessage());
    }
}

public function show($id)
{
    try {
        Log::info('Attempting to show harvester license with ID: ' . $id);
        
        // Get the license with related data
        $harvesterLicense = $this->harvesterLicensesRepository->getById($id);
        
        // First debug point
        Log::info('Initial license data:', ['license' => $harvesterLicense]);
        
        if (!$harvesterLicense) {
            Log::warning('Harvester License not found with ID: ' . $id);
            throw new \Exception('Harvester License not found.');
        }

        // Load relationships
        $harvesterLicense->load([
            'applicant',
            'island',
            'licenseType',
            'species',
            'groupMembers'
        ]);
        
        // Second debug point
        Log::info('License data with relationships:', ['license' => $harvesterLicense->toArray()]);
        
        // Verify each relationship
        Log::info('Relationship check:', [
            'has_applicant' => $harvesterLicense->applicant ? 'yes' : 'no',
            'has_island' => $harvesterLicense->island ? 'yes' : 'no',
            'has_license_type' => $harvesterLicense->licenseType ? 'yes' : 'no',
            'species_count' => $harvesterLicense->species->count(),
            'group_members_count' => $harvesterLicense->groupMembers->count()
        ]);

        return view('license.harvester_license.show', compact('harvesterLicense'));

    } catch (\Exception $e) {
        Log::error('Error showing license:', [
            'error' => $e->getMessage(),
            'license_id' => $id,
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->route('harvester.licenses.index')
            ->with('error', 'Error viewing license: ' . $e->getMessage());
    }
}

 /**
     * Generate a license number.
     *
     * @param $license
     * @return string
     */
   
    /**
     * Show form to issue a license.
     *
     * @param License $license
     * @return \Illuminate\Http\Response
     */
    public function showIssueLicenseForm(HarvesterLicense $license)
{
    try {
        // Get additional data needed for the form
        $applicant = $this->harvesterApplicantsRepository->getById($license->harvester_applicant_id);
        $islands = $this->islandsRepository->pluck();
        $licenseTypes = DB::table('license_types')->pluck('name', 'id');
        
        // Get species by license type
        $speciesByLicenseType = [];
        foreach ($licenseTypes as $id => $name) {
            $speciesByLicenseType[$id] = $this->speciesRepository->getByLicenseType($id);
        }

        return view('license.harvester_license.issue', compact(
            'license',
            'applicant',
            'islands',
            'licenseTypes',
            'speciesByLicenseType'
        ));
        
    } catch (\Exception $e) {
        Log::error('Error showing license issue form:', [
            'error' => $e->getMessage(),
            'license_id' => $license->id
        ]);
        
        return redirect()
            ->back()
            ->withErrors('Error: ' . $e->getMessage());
    }
}

    /**
     * Download the license as a PDF.
     *
     * @param License $license
     * @return \Illuminate\Http\Response
     */
    public function downloadLicense(HarvesterLicense $license)
{
    try {
        $license->load(['applicant', 'licenseType', 'island']); // Changed from harvesterApplicant to applicant

        // Debug logging
        Log::info('License Details:', [
            'id' => $license->id,
            'type' => $license->licenseType->name ?? 'No type',
            'applicant' => $license->applicant->name ?? 'No applicant' // Changed from harvesterApplicant to applicant
        ]);

        $view = $this->getLicenseTemplateView($license->licenseType->name);
        
        // Check if view exists
        if (!View::exists($view)) {
            Log::error('Template not found:', ['view' => $view]);
            return redirect()->back()->with('error', 'License template not found.');
        }

        // Render view first to check for any errors
        $html = View::make($view, [
            'license' => $license,
            'applicant' => $license->applicant, // Changed from harvesterApplicant to applicant
            'island' => $license->island
        ])->render();

        $pdf = PDF::loadHTML($html);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Generate filename
        $filename = sprintf(
            'License-%s-%s.pdf',
            $license->license_number,
            date('Y-m-d')
        );

        // Force download
        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('License Download Error:', [
            'message' => $e->getMessage(),
            'license_id' => $license->id,
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->back()
            ->withErrors('Unable to generate license PDF: ' . $e->getMessage());
    }
}

    /**
     * Get the license template view based on the license type.
     *
     * @param string $licenseTypeName
     * @return string
     */
    private function getLicenseTemplateView(string $licenseType): string
    {
        // Clean up the input
        $type = strtolower(trim($licenseType));
        
        Log::info('Getting template for license type: ' . $type);
    
        // Map license types to views - update these to match exactly with your database values
        $templates = [
            'export license for seacucumber' => 'license.harvester_license.seacucumber',
            'export license for petfish' => 'license.harvester_license.aquarium',
            'export license for lobster' => 'license.harvester_license.lobster',
            'export license for shark fin' => 'license.harvester_license.shark_fin',
            // Add any other license types that exist in your database
        ];
    
        // Debug log to see what type we're trying to match
        Log::info('Attempting to match license type: ' . $type);
        Log::info('Available templates: ' . implode(', ', array_keys($templates)));
    
        // Get template or fallback to default
        $view = $templates[$type] ?? 'license.harvester_license.default';
        
        Log::info('Selected template: ' . $view);
        
        return $view;
    }

    /**
 * Issue the license
 */
public function issueLicense(Request $request, HarvesterLicense $license)
{
    try {
        $validated = $request->validate([
            'issue_date' => 'required|date|after_or_equal:today',
            'expiry_date' => 'required|date|after:issue_date',
        ]);

        $license->update([
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'status' => 'license_issued',
            'issued_by' => auth()->id(),
            'issued_at' => now(),
        ]);

        return redirect()
            ->route('harvester.licenses.index')
            ->with('success', 'License has been issued successfully');
            
    } catch (\Exception $e) {
        Log::error('Error issuing license:', [
            'error' => $e->getMessage(),
            'license_id' => $license->id
        ]);
        
        return redirect()
            ->back()
            ->withInput()
            ->withErrors('Error: ' . $e->getMessage());
    }
}

public function downloadPDF($id)
{
    try {
        $harvesterLicense = $this->harvesterLicensesRepository->getById($id);
        
        if (!$harvesterLicense) {
            throw new \Exception('Harvester License not found.');
        }

        // Load necessary relationships
        $harvesterLicense->load([
            'applicant',
            'island',
            'licenseType',
            'species',
            'groupMembers'
        ]);

        // Generate PDF using your preferred PDF library (example using DomPDF)
        $pdf = \PDF::loadView('license.harvester_license.pdf', compact('harvesterLicense'));
        
        // Generate filename
        $filename = 'harvester_license_' . $harvesterLicense->license_number . '.pdf';

        // Return the PDF for download
        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Error generating PDF:', [
            'error' => $e->getMessage(),
            'license_id' => $id,
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->back()
            ->with('error', 'Error generating PDF: ' . $e->getMessage());
    }
}


}