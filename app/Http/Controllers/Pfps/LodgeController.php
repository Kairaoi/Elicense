<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\LodgeRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class LodgeController extends Controller
{
    protected $lodgeRepository;

    /**
     * LodgeController constructor.
     *
     * @param LodgeRepository $lodgeRepository
     */
    public function __construct(LodgeRepository $lodgeRepository)
    {
        $this->lodgeRepository = $lodgeRepository;
    }

    /**
     * Get DataTable of lodges.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->lodgeRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of lodges.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.lodge.index');
    }

    /**
     * Show the form for creating a new lodge.
     *
     * @return Response
     */
    public function create()
    {
        return view('pfps.lodge.create');
    }

    /**
     * Store a newly created lodge in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'lodge_name' => 'required|string|max:255|unique:lodges,lodge_name',
            'location' => 'nullable|string|max:255',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->id();

        // Save the lodge using the repository
        $lodge = $this->lodgeRepository->create($data);

        // Redirect with success message
        return redirect()->route('pfps.lodges.index')->with('success', 'Lodge created successfully.');
    }

    /**
     * Display the specified lodge.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $lodge = $this->lodgeRepository->getById($id);

        if (!$lodge) {
            return response()->json(['message' => 'Lodge not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.lodge.show', compact('lodge'));
    }

    /**
     * Show the form for editing the specified lodge.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $lodge = $this->lodgeRepository->getById($id);

        if (!$lodge) {
            return redirect()->route('pfps.lodges.index')->with('error', 'Lodge not found.');
        }

        return view('pfps.lodge.edit', compact('lodge'));
    }

    /**
     * Update the specified lodge in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'lodge_name' => 'required|string|max:255|unique:lodges,lodge_name,' . $id . ',lodge_id',
            'location' => 'nullable|string|max:255',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the lodge record
        $updated = $this->lodgeRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('pfps.lodges.index')->with('error', 'Failed to update lodge.');
        }

        return redirect()->route('pfps.lodges.index')->with('success', 'Lodge updated successfully.');
    }

    /**
     * Remove the specified lodge from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->lodgeRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Lodge not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Lodge deleted successfully']);
    }
}