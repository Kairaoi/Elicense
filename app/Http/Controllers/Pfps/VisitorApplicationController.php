<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\VisitorApplicationRepository;
use App\Repositories\Pfps\VisitorRepository;
use App\Repositories\Pfps\PermitCategoryRepository;
use App\Repositories\Pfps\ActivityTypeRepository;
use App\Repositories\Pfps\DurationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class VisitorApplicationController extends Controller
{
    protected $visitorApplicationRepository;
    protected $visitorRepository;
    protected $permitCategoryRepository;
    protected $activityTypeRepository;
    protected $durationRepository;

    /**
     * VisitorApplicationController constructor.
     */
    public function __construct(
        VisitorApplicationRepository $visitorApplicationRepository,
        VisitorRepository $visitorRepository,
        PermitCategoryRepository $permitCategoryRepository,
        ActivityTypeRepository $activityTypeRepository,
        DurationRepository $durationRepository
    ) {
        $this->visitorApplicationRepository = $visitorApplicationRepository;
        $this->visitorRepository = $visitorRepository;
        $this->permitCategoryRepository = $permitCategoryRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->durationRepository = $durationRepository;
    }

    /**
     * Get DataTable of applications.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->visitorApplicationRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }
    /**
     * Display a listing of applications.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.visitor-application.index');
    }

    /**
     * Show the form for creating a new application.
     *
     * @return Response
     */
    public function create()
    {
        $categories = $this->permitCategoryRepository->pluck();
        $durations = $this->durationRepository->pluck();
        $visitors = $this->visitorRepository->pluck();
        $activityTypes = $this->activityTypeRepository->pluck();
        
        return view('pfps.visitor-application.create', compact('categories', 'durations','visitors','activityTypes'));
    }

    /**
     * Store a newly created application in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'visitor_id' => 'required|exists:visitors,visitor_id',
            'category_id' => 'required|exists:permit_categories,category_id',
            'activity_type_id' => 'required|exists:activity_types,activity_type_id',
            'duration_id' => 'required|exists:durations,duration_id',
        ]);

        // Set additional data
        $data['status'] = 'pending';
        $data['application_date'] = now();
        $data['created_by'] = auth()->id();

        // Save the application using the repository
        $application = $this->visitorApplicationRepository->create($data);

        return redirect()->route('pfps.visitor-applications.index')
            ->with('success', 'Application submitted successfully.');
    }

    /**
     * Display the specified application.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $application = $this->visitorApplicationRepository->getById($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.visitor-application.show', compact('application'));
    }

    public function edit($id)
{
    // Fetch the visitor application using its ID
    $application = $this->visitorApplicationRepository->getById($id);

    if (!$application) {
        abort(404, 'Visitor application not found.');
    }

    // Fetch related data for dropdowns
    $categories = $this->permitCategoryRepository->pluck();
    $durations = $this->durationRepository->pluck();
    $visitors = $this->visitorRepository->pluck();
    $activityTypes = $this->activityTypeRepository->pluck();

    // Return the edit view with the application and dropdown data
    return view('pfps.visitor-application.edit', compact('application', 'categories', 'durations', 'visitors', 'activityTypes'));
}

public function update(Request $request, $id)
{
    // Validate request data
    $data = $request->validate([
        'visitor_id' => 'required|exists:visitors,visitor_id',
        'category_id' => 'required|exists:permit_categories,category_id',
        'activity_type_id' => 'required|exists:activity_types,activity_type_id',
        'duration_id' => 'required|exists:durations,duration_id',
        'status' => 'required|in:pending,approved,rejected',
        'rejection_reason' => 'nullable|string|max:255',
    ]);

    // Fetch the application using the repository
    $application = $this->visitorApplicationRepository->getById($id);

    if (!$application) {
        return redirect()->route('pfps.visitor-applications.index')
            ->with('error', 'Application not found.');
    }

    // Add additional data
    $data['updated_by'] = auth()->id();

    // Update the application
    $this->visitorApplicationRepository->update($id, $data);

    return redirect()->route('pfps.visitor-applications.index')
        ->with('success', 'Application updated successfully.');
}

    /**
     * Update application status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string'
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->visitorApplicationRepository->updateStatus($id, $data);

        if (!$updated) {
            return response()->json([
                'message' => 'Failed to update application status'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Application status updated successfully',
            'status' => $data['status']
        ]);
    }

    /**
     * Get activity types by category.
     *
     * @param int $categoryId
     * @return Response
     */
    public function getActivityTypes($categoryId)
    {
        $activityTypes = $this->activityTypeRepository->getByCategoryId($categoryId);
        return response()->json($activityTypes);
    }

    /**
     * Search visitor by passport.
     *
     * @param Request $request
     * @return Response
     */
    public function searchVisitor(Request $request)
    {
        $passport = $request->input('passport_number');
        $visitor = $this->visitorRepository->findByPassport($passport);
        return response()->json($visitor);
    }

    /**
     * Get status badge HTML.
     *
     * @param string $status
     * @return string
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'pending' => 'badge badge-warning',
            'approved' => 'badge badge-success',
            'rejected' => 'badge badge-danger'
        ];

        return sprintf(
            '<span class="%s">%s</span>',
            $badges[$status] ?? 'badge badge-secondary',
            ucfirst($status)
        );
    }

    /**
 * Remove the specified application from storage.
 *
 * @param int $id
 * @return Response
 */
public function destroy($id)
{
    // Attempt to delete the application using the repository
    $deleted = $this->visitorApplicationRepository->deleteById($id);

    // If deletion fails, return an error message
    if (!$deleted) {
        return response()->json(['message' => 'Failed to delete application'], Response::HTTP_BAD_REQUEST);
    }

    // If successful, return a success message
    return response()->json(['message' => 'Application deleted successfully']);
}

}