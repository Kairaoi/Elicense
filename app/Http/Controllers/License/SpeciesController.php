<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\SpeciesRepository; 
use App\Repositories\License\LicenseTypesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class SpeciesController extends Controller
{
    protected $speciesRepository;
    protected $licenseTypesRepository;

    /**
     * SpeciesController constructor.
     *
     * @param SpeciesRepository $speciesRepository
     */
    public function __construct(SpeciesRepository $speciesRepository, LicenseTypesRepository $licenseTypesRepository)
    {
        $this->speciesRepository = $speciesRepository;
        $this->licenseTypesRepository = $licenseTypesRepository;
    }

    /**
     * Get DataTable of species.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->speciesRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of species.
     *
     * @return Response
     */
    public function index()
    {
        return view('license.species.index');
    }

    /**
     * Show the form for creating a new species.
     *
     * @return Response
     */
    public function create()
    {

        $licenseTypes = $this->licenseTypesRepository->pluck();
        // dd($licenseTypes);
        return view('license.species.create')->with('licenseTypes', $licenseTypes);
        
    }

    /**
     * Store a newly created species in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'license_type_id'  => 'required|exists:license_types,id',
            'quota'            => 'required|numeric|min:0',
            'year'            => 'required|numeric|min:0',
            'unit_price'       => 'required|numeric|min:0',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->check() ? auth()->id() : null;

        // Save the species using the repository
        $species = $this->speciesRepository->create($data);

        // Redirect with success message
        return redirect()->route('reference.species.index')->with('success', 'Species created successfully.');
    }

    /**
     * Display the specified species.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $species = $this->speciesRepository->getById($id);

        if (!$species) {
            return response()->json(['message' => 'Species not found'], Response::HTTP_NOT_FOUND);
        }

        return view('license.species.show', compact('species'));
    }

    /**
     * Show the form for editing the specified species.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $species = $this->speciesRepository->getById($id);
        $licenseTypes = $this->licenseTypesRepository->pluck();

        if (!$species) {
            return redirect()->route('reference.species.index')->with('error', 'Species not found.');
        }

        return view('license.species.edit', compact('species','licenseTypes'));
    }

    /**
     * Update the specified species in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'license_type_id'  => 'required|exists:license_types,id',
            'quota'            => 'required|numeric|min:0',
            'year'            => 'required|numeric|min:0',
            'unit_price'       => 'required|numeric|min:0',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the species record
        $updated = $this->speciesRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('reference.species.index')->with('error', 'Failed to update species.');
        }

        return redirect()->route('reference.species.index')->with('success', 'Species updated successfully.');
    }

    /**
     * Remove the specified species from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->speciesRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Species not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Species deleted successfully']);
    }
}
