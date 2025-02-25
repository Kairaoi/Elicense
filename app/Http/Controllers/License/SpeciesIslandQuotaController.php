<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\SpeciesIslandQuotaRepository; 
use App\Repositories\License\SpeciesRepository;
use App\Repositories\Reference\IslandsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class SpeciesIslandQuotaController extends Controller
{
    protected $speciesIslandQuotaRepository;
    protected $speciesRepository;
    protected $islandRepository;

    /**
     * SpeciesIslandQuotaController constructor.
     *
     * @param SpeciesIslandQuotaRepository $speciesIslandQuotaRepository
     * @param SpeciesRepository $speciesRepository
     * @param IslandRepository $islandRepository
     */
    public function __construct(
        SpeciesIslandQuotaRepository $speciesIslandQuotaRepository,
        SpeciesRepository $speciesRepository,
        IslandsRepository $islandRepository
    )
    {
        $this->speciesIslandQuotaRepository = $speciesIslandQuotaRepository;
        $this->speciesRepository = $speciesRepository;
        $this->islandRepository = $islandRepository;
    }

    /**
     * Get DataTable of species island quotas.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->speciesIslandQuotaRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of species island quotas.
     *
     * @return Response
     */
    public function index()
    {
        return view('license.speciesIslandQuota.index');
    }

    /**
     * Show the form for creating a new species island quota.
     *
     * @return Response
     */
    public function create()
    {
        $species = $this->speciesRepository->pluck();
        $islands = $this->islandRepository->pluck();
        return view('license.speciesIslandQuota.create', compact('species', 'islands'));
    }

   /**
 * Store a newly created species island quota in storage.
 *
 * @param Request $request
 * @return Response
 */
public function store(Request $request)
{
    // Validate request data
    $data = $request->validate([
        'species_id'      => 'required|array',
        'species_id.*'    => 'exists:species,id',
        'island_id'       => 'required|array',
        'island_id.*'     => 'exists:islands,id',
        'island_quota'    => 'required|array',
        'island_quota.*'  => 'numeric|min:0',
        'year'            => 'required|integer',
    ]);

    $createdBy = auth()->check() ? auth()->id() : null;
    $successCount = 0;

    // Create quota entries for each species-island combination
    foreach ($data['species_id'] as $speciesId) {
        foreach ($data['island_id'] as $key => $islandId) {
            // Get quota for this island if it exists, otherwise use the first quota
            $quota = isset($data['island_quota'][$key]) ? $data['island_quota'][$key] : $data['island_quota'][0];

            $quotaData = [
                'species_id' => $speciesId,
                'island_id' => $islandId,
                'island_quota' => $quota,
                'remaining_quota' => $quota, // Initial remaining quota equals island quota
                'year' => $data['year'],
                'created_by' => $createdBy
            ];
            
            $this->speciesIslandQuotaRepository->create($quotaData);
            $successCount++;
        }
    }

    // Redirect with success message
    return redirect()->route('species-island-quotas.quota.index')
        ->with('success', $successCount . ' Species Island Quota entries created successfully.');
}

    /**
     * Display the specified species island quota.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $speciesIslandQuota = $this->speciesIslandQuotaRepository->getById($id);

        if (!$speciesIslandQuota) {
            return response()->json(['message' => 'Species Island Quota not found'], Response::HTTP_NOT_FOUND);
        }

        return view('license.speciesIslandQuota.show', compact('speciesIslandQuota'));
    }

    /**
     * Show the form for editing the specified species island quota.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $speciesIslandQuota = $this->speciesIslandQuotaRepository->getById($id);
        $species = $this->speciesRepository->pluck();
        $islands = $this->islandRepository->pluck();

        if (!$speciesIslandQuota) {
            return redirect()->route('species-island-quotas.quota.index')->with('error', 'Species Island Quota not found.');
        }

        return view('license.speciesIslandQuota.edit', compact('speciesIslandQuota', 'species', 'islands'));
    }

    /**
     * Update the specified species island quota in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'species_id'      => 'required|exists:species,id',
            'island_id'       => 'required|exists:islands,id',
            'island_quota'    => 'required|numeric|min:0',
            'remaining_quota' => 'required|numeric|min:0',
            'year'            => 'required|integer',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the species island quota record
        $updated = $this->speciesIslandQuotaRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('license.speciesIslandQuota.index')->with('error', 'Failed to update species island quota.');
        }

        return redirect()->route('species-island-quotas.quota.index')->with('success', 'Species Island Quota updated successfully.');
    }

    /**
     * Remove the specified species island quota from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->speciesIslandQuotaRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Species Island Quota not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Species Island Quota deleted successfully']);
    }
}
