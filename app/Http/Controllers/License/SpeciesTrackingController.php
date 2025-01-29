<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\SpeciesTrackingRepository;
use App\Repositories\License\AgentsRepository;
use App\Repositories\Reference\IslandsRepository;
use App\Repositories\License\SpeciesRepository;
use App\Models\License\SpeciesTracking;
use App\Models\License\SpeciesIslandQuota;
use App\Services\QuotaValidationService;
use App\Models\License\LicenseItem;
use App\Models\License\IslandQuotaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SpeciesTrackingController extends Controller
{
    protected $speciesTrackingRepository;
    protected $agentsRepository;
    protected $islandsRepository;
    protected $speciesRepository;
    protected $quotaValidationService;

    public function __construct(
        SpeciesTrackingRepository $speciesTrackingRepository,
        AgentsRepository $agentsRepository,
        IslandsRepository $islandsRepository,
        SpeciesRepository $speciesRepository,QuotaValidationService $quotaValidationService
    ) {
        $this->speciesTrackingRepository = $speciesTrackingRepository;
        $this->agentsRepository = $agentsRepository;
        $this->islandsRepository = $islandsRepository;
        $this->speciesRepository = $speciesRepository;
        $this->quotaValidationService = $quotaValidationService;
    }

    public function index()
    {
        return view('license.tracking.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->speciesTrackingRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function create()
    {
        $agents = $this->agentsRepository->pluck();
        $islands = $this->islandsRepository->pluck();
        $species = $this->speciesRepository->pluck();

        return view('license.tracking.create', compact('agents', 'islands', 'species'));
    }



    public function store(Request $request)
{
    try {
        DB::beginTransaction();

        Log::info('Incoming Request Data:', $request->all());

        // Validate request
        $data = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'island_id' => 'required|exists:islands,id',
            'year' => 'required|integer',
            'species' => 'required|array',
            'species.*.species_id' => 'required|exists:species,id',
        ]);

        // Log validated data
        Log::info('Validated Data:', $data);

        foreach ($request->species as $speciesData) {
            $speciesId = $speciesData['species_id'];

            // Fetch species_island_quota record
            Log::info('Fetching Species Island Quota for:', [
                'species_id' => $speciesId,
                'island_id' => $data['island_id'],
                'year' => $data['year'],
            ]);
            $speciesIslandQuota = SpeciesIslandQuota::where('species_id', $speciesId)
                ->where('island_id', $data['island_id'])
                ->where('year', $data['year'])
                ->first();

            if (!$speciesIslandQuota) {
                Log::error('No quota found for Species Island Quota:', [
                    'species_id' => $speciesId,
                    'island_id' => $data['island_id'],
                    'year' => $data['year'],
                ]);
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->with('error', "No quota found for Species ID {$speciesId}, Island ID {$data['island_id']}, and Year {$data['year']}.");
            }

            Log::info('Processing species:', [
                'species_id' => $speciesId,
                'species_island_quota_id' => $speciesIslandQuota->id,
            ]);

            // Check for existing record
            Log::info('Checking if tracking record exists for Species Island Quota:', [
                'species_island_quota_id' => $speciesIslandQuota->id,
                'agent_id' => $data['agent_id'],
            ]);
            $exists = SpeciesTracking::where('species_island_quota_id', $speciesIslandQuota->id)
                ->where('agent_id', $data['agent_id'])
                ->exists();

            if ($exists) {
                Log::error('Tracking record already exists for Species ID:', [
                    'species_id' => $speciesId,
                    'agent_id' => $data['agent_id'],
                ]);
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->with('error', "A tracking record already exists for Species ID {$speciesId}.");
            }

            // Create tracking record
            $trackingData = [
                'species_island_quota_id' => $speciesIslandQuota->id,
                'agent_id' => $data['agent_id'],
                'quota_used' => 0, // Initially no quota is used
                'remaining_quota' => $speciesIslandQuota->quota_allocated, // Initially equal to allocated quota
                'created_by' => auth()->id(),
            ];

            Log::info('Attempting to save tracking data:', $trackingData);

            // Create and store the record
            SpeciesTracking::create($trackingData);
        }

        DB::commit();

        Log::info('Tracking records created successfully.');

        return redirect()->route('license.tracking.index')
            ->with('success', 'Records created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Species tracking creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create records: ' . $e->getMessage());
    }
}

    public function edit($id)
    {
        $speciesTracking = $this->speciesTrackingRepository->getById($id);
        $agents = $this->agentsRepository->pluck();
        $islands = $this->islandsRepository->pluck();
        $species = $this->speciesRepository->pluck();

        return view('license.tracking.edit', compact('speciesTracking', 'agents', 'islands', 'species'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $data = $request->validate([
                'species_id' => 'required|exists:species,id',
                'agent_id' => 'required|exists:agents,id',
                'island_id' => 'required|exists:islands,id',
                'year' => 'required|integer',
                'quota_allocated' => 'required|numeric|min:0',
                'quota_used' => 'required|numeric|min:0',
                'remaining_quota' => 'required|numeric|min:0',
            ]);

            $data['updated_by'] = auth()->id();

            $this->speciesTrackingRepository->update($id, $data);

            DB::commit();

            return redirect()->route('license.tracking.index')
                ->with('success', 'Species tracking record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Species tracking update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update species tracking record: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $this->speciesTrackingRepository->deleteById($id);

            DB::commit();

            return response()->json(['message' => 'Species tracking record deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Species tracking deletion failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Failed to delete species tracking record'], 500);
        }
    }

    public function checkDuplicate(Request $request)
    {
        // Check if the combination of species_id, agent_id, island_id, and year already exists
        $exists = SpeciesTracking::where('species_id', $request->species_id)
            ->where('agent_id', $request->agent_id)
            ->where('island_id', $request->island_id)
            ->where('year', $request->year)
            ->exists();

        // Return a JSON response with the result
        return response()->json(['exists' => $exists]);
    }

    public function show($id)
    {
        $speciesTrackings = $this->speciesTrackingRepository->getById($id);

        if (!$speciesTrackings) {
            return response()->json(['message' => 'speciesTrackings not found'], Response::HTTP_NOT_FOUND);
        }

        return view('license.tracking.index', compact('speciesTrackings'));
    }
}
