<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\ExportDeclarationRepository; 
use App\Repositories\License\ApplicantsRepository;
use App\Repositories\License\SpeciesRepository;
use App\Models\License\ExportDeclaration;
use App\Models\License\ExportDeclarationSpecies;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;
use DB;
use PDF;

class ExportDeclarationsController extends Controller
{
    protected $exportDeclarationRepository;
    protected $applicantsRepository;
    protected $speciesRepository;

    /**
     * ExportDeclarationsController constructor.
     *
     * @param ExportDeclarationRepository $exportDeclarationRepository
     */
    public function __construct(ApplicantsRepository $applicantsRepository,ExportDeclarationRepository $exportDeclarationRepository, SpeciesRepository $speciesRepository)
    {
        $this->exportDeclarationRepository = $exportDeclarationRepository;
        $this->applicantsRepository = $applicantsRepository;
        $this->speciesRepository = $speciesRepository;
    }

    /**
     * Get DataTable of export declarations.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->exportDeclarationRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of export declarations.
     *
     * @return Response
     */
    public function index()
    {
        return view('license.export.index');
    }

    /**
     * Show the form for creating a new export declaration.
     *
     * @return Response
     */
    public function create()
{
    // Assuming applicants have 'first_name' and 'id' fields and species have 'name' and 'id' fields
    $applicants = $this->applicantsRepository->pluck();
    $speciesList = $this->speciesRepository->pluck('name', 'id');
    // dd($applicants);
    return view('license.export.create')->with('applicants', $applicants)->with('speciesList', $speciesList);
}

public function getSpeciesForApplicant(Request $request)
{
    $applicantId = $request->input('applicant_id');
    $species = $this->speciesRepository->getSpeciesForApplicant($applicantId);
    return response()->json($species);
}


