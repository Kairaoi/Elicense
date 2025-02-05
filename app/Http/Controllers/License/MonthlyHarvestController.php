<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Models\License\MonthlyHarvest;
use App\Models\License\SpeciesTracking;
use App\Repositories\License\MonthlyHarvestRepository;
use App\Repositories\License\SpeciesTrackingRepository;
use App\Repositories\Reference\IslandsRepository;
use App\Models\License\LicenseItem;  // Add this import
use App\Models\License\LicenseType;  
use App\Models\License\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\License\ApplicantsRepository;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class MonthlyHarvestController extends Controller
{
    protected $monthlyHarvestRepository;
    protected $speciesTrackingRepository;
    protected $applicantsRepository;


    public function __construct(IslandsRepository $islandsRepository, ApplicantsRepository $applicantsRepository, 
    MonthlyHarvestRepository $monthlyHarvestRepository,
    SpeciesTrackingRepository $speciesTrackingRepository // <-- Update the parameter name
) {
    $this->monthlyHarvestRepository = $monthlyHarvestRepository;
    $this->speciesTrackingRepository = $speciesTrackingRepository; // <-- Correctly assign to the right property
    $this->applicantsRepository = $applicantsRepository;
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
        $applicants = $this->applicantsRepository->pluck();
        $islands = $this->islandsRepository->pluck();
        
        // Get all license types for the dropdown
        $licenseTypes = LicenseType::pluck('name', 'id');
        
        $months = array_combine(range(1, 12), array_map(function($m) {
            return date('F', mktime(0, 0, 0, $m, 1));
        }, range(1, 12)));
        
        $currentYear = date('Y');
        $years = array_combine(
            range($currentYear - 2, $currentYear + 2),
            range($currentYear - 2, $currentYear + 2)
        );
    
        return view('license.monthly-harvest.create', compact(
            'applicants', 
            'months', 
            'years', 
            'islands',
            'licenseTypes'
        ));
    }
    
    public function getLicenseItems(Request $request)
    {
        $applicantId = $request->query('applicant_id');
        $islandId = $request->query('island_id');
        $licenseTypeId = $request->query('license_type_id');
        
        if (!$applicantId || !$islandId || !$licenseTypeId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameters'
            ], 400);
        }

        try {
            // Get license items based on the license type and applicant
            $licenseItems = LicenseItem::with(['species', 'license'])
                ->whereHas('license', function ($query) use ($applicantId, $licenseTypeId) {
                    $query->where('applicant_id', $applicantId)
                          ->where('license_type_id', $licenseTypeId)
                          ->where('status', 'license_issued');
                })
                ->whereHas('species', function ($query) use ($licenseTypeId) {
                    $query->where('license_type_id', $licenseTypeId);
                })
                ->where('island_id', $islandId)
                ->get();

            if ($licenseItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No license items found for this selection'
                ]);
            }

            $mappedItems = $licenseItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'species_name' => $item->species->name ?? 'N/A',
                    'requested_quota' => $item->requested_quota,
                    'remaining_quota' => $item->getRemainingQuota(),
                    'license_number' => $item->license->license_number ?? 'Unknown',
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price
                ];
            });

            return response()->json([
                'success' => true,
                'items' => $mappedItems
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getLicenseItems: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching license items: ' . $e->getMessage()
            ], 500);
        }
    }

