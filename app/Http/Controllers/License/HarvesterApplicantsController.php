<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\HarvesterApplicantsRepository; 
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class HarvesterApplicantsController extends Controller
{
    protected $harvesterApplicantsRepository;

    /**
     * HarvesterApplicantsController constructor.
     *
     * @param HarvesterApplicantsRepository $harvesterApplicantsRepository
     */
    public function __construct(HarvesterApplicantsRepository $harvesterApplicantsRepository)
    {
        $this->harvesterApplicantsRepository = $harvesterApplicantsRepository;
    }

    /**
     * Get DataTable of harvester applicants.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->harvesterApplicantsRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of harvester applicants.
     *
     * @return Response
     */
    public function index()
    {
        return view('license.harvester_applicant.index');
    }

    /**
     * Show the form for creating a new harvester applicant.
     *
     * @return Response
     */
    public function create()
    {
        return view('license.harvester_applicant.create');
    }

    /**
     * Store a newly created harvester applicant in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:harvester_applicants,email',
            'is_group' => 'required|boolean',
            'group_size' => 'nullable|integer|min:1',
            'national_id' => 'required|string|max:50',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->check() ? auth()->id() : null;

        // Save the harvester applicant using the repository
        $applicant = $this->harvesterApplicantsRepository->create($data);

        // Redirect with success message
        return redirect()->route('harvester.applicants.index')->with('success', 'Harvester applicant created successfully.');
    }

    /**
     * Display the specified harvester applicant.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $applicant = $this->harvesterApplicantsRepository->getById($id);

        if (!$applicant) {
            return response()->json(['message' => 'Harvester applicant not found'], Response::HTTP_NOT_FOUND);
        }

        return view('license.harvester_applicant.show', compact('applicant'));
    }

    /**
     * Show the form for editing the specified harvester applicant.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $applicant = $this->harvesterApplicantsRepository->getById($id);

        if (!$applicant) {
            return redirect()->route('harvester.applicants.index')->with('error', 'Harvester applicant not found.');
        }

        return view('license.harvester_applicant.edit', compact('applicant'));
    }

    /**
     * Update the specified harvester applicant in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:harvester_applicants,email,' . $id,
            'is_group' => 'required|boolean',
            'group_size' => 'nullable|integer|min:1',
            'national_id' => 'required|string|max:50',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the harvester applicant record
        $updated = $this->harvesterApplicantsRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('harvester.applicants.index')->with('error', 'Failed to update harvester applicant.');
        }

        return redirect()->route('harvester.applicants.index')->with('success', 'Harvester applicant updated successfully.');
    }

    /**
     * Remove the specified harvester applicant from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->harvesterApplicantsRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Harvester applicant not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Harvester applicant deleted successfully']);
    }
}
