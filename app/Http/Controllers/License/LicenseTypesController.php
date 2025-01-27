<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\LicenseTypesRepository; 
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class LicenseTypesController extends Controller
{
    protected $licenseTypesRepository;

    /**
     * LicenseTypesController constructor.
     *
     * @param LicenseTypesRepository $licenseTypesRepository
     */
    public function __construct(LicenseTypesRepository $licenseTypesRepository)
    {
        $this->licenseTypesRepository = $licenseTypesRepository;
    }

    /**
     * Get DataTable of license types.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->licenseTypesRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of license types.
     *
     * @return Response
     */
    public function index()
    {
        return view('license.license_type.index');
    }

    /**
     * Show the form for creating a new license type.
     *
     * @return Response
     */
    public function create()
    {
        return view('license.license_type.create');
    }

    /**
     * Store a newly created license type in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:license_types,name',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->check() ? auth()->id() : null;

        // Save the license type using the repository
        $licenseType = $this->licenseTypesRepository->create($data);

        // Redirect with success message
        return redirect()->route('reference.licenses_types.index')->with('success', 'License type created successfully.');
    }

    /**
     * Display the specified license type.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $licenseType = $this->licenseTypesRepository->getById($id);

        if (!$licenseType) {
            return response()->json(['message' => 'License type not found'], Response::HTTP_NOT_FOUND);
        }

        return view('license.license_type.show', compact('licenseType'));
    }

    /**
     * Show the form for editing the specified license type.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $licenseType = $this->licenseTypesRepository->getById($id);

        if (!$licenseType) {
            return redirect()->route('reference.licenses_types.index')->with('error', 'License type not found.');
        }

        return view('license.license_type.edit', compact('licenseType'));
    }

    /**
     * Update the specified license type in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:license_types,name,' . $id,
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the license type record
        $updated = $this->licenseTypesRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('reference.licenses_types.index')->with('error', 'Failed to update license type.');
        }

        return redirect()->route('reference.licenses_types.index')->with('success', 'License type updated successfully.');
    }

    /**
     * Remove the specified license type from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->licenseTypesRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'License type not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'License type deleted successfully']);
    }
}
