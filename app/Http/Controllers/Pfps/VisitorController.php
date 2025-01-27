<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\VisitorRepository;
use App\Repositories\Pfps\CountryRepository;
use App\Repositories\Pfps\OrganizationRepository;
use App\Repositories\Pfps\LodgeRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class VisitorController extends Controller
{
    protected $visitorRepository;
    protected $countryRepository;
    protected $organizationRepository;
    protected $lodgeRepository;

    /**
     * VisitorController constructor.
     */
    public function __construct(
        VisitorRepository $visitorRepository,
        CountryRepository $countryRepository,
        OrganizationRepository $organizationRepository,
        LodgeRepository $lodgeRepository
    ) {
        $this->visitorRepository = $visitorRepository;
        $this->countryRepository = $countryRepository;
        $this->organizationRepository = $organizationRepository;
        $this->lodgeRepository = $lodgeRepository;
    }

    /**
     * Get DataTable of visitors.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->visitorRepository->getForDataTable($search);
        return DataTables::of($query)
            ->addColumn('country_name', function ($visitor) {
                return $visitor->country->name;
            })
            ->addColumn('organization_name', function ($visitor) {
                return $visitor->organization ? $visitor->organization->organization_name : 'N/A';
            })
            ->addColumn('lodge_name', function ($visitor) {
                return $visitor->lodge->lodge_name;
            })
            ->make(true);
    }

    /**
     * Display a listing of visitors.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.visitor.index');
    }

    /**
     * Show the form for creating a new visitor.
     *
     * @return Response
     */
    public function create()
    {
        $countries = $this->countryRepository->pluck();
        $organizations = $this->organizationRepository->pluck();
        $lodges = $this->lodgeRepository->pluck();
        
        return view('pfps.visitor.create', compact('countries', 'organizations', 'lodges'));
    }

    /**
     * Store a newly created visitor in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // Validate request data
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'passport_number' => 'required|string|max:50|unique:visitors,passport_number',
            'country_id' => 'required|exists:countries,country_id',
            'organization_id' => 'nullable|exists:organizations,organization_id',
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after:arrival_date',
            'lodge_id' => 'required|exists:lodges,lodge_id',
            'emergency_contact' => 'nullable|string|max:255',
            'certification_number' => 'nullable|string|max:255',
            'certification_type' => 'nullable|string|max:255',
            'certification_expiry' => 'nullable|date|after:today',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->id();

        // Save the visitor using the repository
        $visitor = $this->visitorRepository->create($data);

        // Redirect with success message
        return redirect()->route('pfps.visitors.index')
            ->with('success', 'Visitor registered successfully.');
    }

    /**
     * Display the specified visitor.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $visitor = $this->visitorRepository->getById($id);

        if (!$visitor) {
            return response()->json(['message' => 'Visitor not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.visitor.show', compact('visitor'));
    }

    /**
     * Show the form for editing the specified visitor.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $visitor = $this->visitorRepository->getById($id);
        $countries = $this->countryRepository->pluck();
        $organizations = $this->organizationRepository->pluck();
        $lodges = $this->lodgeRepository->pluck();

        if (!$visitor) {
            return redirect()->route('pfps.visitors.index')
                ->with('error', 'Visitor not found.');
        }

        return view('pfps.visitor.edit', compact('visitor', 'countries', 'organizations', 'lodges'));
    }

    /**
     * Update the specified visitor in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'passport_number' => 'required|string|max:50|unique:visitors,passport_number,' . $id . ',visitor_id',
            'country_id' => 'required|exists:countries,country_id',
            'organization_id' => 'nullable|exists:organizations,organization_id',
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after:arrival_date',
            'lodge_id' => 'required|exists:lodges,lodge_id',
            'emergency_contact' => 'nullable|string|max:255',
            'certification_number' => 'nullable|string|max:255',
            'certification_type' => 'nullable|string|max:255',
            'certification_expiry' => 'nullable|date|after:today',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the visitor record
        $updated = $this->visitorRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('pfps.visitors.index')
                ->with('error', 'Failed to update visitor.');
        }

        return redirect()->route('pfps.visitors.index')
            ->with('success', 'Visitor updated successfully.');
    }

    /**
     * Remove the specified visitor from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->visitorRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(
                ['message' => 'Visitor not found or failed to delete'], 
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(['message' => 'Visitor deleted successfully']);
    }

    /**
     * Search visitors by passport number.
     *
     * @param Request $request
     * @return Response
     */
    public function searchByPassport(Request $request)
    {
        $passportNumber = $request->input('passport_number');
        $visitor = $this->visitorRepository->findByPassport($passportNumber);
        return response()->json($visitor);
    }
}