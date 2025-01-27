<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\SpeciesTrackingRepository;
use App\Repositories\License\AgentsRepository;
use App\Repositories\Reference\IslandsRepository;
use App\Repositories\License\SpeciesRepository;
use App\Models\License\SpeciesTracking;
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
            'species.*.quota_allocated' => 'required|numeric|min:0',
        ]);

        $savedRecords = []; // Array to store created records

        foreach ($request->species as $speciesData) {
            $speciesId = $speciesData['species_id'];
            $quotaAllocated = $speciesData['quota_allocated'];

            Log::info('Processing species:', [
                'species_id' => $speciesId,
                'quota' => $quotaAllocated
            ]);

            // Check for existing record
            $exists = SpeciesTracking::where('species_id', $speciesId)
                ->where('agent_id', $data['agent_id'])
                ->where('island_id', $data['island_id'])
                ->where('year', $data['year'])
                ->exists();

            if ($exists) {
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->with('error', "E a kaman exist te tracking record ibukin Species ID {$speciesId}");
            }

            // Create tracking record
            $trackingData = [
                'species_id' => $speciesId,
                'agent_id' => $data['agent_id'],
                'island_id' => $data['island_id'],
                'year' => $data['year'],
                'quota_allocated' => $quotaAllocated,
                'quota_used' => 0,
                'remaining_quota' => $quotaAllocated,
                'created_by' => auth()->id(),
            ];

            // Log before saving
            Log::info('Attempting to save tracking data:', $trackingData);

            // Create and store the record
            $record = SpeciesTracking::create($trackingData);

            // Log after saving
            Log::info('Record created with ID: ' . $record->id);

            // Store created record
            $savedRecords[] = $record;
        }

        // Double check that records were saved
        foreach ($savedRecords as $record) {
            $dbRecord = SpeciesTracking::find($record->id);
            if (!$dbRecord) {
                DB::rollBack();
                Log::error('Failed to verify saved record:', ['id' => $record->id]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'E aki nakoraoi te kawakinan rongorongo.');
            }
        }

        DB::commit();

        // Log final confirmation
        Log::info('Successfully saved records:', [
            'count' => count($savedRecords),
            'records' => $savedRecords
        ]);

        return redirect()->route('license.tracking.index')
            ->with('success', 'E tia n rin raoi am rongorongo.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Species tracking creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()->back()
            ->withInput()
            ->with('error', 'E aki nakoraoi te kawakinan rongorongo: ' . $e->getMessage());
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
