<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;

use App\Repositories\Pfps\ActivitySiteRepository;
use App\Repositories\Pfps\PermitCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class ActivitySiteController extends Controller
{
    protected $activitySiteRepository;
    protected $permitCategoryRepository;

    /**
     * ActivitySiteController constructor.
     */
    public function __construct(ActivitySiteRepository $activitySiteRepository, PermitCategoryRepository $permitCategoryRepository)
    {
        $this->activitySiteRepository = $activitySiteRepository;
        $this->permitCategoryRepository = $permitCategoryRepository;
    }

    /**
     * Get DataTable of activity sites.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->activitySiteRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of activity sites.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.activity_site.index');
    }

    /**
     * Show form for creating new activity site.
     *
     * @return Response
     */
    public function create()
    {
        // Assuming `PermitCategory` model exists for categories
        $categories = $this->permitCategoryRepository->pluck();
        return view('pfps.activity_site.create', compact('categories'));
    }

    /**
     * Store a newly created activity site in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:255',
            'category_id' => 'required|exists:permit_categories,category_id',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        
        $site = $this->activitySiteRepository->create($data);

        return redirect()->route('pfps.activity_sites.index')->with('success', 'Activity site created successfully');
    }

    /**
     * Display the specified activity site.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $site = $this->activitySiteRepository->getById($id);

        if (!$site) {
            return response()->json(['message' => 'Activity site not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.activity_site.show', compact('site'));
    }

    /**
     * Show the form for editing the specified activity site.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $site = $this->activitySiteRepository->getById($id);

        if (!$site) {
            return response()->json(['message' => 'Activity site not found'], Response::HTTP_NOT_FOUND);
        }

        // Assuming `PermitCategory` model exists for categories
        $categories = $this->permitCategoryRepository->pluck();
        return view('pfps.activity_site.edit', compact('site', 'categories'));
    }

    /**
     * Update the specified activity site in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:255',
            'category_id' => 'required|exists:permit_categories,category_id',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->activitySiteRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update activity site'], Response::HTTP_BAD_REQUEST);
        }

        return redirect()->route('pfps.activity_sites.index')->with('success', 'Activity site updated successfully');
    }

    /**
     * Remove the specified activity site from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->activitySiteRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Failed to delete activity site'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['message' => 'Activity site deleted successfully']);
    }
}
