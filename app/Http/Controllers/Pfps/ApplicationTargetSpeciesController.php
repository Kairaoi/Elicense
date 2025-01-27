<?php

namespace App\Http\Controllers\Pfps;

use App\Http\Controllers\Controller;
use App\Repositories\Pfps\ApplicationTargetSpeciesRepository;
use App\Repositories\Pfps\VisitorApplicationRepository;
use App\Repositories\Pfps\TargetSpeciesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApplicationTargetSpeciesController extends Controller
{
    protected $applicationTargetSpeciesRepository;
    protected $visitorApplicationRepository;
    protected $targetSpeciesRepository;

    public function __construct(
        ApplicationTargetSpeciesRepository $applicationTargetSpeciesRepository,
        VisitorApplicationRepository $visitorApplicationRepository,
        TargetSpeciesRepository $targetSpeciesRepository
    ) {
        $this->applicationTargetSpeciesRepository = $applicationTargetSpeciesRepository;
        $this->visitorApplicationRepository = $visitorApplicationRepository;
        $this->targetSpeciesRepository = $targetSpeciesRepository;
    }

    /**
     * Display a listing of target species for a given visitor application.
     *
     * @param int $applicationId
     * @return Response
     */
    public function index($applicationId)
    {
        $application = $this->visitorApplicationRepository->getById($applicationId);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], Response::HTTP_NOT_FOUND);
        }

        // Get the associated target species
        $targetSpecies = $this->applicationTargetSpeciesRepository->getByApplicationId($applicationId);

        return view('pfps.application-target-species.index', compact('targetSpecies', 'application'));
    }

    /**
     * Show the form for creating a new target species association.
     *
     * @param int $applicationId
     * @return Response
     */
    public function create($applicationId)
    {
        $application = $this->visitorApplicationRepository->getById($applicationId);

        if (!$application) {
            return redirect()->route('pfps.visitor-applications.index')->with('error', 'Application not found');
        }

        // Get all species to choose from
        $species = $this->targetSpeciesRepository->pluck();

        return view('pfps.application-target-species.create', compact('application', 'species'));
    }

    /**
     * Store a newly created target species association in storage.
     *
     * @param Request $request
     * @param int $applicationId
     * @return Response
     */
    public function store(Request $request, $applicationId)
    {
        $data = $request->validate([
            'species_id' => 'required|exists:target_species,species_id'
        ]);

        $data['application_id'] = $applicationId;

        // Save the new application-target species association
        $this->applicationTargetSpeciesRepository->create($data);

        return redirect()->route('pfps.application-target-species.index', $applicationId)
            ->with('success', 'Target species added successfully.');
    }

    /**
     * Remove the specified target species association from storage.
     *
     * @param int $applicationId
     * @param int $targetSpeciesId
     * @return Response
     */
    public function destroy($applicationId, $targetSpeciesId)
    {
        // Attempt to delete the target species association
        $deleted = $this->applicationTargetSpeciesRepository->delete($applicationId, $targetSpeciesId);

        if (!$deleted) {
            return response()->json(['message' => 'Failed to delete target species'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['message' => 'Target species deleted successfully']);
    }
}
