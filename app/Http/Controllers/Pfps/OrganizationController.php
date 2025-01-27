<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\OrganizationRepository; 

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class OrganizationController extends Controller
{
    protected $organizationRepository;
   

    /**
     * OrganisationController constructor.
     *
     * @param OrganizationRepository $OrganizationRepository
     */
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
       
    }

    /**
     * Get DataTable of organistion.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->organizationRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of organistion.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.organization.index');
    }

    /**
     * Show the form for creating a new organistion.
     *
     * @return Response
     */
    public function create()
    {

       
        return view('pfps.organization.create');
        
    }

    /**
     * Store a newly created organistion in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
{
    // Validate request data
    $data = $request->validate([
        'organization_name' => 'required|string|max:255|unique:organizations,organization_name',
    ]);

    // Track the creator's ID
    $data['created_by'] = auth()->id(); // Ensure user is authenticated

    // Save the organization using the repository or model
    $organization = $this->organizationRepository->create($data);

    // Redirect with success message
    return redirect()->route('pfps.organizations.index')->with('success', 'Organization created successfully.');
}

    /**
     * Display the specified organistion.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $organistion = $this->organizationRepository->getById($id);

        if (!$organistion) {
            return response()->json(['message' => 'organistion not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.organization.show', compact('organistion'));
    }

    /**
     * Show the form for editing the specified organistion.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $organization = $this->organizationRepository->getById($id);
       

        if (!$organization) {
            return redirect()->route('pfps.organizations.index')->with('error', 'organization not found.');
        }

        return view('pfps.organization.edit', compact('organization'));
    }

    /**
     * Update the specified organistion in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'organization_name' => 'required|string|max:255|unique:organizations,organization_name',
            
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);
        

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the organistion record
        $updated = $this->organizationRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('pfps.organizations.index')->with('error', 'Failed to update organistion.');
        }

        return redirect()->route('pfps.organizations.index')->with('success', 'organistion updated successfully.');
    }

    /**
     * Remove the specified organistion from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->organizationRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'organistion not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'organistion deleted successfully']);
    }
}
