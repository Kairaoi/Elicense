<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;

use App\Models\Pfps\TargetSpecies;
use App\Repositories\Pfps\TargetSpeciesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class TargetSpeciesController extends Controller
{
    protected $targetSpeciesRepository;

    /**
     * TargetSpeciesController constructor.
     */
    public function __construct(TargetSpeciesRepository $targetSpeciesRepository)
    {
        $this->targetSpeciesRepository = $targetSpeciesRepository;
    }

    /**
     * Get DataTable of target species.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->targetSpeciesRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of target species.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.target_species.index');
    }

    /**
     * Show form for creating new target species.
     *
     * @return Response
     */
    public function create()
    {
        return view('pfps.target_species.create');
    }

    /**
     * Store a newly created target species in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'species_name' => 'required|string|max:255',
            'species_category' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        
        $species = $this->targetSpeciesRepository->create($data);

        return redirect()->route('pfps.target_species.index')->with('success', 'Target species created successfully');
    }

    /**
     * Display the specified target species.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $species = $this->targetSpeciesRepository->getById($id);

        if (!$species) {
            return response()->json(['message' => 'Target species not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.target_species.show', compact('species'));
    }

    /**
     * Show the form for editing the specified target species.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $species = $this->targetSpeciesRepository->getById($id);

        if (!$species) {
            return response()->json(['message' => 'Target species not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.target_species.edit', compact('species'));
    }

    /**
     * Update the specified target species in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'species_name' => 'required|string|max:255',
            'species_category' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->targetSpeciesRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update target species'], Response::HTTP_BAD_REQUEST);
        }

        return redirect()->route('pfps.target_species.index')->with('success', 'Target species updated successfully');
    }

    /**
     * Remove the specified target species from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->targetSpeciesRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Failed to delete target species'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['message' => 'Target species deleted successfully']);
    }
}
