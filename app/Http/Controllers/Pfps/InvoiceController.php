<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\InvoiceRepository;
use App\Repositories\Pfps\VisitorApplicationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;
use PDF;

class InvoiceController extends Controller
{
    protected $invoiceRepository;
    protected $visitorApplicationRepository;

    /**
     * InvoiceController constructor.
     */
    public function __construct(
        InvoiceRepository $invoiceRepository,
        VisitorApplicationRepository $visitorApplicationRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->visitorApplicationRepository = $visitorApplicationRepository;
    }

    /**
     * Get DataTable of invoices.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->invoiceRepository->getForDataTable($search);
        
        return DataTables::of($query)
            ->addColumn('visitor_name', function ($invoice) {
                return $invoice->visitorApplication->visitor->full_name;
            })
            ->addColumn('permit_type', function ($invoice) {
                return $invoice->visitorApplication->permitCategory->category_name;
            })
            ->addColumn('status_badge', function ($invoice) {
                return $this->getStatusBadge($invoice->status);
            })
            ->addColumn('formatted_amount', function ($invoice) {
                return number_format($invoice->amount, 2);
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Display a listing of invoices.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.invoice.index');
    }

    /**
     * Generate invoice for an application.
     *
     * @param Request $request
     * @return Response
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'application_id' => 'required|exists:visitor_applications,application_id',
            'amount' => 'required|numeric|min:0',
        ]);

        $data['status'] = 'pending';
        $data['invoice_date'] = now();
        $data['created_by'] = auth()->id();

        $invoice = $this->invoiceRepository->create($data);

        return response()->json([
            'message' => 'Invoice generated successfully',
            'invoice_id' => $invoice->invoice_id
        ]);
    }

    /**
     * Display the specified invoice.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $invoice = $this->invoiceRepository->getById($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.invoice.show', compact('invoice'));
    }

    /**
     * Update payment status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updatePayment(Request $request, $id)
    {
        $data = $request->validate([
            'payment_reference' => 'required|string|max:255',
        ]);

        $data['status'] = 'paid';
        $data['updated_by'] = auth()->id();

        $updated = $this->invoiceRepository->updatePayment($id, $data);

        if (!$updated) {
            return response()->json([
                'message' => 'Failed to update payment status'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Payment recorded successfully'
        ]);
    }

    /**
     * Generate PDF invoice.
     *
     * @param int $id
     * @return Response
     */
    public function generatePDF($id)
    {
        $invoice = $this->invoiceRepository->getById($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        $pdf = PDF::loadView('pfps.invoice.pdf', compact('invoice'));
        
        return $pdf->download('invoice-' . $invoice->invoice_id . '.pdf');
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
            'paid' => 'badge badge-success'
        ];

        return sprintf(
            '<span class="%s">%s</span>',
            $badges[$status] ?? 'badge badge-secondary',
            ucfirst($status)
        );
    }
}