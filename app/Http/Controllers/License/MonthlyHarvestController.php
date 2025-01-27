<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Models\License\MonthlyHarvest;
use App\Models\License\SpeciesTracking;
use App\Repositories\License\MonthlyHarvestRepository;
use App\Repositories\License\SpeciesTrackingRepository;
use App\Repositories\Reference\IslandsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\License\AgentsRepository;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class MonthlyHarvestController extends Controller
{
    protected $monthlyHarvestRepository;
    protected $speciesTrackingRepository;
    protected $agentsRepository;


    public function __construct(IslandsRepository $islandsRepository, AgentsRepository $agentsRepository, 
    MonthlyHarvestRepository $monthlyHarvestRepository,
    SpeciesTrackingRepository $speciesTrackingRepository // <-- Update the parameter name
) {
    $this->monthlyHarvestRepository = $monthlyHarvestRepository;
    $this->speciesTrackingRepository = $speciesTrackingRepository; // <-- Correctly assign to the right property
    $this->agentsRepository = $agentsRepository;
    $this->islandsRepository = $islandsRepository;
}


    public function index()
    {
        return view('license.monthly-harvest.index');
    }

    public function getDataTables(Request $request, SpeciesTracking $speciesTracking)
    {
        $search = $request->input('search.value', '');
        $query = $this->monthlyHarvestRepository->getForDataTable($search, $speciesTracking->id);
        return DataTables::of($query)->make(true);
    }

    public function create()
    {
        $agents = $this->agentsRepository->pluck();
        $islands = $this->islandsRepository->pluck();
        
        $months = array_combine(range(1, 12), array_map(function($m) {
            return date('F', mktime(0, 0, 0, $m, 1));
        }, range(1, 12)));
        
        // Add years (current year - 2 to current year + 2)
        $currentYear = date('Y');
        $years = array_combine(
            range($currentYear - 2, $currentYear + 2),
            range($currentYear - 2, $currentYear + 2)
        );
    
        // Get selected agent and island
        $selectedAgentId = old('agent_id');
        $selectedIslandId = old('island_id');
        
        // Initialize speciesTrackings variable
        $speciesTrackings = [];
        
        if ($selectedAgentId && $selectedIslandId) {
            // Fetch species tracking based on selected agent and island
            $speciesTrackings = $this->speciesTrackingRepository->pluck($selectedAgentId, $selectedIslandId);
        } else {
            // Optional: Provide a fallback when no agent or island is selected
            $speciesTrackings = $this->speciesTrackingRepository->pluck(); // Adjust this to return default species data
        }
    
        // Debugging line to check what is being returned
        // dd($speciesTrackings);  // Check what data is returned
    
        // Return the view with data
        return view('license.monthly-harvest.create', compact('agents', 'months', 'speciesTrackings', 'years', 'islands'));
    }
    
    
    
    // Add this new method in the same controller for the AJAX request
    public function getSpecies(Request $request)
    {
        $agentId = $request->query('agent_id');
        $islandId = $request->query('island_id');
    
        // Validate input
        if (!$agentId || !$islandId) {
            return response()->json(['species' => []], 400);
        }
    
        try {
            // Fetch species tracking data with proper joins
            $speciesTrackingData = DB::table('species_tracking')
                ->join('species', 'species_tracking.species_id', '=', 'species.id')
                ->where('species_tracking.agent_id', $agentId)
                ->where('species_tracking.island_id', $islandId)
                ->where('species_tracking.year', date('Y')) // Current year
                ->whereNull('species_tracking.deleted_at')
                ->select([
                    'species_tracking.id',
                    'species.name',
                    'species_tracking.quota_allocated',
                    'species_tracking.quota_used',
                    'species_tracking.remaining_quota'
                ])
                ->get();
    
            return response()->json([
                'success' => true,
                'species' => $speciesTrackingData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching species: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching species data'], 500);
        }
    }
    

    public function store(Request $request)
{
    Log::info('Form data', $request->all());
    Log::info('Species IDs from request:', $request->get('species'));

    try {
        DB::beginTransaction();

        // Validation step
        $data = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'island_id' => 'required|exists:islands,id',
            'year' => 'required|integer|min:2000|max:2099',
            'month' => 'required|integer|between:1,12',
            'species' => 'required|array|distinct',
            'species.*' => [
                'required',
                Rule::exists('species_tracking', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at'); // Include if soft deletes are used
                }),
            ],
            'quantities' => 'required|array',
            'quantities.*' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Log validated data
        Log::info('Validated data', $data);

        // Process the data (species harvesting and tracking update)
        foreach ($data['species'] as $index => $speciesId) {
            $quantityHarvested = $data['quantities'][$index];
            Log::info("Processing species ID: $speciesId with quantity: $quantityHarvested");

            // Create harvest record
            $harvest = MonthlyHarvest::create([
                'species_tracking_id' => $speciesId,
                'agent_id' => $data['agent_id'],
                'island_id' => $data['island_id'],
                'year' => $data['year'],
                'month' => $data['month'],
                'quantity_harvested' => $quantityHarvested,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            Log::info('Created harvest record', $harvest->toArray());

            // Update species tracking quotas
            $speciesTracking = SpeciesTracking::find($speciesId);
            $speciesTracking->quota_used += $quantityHarvested;
            $speciesTracking->remaining_quota = $speciesTracking->quota_allocated - $speciesTracking->quota_used;
            $speciesTracking->save();

            Log::info('Updated species tracking', $speciesTracking->toArray());
        }

        DB::commit();

        return redirect()->route('license.monthly-harvests.index')
            ->with('success', 'Harvest records have been successfully saved.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Log specific validation error details
        Log::error('Validation failed for species', [
            'error' => $e->getMessage(),
            'validation_errors' => print_r($e->errors(), true) // Log errors as string representation
        ]);

        // Prepare a user-friendly message for validation errors
        $errorMessages = [];
        foreach ($e->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $errorMessages[] = "$field: $message";
            }
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'There were validation errors: ' . implode(', ', $errorMessages));
    } catch (\Exception $e) {
        DB::rollBack();
        // Log general exception error details
        Log::error('Monthly harvest creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()->back()
            ->withInput()
            ->with('error', 'An error occurred while saving harvest records: ' . $e->getMessage());
    }
}



    
    
    public function edit(SpeciesTracking $speciesTracking, MonthlyHarvest $monthlyHarvest)
    {
        $months = array_combine(range(1, 12), array_map(function($m) {
            return date('F', mktime(0, 0, 0, $m, 1));
        }, range(1, 12)));

        return view('license.monthly-harvest.edit', compact('speciesTracking', 'monthlyHarvest', 'months'));
    }

    public function update(Request $request, SpeciesTracking $speciesTracking, MonthlyHarvest $monthlyHarvest)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $data = $request->validate([
                'month' => [
                    'required',
                    'integer',
                    'between:1,12',
                    function ($attribute, $value, $fail) use ($speciesTracking, $monthlyHarvest) {
                        // Check if month exists but exclude current record
                        if (MonthlyHarvest::where('species_tracking_id', $speciesTracking->id)
                            ->where('month', $value)
                            ->where('id', '!=', $monthlyHarvest->id)
                            ->exists()) {
                            $fail('A harvest record already exists for this month.');
                        }
                    },
                ],
                'quantity_harvested' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:1000'
            ]);

            // Calculate quota difference
            $quotaDiff = $data['quantity_harvested'] - $monthlyHarvest->quantity_harvested;
            
            // Check if new quantity would exceed remaining quota
            if ($quotaDiff > $speciesTracking->remaining_quota) {
                throw new \Exception('New harvest quantity would exceed remaining quota');
            }

            // Update harvest record
            $data['updated_by'] = auth()->id();
            $this->monthlyHarvestRepository->update($monthlyHarvest->id, $data);

            // Update tracking quotas
            $speciesTracking->updateQuotas();

            DB::commit();

            return redirect()->route('license.trackings.show', $speciesTracking)
                ->with('success', 'Monthly harvest updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Monthly harvest update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(SpeciesTracking $speciesTracking, MonthlyHarvest $monthlyHarvest)
    {
        try {
            DB::beginTransaction();

            // Delete the harvest record
            $this->monthlyHarvestRepository->deleteById($monthlyHarvest->id);

            // Update tracking quotas
            $speciesTracking->updateQuotas();

            DB::commit();

            return response()->json(['message' => 'Monthly harvest deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Monthly harvest deletion failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Failed to delete monthly harvest'], 500);
        }
    }

    public function show($id)
    {
        $monthlyHarvest = $this->monthlyHarvestRepository->getById($id);

        if (!$monthlyHarvest) {
            return response()->json(['message' => 'monthlyHarvest not found'], Response::HTTP_NOT_FOUND);
        }

        return view('license.monthly-harvest.index', compact('monthlyHarvest'));
    }
}