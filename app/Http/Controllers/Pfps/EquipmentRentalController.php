<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\EquipmentRentalRepository; // Assuming repository exists
use App\Repositories\Pfps\PermitRepository; // Assuming Permit model exists
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class EquipmentRentalController extends Controller
{
    protected $equipmentRentalRepository;
    protected $permitRepository;

    /**
     * EquipmentRentalController constructor.
     */
    public function __construct(EquipmentRentalRepository $equipmentRentalRepository, PermitRepository $permitRepository)
    {
        $this->equipmentRentalRepository = $equipmentRentalRepository;
        $this->permitRepository = $permitRepository;
    }

    /**
     * Get DataTable of equipment rentals.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->equipmentRentalRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of equipment rentals.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.equipment_rental.index');
    }

    /**
     * Show form for creating new equipment rental.
     *
     * @return Response
     */
    public function create()
    {
        // Assuming `Permit` model exists for permits
        $permits = $this->permitRepository->pluck();
        return view('pfps.equipment_rental.create', compact('permits'));
    }

    /**
     * Store a newly created equipment rental in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'permit_id' => 'required|exists:permits,permit_id',
            'equipment_type' => 'required|string|max:255',
            'rental_fee' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'rental_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:rental_date',
        ]);

        $data['created_by'] = auth()->id();

        $rental = $this->equipmentRentalRepository->create($data);

        return redirect()->route('pfps.equipment_rentals.index')->with('success', 'Equipment rental created successfully');
    }

    /**
     * Display the specified equipment rental.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $rental = $this->equipmentRentalRepository->getById($id);

        if (!$rental) {
            return response()->json(['message' => 'Equipment rental not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.equipment_rental.show', compact('rental'));
    }

    /**
     * Show the form for editing the specified equipment rental.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
{
    $equipmentRental = $this->equipmentRentalRepository->getById($id);

    if (!$equipmentRental) {
        return response()->json(['message' => 'Equipment rental not found'], Response::HTTP_NOT_FOUND);
    }

    $permits = $this->permitRepository->pluck();

    return view('pfps.equipment_rental.edit', compact('equipmentRental', 'permits'));
}

    /**
     * Update the specified equipment rental in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'permit_id' => 'required|exists:permits,permit_id',
            'equipment_type' => 'required|string|max:255',
            'rental_fee' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'rental_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:rental_date',
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->equipmentRentalRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update equipment rental'], Response::HTTP_BAD_REQUEST);
        }

        return redirect()->route('pfps.equipment_rentals.index')->with('success', 'Equipment rental updated successfully');
    }

    /**
     * Remove the specified equipment rental from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->equipmentRentalRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Failed to delete equipment rental'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['message' => 'Equipment rental deleted successfully']);
    }
}
