<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\CountryRepository; 

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CountryController extends Controller
{
    protected $countryRepository;
   

    /**
     * countryController constructor.
     *
     * @param countryRepository $countryRepository
     */
    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
       
    }

    /**
     * Get DataTable of country.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->countryRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of country.
     *
     * @return Response
     */
    public function index()
    {
        return view('pfps.country.index');
    }

    /**
     * Show the form for creating a new country.
     *
     * @return Response
     */
    public function create()
    {

       
        return view('pfps.country.create');
        
    }

    /**
     * Store a newly created country in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request data
         $data = $request->validate([
            'country_name' => 'required|string|max:255|unique:countries,country_name',
            'iso_code' => 'required|string|size:2|unique:countries,iso_code',
        ], [
            'country_name.required' => 'The country name is required.',
            'country_name.string' => 'The country name must be a string.',
            'country_name.max' => 'The country name cannot exceed 255 characters.',
            'country_name.unique' => 'The country name must be unique.',
        ]);

        // Track the creator's ID
        $data['created_by'] = auth()->check() ? auth()->id() : null;

        // Save the country using the repository
        $country = $this->countryRepository->create($data);

        // Redirect with success message
        return redirect()->route('pfps.countries.index')->with('success', 'country created successfully.');
    }

    /**
     * Display the specified country.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $country = $this->countryRepository->getById($id);

        if (!$country) {
            return response()->json(['message' => 'country not found'], Response::HTTP_NOT_FOUND);
        }

        return view('pfps.country.show', compact('country'));
    }

    /**
     * Show the form for editing the specified country.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $country = $this->countryRepository->getById($id);
       

        if (!$country) {
            return redirect()->route('pfps.country.index')->with('error', 'country not found.');
        }

        return view('pfps.country.edit', compact('country'));
    }

    /**
     * Update the specified country in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $data = $request->validate([
            'country_name' => 'required|string|max:255|unique:countries,country_name',
            'iso_code' => 'required|string|size:2|unique:countries,iso_code',
        ], [
            'country_name.required' => 'The country name is required.',
            'country_name.string' => 'The country name must be a string.',
            'country_name.max' => 'The country name cannot exceed 255 characters.',
            'country_name.unique' => 'The country name must be unique.',
        ]);

        // Track the updater's ID
        $data['updated_by'] = auth()->id();

        // Attempt to update the country record
        $updated = $this->countryRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('pfps.countries.index')->with('error', 'Failed to update country.');
        }

        return redirect()->route('pfps.countries.index')->with('success', 'country updated successfully.');
    }

    /**
     * Remove the specified country from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->countryRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'country not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'country deleted successfully']);
    }
}
