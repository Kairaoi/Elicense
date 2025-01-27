<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\AgentsRepository;
use App\Repositories\License\ApplicantsRepository;
use App\Repositories\Reference\IslandsRepository; 
use App\Models\License\Agent;
use App\Repositories\License\SpeciesTrackingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AgentsController extends Controller
{
    protected $agentsRepository;
    protected $applicantsRepository;
    protected $islandsRepository; // Add this
    protected $speciesTrackingRepo;

    public function __construct(
        AgentsRepository $agentsRepository, 
        ApplicantsRepository $applicantsRepository,
        IslandsRepository $islandsRepository, SpeciesTrackingRepository $speciesTrackingRepo
    ) {
        $this->agentsRepository = $agentsRepository;
        $this->applicantsRepository = $applicantsRepository;
        $this->islandsRepository = $islandsRepository; // Add this
        $this->speciesTrackingRepo = $speciesTrackingRepo;
    }

    public function index()
    {
        return view('license.agent.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->agentsRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }
    public function create()
    {
        $applicants = $this->applicantsRepository->pluck();
        $islands = $this->islandsRepository->pluck(); 
        // dd($islands);
        
        return view('license.agent.create', compact('applicants', 'islands'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate the request data
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email|unique:agents,email',
                'applicant_id' => 'required|exists:applicants,id',
                'status' => 'required|in:active,inactive',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'notes' => 'nullable|string',
                'islands' => 'required|array|min:1', // Add validation for islands
                'islands.*' => 'exists:islands,id'
            ]);

            // Add created_by
            $data['created_by'] = auth()->id();

            // Create the agent
            $agent = $this->agentsRepository->create($data);

            // Attach islands
            if (!empty($data['islands'])) {
                $agent->islands()->attach($data['islands'], [
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();

            return redirect()->route('license.agents.index')
                ->with('success', 'Agent created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create agent: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $agent = $this->agentsRepository->getById($id);
        $applicants = $this->applicantsRepository->pluck();
        $islands = $this->islandsRepository->pluck(); // Add this
        
        return view('license.agent.edit', compact('agent', 'applicants', 'islands'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $agent = $this->agentsRepository->getById($id);

            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email|unique:agents,email,' . $id,
                'applicant_id' => 'required|exists:applicants,id',
                'status' => 'required|in:active,inactive',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'notes' => 'nullable|string',
                'islands' => 'required|array|min:1', // Add validation for islands
                'islands.*' => 'exists:islands,id'
            ]);

            $data['updated_by'] = auth()->id();

            // Update agent
            $this->agentsRepository->update($id, $data);

            // Sync islands
            $agent->islands()->sync($data['islands'], [
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            return redirect()->route('license.agents.index')
                ->with('success', 'Agent updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update agent: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $agent = $this->agentsRepository->getById($id);
        return view('license.agent.show', compact('agent'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $agent = $this->agentsRepository->getById($id);

            if (!$agent) {
                return response()->json(['message' => 'Agent not found'], 404);
            }

            // Detach all islands first
            $agent->islands()->detach();
            
            // Delete the agent
            $this->agentsRepository->deleteById($id);

            DB::commit();

            return response()->json(['message' => 'Agent deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent deletion failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete agent'], 500);
        }
    }

    public function getSpeciesByAgent($agentId)
{
    try {
        Log::debug('Fetching species for agent ID:', ['agentId' => $agentId]);

        $species = Species::where('agent_id', $agentId)->get();

        Log::debug('Species data:', ['species' => $species]);

        return response()->json($species, 200);
    } catch (\Exception $e) {
        Log::error('Error fetching species:', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'Failed to fetch species.'
        ], 500);
    }
}
    
}