<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\LicenseItemsRepository; 
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class LicenseItemsController extends Controller
{
    protected $licenseItemsRepository;

    /**
     * LicenseItemsController constructor.
     *
     * @param LicenseItemsRepository $licenseItemsRepository
     */
    public function __construct(LicenseItemsRepository $licenseItemsRepository)
    {
        $this->licenseItemsRepository = $licenseItemsRepository;
    }

    /**
     * Get DataTable of license items.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->licenseItemsRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of license items.
     *
     * @return Response
     */
    public function index()
    {
        return view('license.license_item.index');
    }

    /**
     * Show the form for creating a new license item.
     *
     * @return Response
     */
    public function create()
    {
        return view('license.license_item.create');
    }

    /**
     * Store a newly created license item in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'license_id'   => 'required|exists:licenses,id',
            'species_id'   => 'required|exists:species,id',
            'quantity'     => 'required|numeric|min:0',
            'unit_price'   => 'required|numeric|min:0',
            'total_price'  => 'required|numeric|min:0',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->check() ? auth()->id() : null;

        // Calculate total price if not provided
        if (!isset($data['total_price'])) {
            $data['total_price'] = $data['quantity'] * $data['unit_price'];
        }

        // Save the license item using the repository
        $licenseItem = $this->licenseItemsRepository->create($data);

        // Redirect with success message
        return redirect()->route('license.license_items.index')->with('success', 'License item created successfully.');
    }

    /**
     * Display the specified license item.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $licenseItem = $this->licenseItemsRepository->getById($id);

        if (!$licenseItem) {
            return response()->json(['message' => 'License item not found'], Response::HTTP_NOT_FOUND);
        }

        return view('license.license_item.show', compact('licenseItem'));
    }

    /**
     * Show the form for editing the specified license item.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $licenseItem = $this->licenseItemsRepository->getById($id);

        if (!$licenseItem) {
            return redirect()->route('license.license_items.index')->with('error', 'License item not found.');
        }

        return view('license.license_item.edit', compact('licenseItem'));
    }

    /**
     * Update the specified license item in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'license_id'   => 'required|exists:licenses,id',
            'species_id'   => 'required|exists:species,id',
            'quantity'     => 'required|numeric|min:0',
            'unit_price'   => 'required|numeric|min:0',
            'total_price'  => 'required|numeric|min:0',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the license item record
        $updated = $this->licenseItemsRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('license.license_items.index')->with('error', 'Failed to update license item.');
        }

        return redirect()->route('license.license_items.index')->with('success', 'License item updated successfully.');
    }

    /**
     * Remove the specified license item from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->licenseItemsRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'License item not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'License item deleted successfully']);
    }
}
