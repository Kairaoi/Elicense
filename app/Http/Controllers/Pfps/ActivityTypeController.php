<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\ActivityTypeRepository;
use App\Repositories\Pfps\PermitCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class ActivityTypeController extends Controller
{
    protected $activityTypeRepository;
    protected $permitCategoryRepository;

    /**
     * ActivityTypeController constructor.
     *
     * @param ActivityTypeRepository $activityTypeRepository
     * @param PermitCategoryRepository $permitCategoryRepository
     */
    public function __construct(
        ActivityTypeRepository $activityTypeRepository,
        PermitCategoryRepository $permitCategoryRepository
    ) {
        $this->activityTypeRepository = $activityTypeRepository;
        $this->permitCategoryRepository = $permitCategoryRepository;
    }

    /**
     * Get DataTable of activity types.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->activityTypeRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of activity types.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.activity-type.index');
    }

    /**
     * Show the form for creating a new activity type.
     *
     * @return Response
     */
    public function create()
    {
        $categories = $this->permitCategoryRepository->pluck();
        return view('pfps.activity-type.create', compact('categories'));
    }

    /**
     * Store a newly created activity type in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'category_id' => 'required|exists:permit_categories,category_id',
            'activity_name' => 'required|string|max:255|unique:activity_types,activity_name',
            'requirements' => 'nullable|string',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->id();

        // Save the activity type using the repository
        $activityType = $this->activityTypeRepository->create($data);

        // Redirect with success message
        return redirect()->route('pfps.activity-types.index')
            ->with('success', 'Activity type created successfully.');
    }

    /**
     * Display the specified activity type.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $activityType = $this->activityTypeRepository->getById($id);

        if (!$activityType) {
            return response()->json(['message' => 'Activity type not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.activity-type.show', compact('activityType'));
    }

    /**
     * Show the form for editing the specified activity type.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $activityType = $this->activityTypeRepository->getById($id);
        $categories = $this->permitCategoryRepository->getAll();

        if (!$activityType) {
            return redirect()->route('pfps.activity-types.index')
                ->with('error', 'Activity type not found.');
        }

        return view('pfps.activity-type.edit', compact('activityType', 'categories'));
    }

    /**
     * Update the specified activity type in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'category_id' => 'required|exists:permit_categories,category_id',
            'activity_name' => 'required|string|max:255|unique:activity_types,activity_name,' . $id . ',activity_type_id',
            'requirements' => 'nullable|string',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the activity type record
        $updated = $this->activityTypeRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('pfps.activity-types.index')
                ->with('error', 'Failed to update activity type.');
        }

        return redirect()->route('pfps.activity-types.index')
            ->with('success', 'Activity type updated successfully.');
    }

    /**
     * Remove the specified activity type from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->activityTypeRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(
                ['message' => 'Activity type not found or failed to delete'], 
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(['message' => 'Activity type deleted successfully']);
    }

    /**
     * Get activity types by category.
     *
     * @param int $categoryId
     * @return Response
     */
    public function getByCategory($categoryId)
    {
        $activityTypes = $this->activityTypeRepository->getByCategoryId($categoryId);
        return response()->json($activityTypes);
    }
}