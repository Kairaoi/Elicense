<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\PermitRepository;
use App\Repositories\Pfps\VisitorApplicationRepository;
use App\Repositories\Pfps\InvoiceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;
use PDF;

class PermitController extends Controller
{
    protected $permitRepository;
    protected $visitorApplicationRepository;
    protected $invoiceRepository;

    /**
     * PermitController constructor.
     */
    public function __construct(
        PermitRepository $permitRepository,
        VisitorApplicationRepository $visitorApplicationRepository,
        InvoiceRepository $invoiceRepository
    ) {
        $this->permitRepository = $permitRepository;
        $this->visitorApplicationRepository = $visitorApplicationRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Get DataTable of permits.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->permitRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of permits.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.permit.index');
    }

    /**
     * Generate new permit.
     *
     * @param Request $request
     * @return Response
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'application_id' => 'required|exists:visitor_applications,application_id',
            'invoice_id' => 'required|exists:invoices,invoice_id',
            'permit_type' => 'required|in:printed,e-copy',
            'special_conditions' => 'nullable|string',
        ]);

        // Verify if invoice is paid
        $invoice = $this->invoiceRepository->getById($data['invoice_id']);
        if ($invoice->status !== 'paid') {
            return response()->json([
                'message' => 'Cannot generate permit for unpaid invoice'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Generate unique permit number
        $data['permit_number'] = $this->generatePermitNumber();
        $data['issue_date'] = now();
        
        // Calculate expiry date based on duration
        $application = $this->visitorApplicationRepository->getById($data['application_id']);
        $data['expiry_date'] = $this->calculateExpiryDate($application);
        
        $data['created_by'] = auth()->id();
        $data['is_active'] = true;

        $permit = $this->permitRepository->create($data);

        return response()->json([
            'message' => 'Permit generated successfully',
            'permit_id' => $permit->permit_id
        ]);
    }

    /**
     * Display the specified permit.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $permit = $this->permitRepository->getById($id);

        if (!$permit) {
            return response()->json(['message' => 'Permit not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.permit.show', compact('permit'));
    }

    /**
     * Update permit status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->permitRepository->updateStatus($id, $data);

        if (!$updated) {
            return response()->json([
                'message' => 'Failed to update permit status'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Permit status updated successfully'
        ]);
    }

    /**
     * Generate PDF permit.
     *
     * @param int $id
     * @return Response
     */
    public function generatePDF($id)
    {
        $permit = $this->permitRepository->getById($id);

        if (!$permit) {
            return response()->json(['message' => 'Permit not found'], Response::HTTP_NOT_FOUND);
        }

        $pdf = PDF::loadView('pfps.permit.pdf', compact('permit'));
        
        return $pdf->download('permit-' . $permit->permit_number . '.pdf');
    }

    /**
     * Verify permit by number.
     *
     * @param Request $request
     * @return Response
     */
    public function verify(Request $request)
    {
        $permitNumber = $request->input('permit_number');
        $permit = $this->permitRepository->findByNumber($permitNumber);

        if (!$permit) {
            return response()->json([
                'message' => 'Invalid permit number'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'valid' => $permit->is_active && $permit->expiry_date >= now(),
            'permit' => $permit
        ]);
    }

    /**
     * Generate unique permit number.
     *
     * @return string
     */
    private function generatePermitNumber()
    {
        $prefix = 'PER';
        $year = date('Y');
        $random = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return $prefix . $year . $random;
    }

    /**
     * Calculate permit expiry date.
     *
     * @param object $application
     * @return \Carbon\Carbon
     */
    private function calculateExpiryDate($application)
    {
        return now()->addDays($application->duration->days);
    }

    /**
     * Get status badge HTML.
     *
     * @param bool $isActive
     * @return string
     */
    private function getStatusBadge($isActive)
    {
        return $isActive 
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-danger">Inactive</span>';
    }

    /**
     * Get permit type badge HTML.
     *
     * @param string $type
     * @return string
     */
    private function getPermitTypeBadge($type)
    {
        $badges = [
            'printed' => 'badge badge-primary',
            'e-copy' => 'badge badge-info'
        ];

        return sprintf(
            '<span class="%s">%s</span>',
            $badges[$type] ?? 'badge badge-secondary',
            ucfirst($type)
        );
    }
}