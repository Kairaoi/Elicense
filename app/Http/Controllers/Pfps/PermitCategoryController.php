<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\PermitCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class PermitCategoryController extends Controller
{
    protected $permitCategoryRepository;

    /**
     * PermitCategoryController constructor.
     *
     * @param PermitCategoryRepository $permitCategoryRepository
     */
    public function __construct(PermitCategoryRepository $permitCategoryRepository)
    {
        $this->permitCategoryRepository = $permitCategoryRepository;
    }

    /**
     * Get DataTable of permit categories.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->permitCategoryRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of permit categories.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.permit-category.index');
    }

    /**
     * Show the form for creating a new permit category.
     *
     * @return Response
     */
    public function create()
    {
        return view('pfps.permit-category.create');
    }

    /**
     * Store a newly created permit category in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'category_name' => 'required|string|max:255|unique:permit_categories,category_name',
            'description' => 'nullable|string',
            'base_fee' => 'required|numeric|min:0',
            'requires_certification' => 'boolean',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->id();
        
        // Convert checkbox value to boolean
        $data['requires_certification'] = $request->has('requires_certification');

        // Save the permit category using the repository
        $permitCategory = $this->permitCategoryRepository->create($data);

        // Redirect with success message
        return redirect()->route('pfps.permit-categories.index')
            ->with('success', 'Permit category created successfully.');
    }

    /**
     * Display the specified permit category.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $permitCategory = $this->permitCategoryRepository->getById($id);

        if (!$permitCategory) {
            return response()->json(['message' => 'Permit category not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.permit-category.show', compact('permitCategory'));
    }

    /**
     * Show the form for editing the specified permit category.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $permitCategory = $this->permitCategoryRepository->getById($id);

        if (!$permitCategory) {
            return redirect()->route('pfps.permit-categories.index')
                ->with('error', 'Permit category not found.');
        }

        return view('pfps.permit-category.edit', compact('permitCategory'));
    }

    /**
     * Update the specified permit category in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'category_name' => 'required|string|max:255|unique:permit_categories,category_name,' . $id . ',category_id',
            'description' => 'nullable|string',
            'base_fee' => 'required|numeric|min:0',
            'requires_certification' => 'boolean',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();
        
        // Convert checkbox value to boolean
        $data['requires_certification'] = $request->has('requires_certification');

        // Attempt to update the permit category record
        $updated = $this->permitCategoryRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('pfps.permit-categories.index')
                ->with('error', 'Failed to update permit category.');
        }

        return redirect()->route('pfps.permit-categories.index')
            ->with('success', 'Permit category updated successfully.');
    }

    /**
     * Remove the specified permit category from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->permitCategoryRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(
                ['message' => 'Permit category not found or failed to delete'], 
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(['message' => 'Permit category deleted successfully']);
    }
}