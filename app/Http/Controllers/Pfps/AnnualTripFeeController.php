<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;
use App\Repositories\Pfps\AnnualTripFeeRepository;

class AnnualTripFeeController extends Controller
{
    protected $annualTripFeeRepository;

    /**
     * AnnualTripFeeController constructor.
     */
    public function __construct(AnnualTripFeeRepository $annualTripFeeRepository)
    {
        $this->annualTripFeeRepository = $annualTripFeeRepository;
    }

    /**
     * Get DataTable of annual trip fees.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->annualTripFeeRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of annual trip fees.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.annual_trip_fee.index');
    }

    /**
     * Show form for creating a new annual trip fee.
     *
     * @return Response
     */
    public function create()
    {
        return view('pfps.annual_trip_fee.create');
    }

    /**
     * Store a newly created annual trip fee in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'year' => 'required|integer|unique:annual_trip_fees,year',
        ]);

        $data['created_by'] = auth()->id();

        $fee = $this->annualTripFeeRepository->create($data);

        return redirect()->route('pfps.annual_trip_fees.index')->with('success', 'Annual trip fee created successfully');
    }

    /**
     * Display the specified annual trip fee.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $fee = $this->annualTripFeeRepository->getById($id);

        if (!$fee) {
            return response()->json(['message' => 'Annual trip fee not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.annual_trip_fee.show', compact('fee'));
    }

    /**
     * Show the form for editing the specified annual trip fee.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $fee = $this->annualTripFeeRepository->getById($id);

        if (!$fee) {
            return response()->json(['message' => 'Annual trip fee not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.annual_trip_fee.edit', compact('fee'));
    }

    /**
     * Update the specified annual trip fee in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'year' => 'required|integer|unique:annual_trip_fees,year,' . $id . ',fee_id',
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->annualTripFeeRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update annual trip fee'], Response::HTTP_BAD_REQUEST);
        }

        return redirect()->route('pfps.annual_trip_fees.index')->with('success', 'Annual trip fee updated successfully');
    }

    /**
     * Remove the specified annual trip fee from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->annualTripFeeRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Failed to delete annual trip fee'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['message' => 'Annual trip fee deleted successfully']);
    }
}