public function store(Request $request)
{
    Log::info('Request data:', $request->all());

    // Validate the incoming request
    $request->validate([
        'applicant_id' => 'required|exists:applicants,id',
        'island_id' => 'required|exists:islands,id',
        'year' => 'required|integer',
        'month' => 'required|integer|between:1,12',
        'harvested_quantity' => 'required|array',
        'harvested_quantity.*' => 'required|numeric|min:0',
        'license_item_id' => 'required|array',
        'license_item_id.*' => 'required|exists:license_items,id',
    ]);

    try {
        DB::beginTransaction();
        
        // First, delete any existing records for this applicant/island/year/month combination
        MonthlyHarvest::where('applicant_id', $request->applicant_id)
            ->where('island_id', $request->island_id)
            ->where('year', $request->year)
            ->where('month', $request->month)
            ->delete();
        
        foreach ($request->harvested_quantity as $itemId => $quantity) {
            // Ensure license_item_id exists for this entry
            if (!isset($request->license_item_id[$itemId])) {
                throw new \Exception("Missing license item ID for item {$itemId}");
            }

            $licenseItemId = $request->license_item_id[$itemId];
            
            // Get the license item to check requested quota
            $licenseItem = LicenseItem::findOrFail($licenseItemId);
            
            // Calculate total harvested quantity for this license item
            $totalHarvested = MonthlyHarvest::where('license_item_id', $licenseItemId)
                ->where(function($query) use ($request) {
                    $query->where('year', '<', $request->year)
                        ->orWhere(function($q) use ($request) {
                            $q->where('year', $request->year)
                                ->where('month', '<=', $request->month);
                        });
                })
                ->sum('quantity_harvested');
            
            // Add current harvest quantity
            $totalHarvested += $quantity;
            
            // Calculate remaining quota
            $remainingQuota = $licenseItem->requested_quota - $totalHarvested;
            
            // Optional: Check if harvest exceeds quota
            if ($remainingQuota < 0) {
                throw new \Exception("Harvest quantity exceeds remaining quota for license item {$licenseItemId}");
            }
            
            // Create the monthly harvest record
            MonthlyHarvest::create([
                'applicant_id' => $request->applicant_id,
                'island_id' => $request->island_id,
                'year' => $request->year,
                'month' => $request->month,
                'quantity_harvested' => $quantity,
                'license_item_id' => $licenseItemId,
                'remaining_quota' => $remainingQuota,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        DB::commit();
        Log::info('Monthly harvest recorded successfully');

        return redirect()
            ->route('license.monthly-harvests.index')
            ->with('success', 'Monthly harvest recorded successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to store monthly harvest: ' . $e->getMessage());
        
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Failed to record monthly harvest: ' . $e->getMessage());
    }
}

    
public function edit($id)
{
    $harvest = MonthlyHarvest::findOrFail($id);
    $applicants = $this->applicantsRepository->pluck();
    $islands = $this->islandsRepository->pluck();
    
    // Generate months for the view (from 1 to 12)
    $months = array_combine(range(1, 12), array_map(function($m) {
        return date('F', mktime(0, 0, 0, $m, 1));
    }, range(1, 12)));
    
    // Generate years for the view (2 years before and 2 years after the current year)
    $currentYear = date('Y');
    $years = array_combine(
        range($currentYear - 2, $currentYear + 2),
        range($currentYear - 2, $currentYear + 2)
    );
    
    return view('license.monthly-harvest.edit', compact(
        'harvest',
        'applicants', 
        'months', 
        'years',
        'islands'
    ));
}

public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all());

    // Validate the incoming request
    $request->validate([
        'applicant_id' => 'required|exists:applicants,id',
        'island_id' => 'required|exists:islands,id',
        'year' => 'required|integer',
        'month' => 'required|integer|between:1,12',
        'harvested_quantity' => 'required|array',
        'harvested_quantity.*' => 'required|numeric|min:0',
        'license_item_id' => 'required|array',
        'license_item_id.*' => 'required|exists:license_items,id',
    ]);

    try {
        DB::beginTransaction();

        // Delete existing harvest records for this month/year/applicant combination
        MonthlyHarvest::where('id', $id)->delete();

        foreach ($request->harvested_quantity as $itemId => $quantity) {
            // Ensure license_item_id exists for this entry
            if (!isset($request->license_item_id[$itemId])) {
                throw new \Exception("Missing license item ID for item {$itemId}");
            }

            $licenseItemId = $request->license_item_id[$itemId];
            
            // Create the monthly harvest record
            MonthlyHarvest::create([
                'applicant_id' => $request->applicant_id,
                'island_id' => $request->island_id,
                'year' => $request->year,
                'month' => $request->month,
                'quantity_harvested' => $quantity,
                'license_item_id' => $licenseItemId,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        DB::commit();
        Log::info('Monthly harvest updated successfully');

        return redirect()
            ->route('license.monthly-harvests.index')
            ->with('success', 'Monthly harvest updated successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to update monthly harvest: ' . $e->getMessage());
        
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Failed to update monthly harvest: ' . $e->getMessage());
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

        // dd($monthlyHarvest);

        if (!$monthlyHarvest) {
            return response()->json(['message' => 'monthlyHarvest not found'], Response::HTTP_NOT_FOUND);
        }

        return view('license.monthly-harvest.show', compact('monthlyHarvest'));
    }
}