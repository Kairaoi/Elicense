<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\DurationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class DurationController extends Controller
{
    protected $durationRepository;

    /**
     * DurationController constructor.
     *
     * @param DurationRepository $durationRepository
     */
    public function __construct(DurationRepository $durationRepository)
    {
        $this->durationRepository = $durationRepository;
    }

    /**
     * Get DataTable of durations.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->durationRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of durations.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.duration.index');
    }

    /**
     * Show the form for creating a new duration.
     *
     * @return Response
     */
    public function create()
    {
        return view('pfps.duration.create');
    }

    /**
     * Store a newly created duration in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'duration_name' => 'required|string|max:255|unique:durations,duration_name',
            'fee_amount' => 'required|numeric|min:0',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->id();

        // Save the duration using the repository
        $duration = $this->durationRepository->create($data);

        // Redirect with success message
        return redirect()->route('pfps.durations.index')
            ->with('success', 'Duration period created successfully.');
    }

    /**
     * Display the specified duration.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $duration = $this->durationRepository->getById($id);

        if (!$duration) {
            return response()->json(['message' => 'Duration not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.duration.show', compact('duration'));
    }

    /**
     * Show the form for editing the specified duration.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $duration = $this->durationRepository->getById($id);

        if (!$duration) {
            return redirect()->route('pfps.durations.index')
                ->with('error', 'Duration not found.');
        }

        return view('pfps.duration.edit', compact('duration'));
    }

    /**
     * Update the specified duration in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'duration_name' => 'required|string|max:255|unique:durations,duration_name,' . $id . ',duration_id',
            'fee_amount' => 'required|numeric|min:0',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the duration record
        $updated = $this->durationRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('pfps.durations.index')
                ->with('error', 'Failed to update duration.');
        }

        return redirect()->route('pfps.durations.index')
            ->with('success', 'Duration updated successfully.');
    }

    /**
     * Remove the specified duration from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->durationRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(
                ['message' => 'Duration not found or failed to delete'], 
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(['message' => 'Duration deleted successfully']);
    }

    /**
     * Get all durations for dropdown.
     *
     * @return Response
     */
    public function getDurations()
    {
        $durations = $this->durationRepository->getAll();
        return response()->json($durations);
    }
}