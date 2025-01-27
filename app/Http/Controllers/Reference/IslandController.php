<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use App\Repositories\Reference\IslandsRepository; 
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class IslandController extends Controller
{
    protected $islandsRepository;

    /**
     * IslandsController constructor.
     *
     * @param IslandsRepository $islandsRepository
     */
    public function __construct(IslandsRepository $islandsRepository)
    {
        $this->islandsRepository = $islandsRepository;
    }

    /**
     * Get DataTable of islands.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->islandsRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of islands.
     *
     * @return Response
     */
    public function index()
    {
        return view('reference.island.index');
    }

    /**
     * Show the form for creating a new island.
     *
     * @return Response
     */
    public function create()
    {
        return view('reference.island.create');
    }

    /**
     * Store a newly created island in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:islands,name',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->check() ? auth()->id() : null;

        // Save the island using the repository
        $island = $this->islandsRepository->create($data);

        // Redirect with success message
        return redirect()->route('reference.islands.index')->with('success', 'Island created successfully.');
    }

    /**
     * Display the specified island.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $island = $this->islandsRepository->getById($id);

        if (!$island) {
            return response()->json(['message' => 'Island not found'], Response::HTTP_NOT_FOUND);
        }

        return view('reference.island.show', compact('island'));
    }

    /**
     * Show the form for editing the specified island.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $island = $this->islandsRepository->getById($id);

        if (!$island) {
            return redirect()->route('reference.islands.index')->with('error', 'Island not found.');
        }

        return view('reference.island.edit', compact('island'));
    }

    /**
     * Update the specified island in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:islands,name,' . $id,
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the island record
        $updated = $this->islandsRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('reference.islands.index')->with('error', 'Failed to update island.');
        }

        return redirect()->route('reference.islands.index')->with('success', 'Island updated successfully.');
    }

    /**
     * Remove the specified island from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->islandsRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Island not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Island deleted successfully']);
    }
}
