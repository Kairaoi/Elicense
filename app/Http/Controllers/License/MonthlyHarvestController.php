<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Models\License\MonthlyHarvest;
use App\Models\License\SpeciesTracking;
use App\Repositories\License\MonthlyHarvestRepository;
use App\Repositories\License\SpeciesTrackingRepository;
use App\Repositories\Reference\IslandsRepository;
use App\Models\License\LicenseItem;  // Add this import
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
    
        return view('license.monthly-harvest.create', compact(
            'applicants', 
            'months', 
            'years', 
            'islands'
        ));
    }
    
    
    public function getLicenseItems(Request $request)
{
    $applicantId = $request->query('applicant_id');
    $islandId = $request->query('island_id');
    
    if (!$applicantId || !$islandId) {
        return response()->json([
            'success' => false,
            'message' => 'Missing required parameters'
        ], 400);
    }

    try {
        $licenseItems = LicenseItem::with(['species', 'license'])
            ->whereHas('license', function ($query) use ($applicantId) {
                $query->where('applicant_id', $applicantId)
                      ->where('status', 'license_issued');
            })
            ->where('island_id', $islandId)
            ->get();

        // Check if any license items exist
        if ($licenseItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No license items found for this selection'
            ]);
        }

        // Map data properly
        $mappedItems = $licenseItems->map(function ($item) {
            return [
                'id' => $item->id,
                'species_name' => $item->species->name ?? 'N/A', 
                'requested_quota' => $item->requested_quota, 
                'remaining_quota' => $item->getRemainingQuota(),  
                'license_number' => $item->license->license_number ?? 'Unknown'
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $mappedItems
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Error fetching license items: ' . $e->getMessage()
        ], 500);
    }
}

    



public function store(Request $request)
{
    try {
        DB::beginTransaction();

        // Validate input data
        $validated = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'island_id' => 'required|exists:islands,id',
            'month' => 'required|integer|between:1,12',
            'year' => [
                'required',
                'integer',
                'min:' . (date('Y') - 2),
                'max:' . (date('Y') + 2)
            ],
            'license_items' => 'required|array|min:1',
            'license_items.*' => 'required|exists:license_items,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        // Get the applicant and check if they have access to the specified island
        $applicant = Applicant::findOrFail($validated['applicant_id']);
        $hasIslandAccess = $applicant->islands()
            ->where('islands.id', $validated['island_id'])
            ->exists();
        
        if (!$hasIslandAccess) {
            throw new \Exception('Applicant does not have access to the specified island.');
        }

        // Process each license item and quantity
        foreach ($validated['license_items'] as $index => $licenseItemId) {
            // Fetch the license item along with related data
            $licenseItem = LicenseItem::with(['species', 'license'])
                ->whereHas('license', function ($query) use ($validated) {
                    $query->where('applicant_id', $validated['applicant_id'])  // Ensure license belongs to the applicant
                        ->where('status', 'active');
                })
                ->where('id', $licenseItemId)
                ->where('island_id', $validated['island_id'])
                ->first();

            // Check if the license item is valid
            if (!$licenseItem) {
                throw new \Exception('Invalid license item selected.');
            }

            // Check if there's already a harvest record for this license item, month, and year
            $existingHarvest = MonthlyHarvest::where('license_item_id', $licenseItemId)
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->exists();

            if ($existingHarvest) {
                throw new \Exception("A harvest record already exists for {$licenseItem->species->name} in " . 
                    date('F', mktime(0, 0, 0, $validated['month'], 1)) . " {$validated['year']}");
            }

            // Calculate remaining quota and requested quantity
            $remainingQuota = $licenseItem->getRemainingQuota();  // Assuming this method exists to get the remaining quota
            $requestedQuantity = $validated['quantities'][$index];

            if ($requestedQuantity > $remainingQuota) {
                throw new \Exception("Requested quantity ({$requestedQuantity} kg) exceeds remaining quota ({$remainingQuota} kg) for {$licenseItem->species->name}");
            }

            // Create a new harvest record
            MonthlyHarvest::create([
                'license_item_id' => $licenseItemId,
                'applicant_id' => $validated['applicant_id'],
                'island_id' => $validated['island_id'],
                'year' => $validated['year'],
                'month' => $validated['month'],
                'quantity_harvested' => $requestedQuantity,
                'remaining_quota' => $remainingQuota - $requestedQuantity, // Update remaining quota
                'notes' => $validated['notes'],
                'created_by' => auth()->id()
            ]);

            // Update the license item's used quota
            $licenseItem->updateQuotas();  // Ensure this method is defined and works as expected

            // Update species tracking if needed
            $speciesIslandQuota = $licenseItem->species->islandQuotas()
                ->where('island_id', $validated['island_id'])
                ->where('year', $validated['year'])
                ->first();

            if ($speciesIslandQuota) {
                $speciesTracking = $speciesIslandQuota->tracking()
                    ->where('applicant_id', $validated['applicant_id'])
                    ->first();

                if ($speciesTracking) {
                    $speciesTracking->quota_used += $requestedQuantity;
                    $speciesTracking->remaining_quota -= $requestedQuantity;
                    $speciesTracking->save();
                }
            }
        }

        // Commit the transaction
        DB::commit();

        // Return success response
        return redirect()
            ->route('license.monthly-harvests.index')
            ->with('success', 'Monthly harvest records created successfully');

    } catch (\Exception $e) {
        // Rollback the transaction in case of error
        DB::rollBack();

        // Log error details
        Log::error('Monthly harvest creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);

        // Return error response
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $e->getMessage());
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