    /**
     * Store a new export declaration and associated species data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        \Log::info('Received request data:', $request->all());
    
        if (!auth()->check()) {
            \Log::warning('Unauthenticated user tried to create export declaration');
            return redirect()->route('login')->with('error', 'Please log in to create an export declaration.');
        }
    
        // Validate the request data
        try {
            $validatedData = $request->validate([
                'applicant_id' => 'required|exists:applicants,id',
                'shipment_date' => 'required|date',
                'export_destination' => 'required|string|max:255',
                'species' => 'required|array',
                'species.*.species_id' => 'required|exists:species,id',
                'species.*.volume_kg' => 'required|numeric|min:0',
                'species.*.under_size_volume_kg' => 'nullable|numeric|min:0',
                'species.*.unit_price' => 'required|numeric|min:0',
            ]);
            \Log::info('Validated data:', $validatedData);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation failed:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    
        // Initialize total license fee
        $totalLicenseFee = 0;
        $feePerUnit = 3.00; // $3.00 per 28kg unit
        $weightPerUnit = 28; // 28kg per unit
    
        // Begin a transaction to ensure data integrity
        DB::beginTransaction();
    
        try {
            // Loop through species data to calculate total license fee
            foreach ($validatedData['species'] as $speciesData) {
                $volume = $speciesData['volume_kg'];
                
                // Calculate number of 28kg units (rounded up)
                $units = ceil($volume / $weightPerUnit);
                
                // Calculate fee for this species
                $speciesFee = $units * $feePerUnit;
                
                // Add to total license fee
                $totalLicenseFee += $speciesFee;
                
                // Store the calculated fee per kg for this species
                $speciesData['unit_price'] = $feePerUnit;
            }
    
            \Log::info('Calculated total license fee: ' . $totalLicenseFee);
    
            // Create a new export declaration with the calculated total license fee
            $exportDeclaration = ExportDeclaration::create([
                'applicant_id' => $validatedData['applicant_id'],
                'shipment_date' => $validatedData['shipment_date'],
                'export_destination' => $validatedData['export_destination'],
                'total_license_fee' => $totalLicenseFee,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
    
            \Log::info('Created export declaration with ID: ' . $exportDeclaration->id);
    
            // Loop through species data and create export_declaration_species records
            foreach ($validatedData['species'] as $speciesData) {
                $volume = $speciesData['volume_kg'];
                $units = ceil($volume / $weightPerUnit);
                $speciesFee = $units * $feePerUnit;
                
                $species = ExportDeclarationSpecies::create([
                    'export_declaration_id' => $exportDeclaration->id,
                    'species_id' => $speciesData['species_id'],
                    'volume_kg' => $volume,
                    'under_size_volume_kg' => $speciesData['under_size_volume_kg'],
                    'fee_per_kg' => $feePerUnit,
                    'units' => $units,
                    'total_fee' => $speciesFee
                ]);
                \Log::info('Created export declaration species with ID: ' . $species->id);
            }
    
            // Commit the transaction
            DB::commit();
            \Log::info('Transaction committed successfully');
    
            // Return a success response
            return redirect()->route('export.declarations.index')
                ->with('success', 'Export declaration created successfully! Total Fee: $' . number_format($totalLicenseFee, 2));
    
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            \Log::error('Error creating export declaration: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
    
            // Return an error response
            return redirect()->route('export.declarations.index')
                ->with('error', 'Failed to create export declaration: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified export declaration.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
{
    // Retrieve the export declaration by ID
    $declaration = $this->exportDeclarationRepository->getById($id);
    
    if (!$declaration) {
        return response()->json(['message' => 'Export declaration not found'], Response::HTTP_NOT_FOUND);
    }
    
    // Get the associated license type of the declaration
    $licenseTypeId = $declaration->license->license_type_id; // Make sure the 'license' relationship is correctly loaded
    
    // Load species for the specific license type
    $declaration->load(['species.species' => function ($query) use ($licenseTypeId) {
        $query->where('license_type_id', $licenseTypeId); // Filter species by license type
    }]);

    return view('license.export.invoice', compact('declaration'));
}

    /**
     * Show the form for editing the specified export declaration.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        // Retrieve the export declaration by ID
        $exportDeclaration = $this->exportDeclarationRepository->getById($id);
        
        // Fetch applicants and species lists
        $applicants = $this->applicantsRepository->pluck();
        $speciesList = $this->speciesRepository->pluck('name', 'id');
    
        // Ensure that the export declaration exists before continuing
        if (!$exportDeclaration) {
            return redirect()->route('export.declarations.index')->with('error', 'Export Declaration not found.');
        }
    
        // Pass the export declaration along with the applicants and species list to the edit view
        return view('license.export.edit')
            ->with('exportDeclaration', $exportDeclaration)
            ->with('applicants', $applicants)
            ->with('speciesList', $speciesList);
    }
    

    /**
     * Update the specified export declaration in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
{
    \Log::info('Received update request data:', $request->all());

    if (!auth()->check()) {
        \Log::warning('Unauthenticated user tried to update export declaration');
        return redirect()->route('login')->with('error', 'Please log in to update an export declaration.');
    }

    // Validate the request data
    try {
        $validatedData = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'shipment_date' => 'required|date',
            'export_destination' => 'required|string|max:255',
            'species' => 'required|array',
            'species.*.species_id' => 'required|exists:species,id',
            'species.*.volume_kg' => 'required|numeric|min:0',
            'species.*.under_size_volume_kg' => 'nullable|numeric|min:0',
            'species.*.unit_price' => 'required|numeric|min:0',
        ]);
        \Log::info('Validated update data:', $validatedData);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::warning('Update validation failed:', $e->errors());
        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    // Initialize total license fee
    $totalLicenseFee = 0;

    // Begin a transaction to ensure data integrity
    DB::beginTransaction();

    try {
        $exportDeclaration = ExportDeclaration::findOrFail($id);

        // Loop through species data to calculate total license fee
        foreach ($validatedData['species'] as $speciesData) {
            $volume = $speciesData['volume_kg'];
            $unitPrice = $speciesData['unit_price'];

            // Calculate fee for this species
            $speciesFee = $volume * $unitPrice;

            // Add to total license fee
            $totalLicenseFee += $speciesFee;
        }

        \Log::info('Calculated updated total license fee: ' . $totalLicenseFee);

        // Update the export declaration with the new data
        $exportDeclaration->update([
            'applicant_id' => $validatedData['applicant_id'],
            'shipment_date' => $validatedData['shipment_date'],
            'export_destination' => $validatedData['export_destination'],
            'total_license_fee' => $totalLicenseFee,
            'updated_by' => auth()->id(),
        ]);

        \Log::info('Updated export declaration with ID: ' . $exportDeclaration->id);

        // Delete existing export_declaration_species records
        $exportDeclaration->species()->delete();

        // Create new export_declaration_species records
        foreach ($validatedData['species'] as $speciesData) {
            $species = ExportDeclarationSpecies::create([
                'export_declaration_id' => $exportDeclaration->id,
                'species_id' => $speciesData['species_id'],
                'volume_kg' => $speciesData['volume_kg'],
                'under_size_volume_kg' => $speciesData['under_size_volume_kg'],
                'fee_per_kg' => $speciesData['unit_price'],
            ]);
            \Log::info('Created updated export declaration species with ID: ' . $species->id);
        }

        // Commit the transaction
        DB::commit();
        \Log::info('Update transaction committed successfully');

        // Return a success response
        return redirect()->route('export.declarations.index', $exportDeclaration->id)->with('success', 'Export declaration updated successfully!');

    } catch (\Exception $e) {
        // Rollback the transaction if something goes wrong
        DB::rollBack();
        \Log::error('Error updating export declaration: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        // Return an error response
        return redirect()->route('export.declarations.index', $id)->with('error', 'Failed to update export declaration: ' . $e->getMessage());
    }
}
    /**
     * Remove the specified export declaration from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->exportDeclarationRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Export declaration not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Export declaration deleted successfully']);
    }



public function showInvoice($id)
{
    // Fetch the export declaration from the database
    $declaration = $this->exportDeclarationRepository->getById($id);

   

    if (!$declaration) {
        return response()->json(['message' => 'Export declaration not found'], Response::HTTP_NOT_FOUND);
    }

    // Load the view and pass the declaration data
    $pdf = PDF::loadView('license.export.invoice', compact('declaration'));

    // Download the PDF
    return $pdf->download('export_declaration_' . $declaration->id . '.pdf');
}

}
