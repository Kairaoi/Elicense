<?php

namespace App\Http\Controllers\License;

use App\Http\Controllers\Controller;
use App\Repositories\License\ApplicantsDetailsRepository;
use App\Repositories\License\LicensesRepository; 
use Illuminate\Http\Request;
use App\Http\Requests\License\StoreApplicantRequest;
use App\Http\Requests\License\UpdateApplicantRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Models\License\License;
use App\Models\License\LicenseType;
use App\Models\License\Applicant;
use App\Models\License\Species;
use App\Models\License\LicenseItem;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; 


class ApplicantsDetailsController extends Controller
{
    protected $applicantsDetailsRepository;
    
    
    /**
     * ApplicantsController constructor.
     *
     */
    public function __construct(ApplicantsDetailsRepository $applicantsDetailsRepository)
    {
        $this->applicantsDetailsRepository = $applicantsDetailsRepository;
         $this->middleware('throttle:3,1')->only('store');
    }

    /**
     * Get DataTable of license applicants.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->applicantsDetailsRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function index()
    {
        return view('license.applicantdetails.index');
    }

  

}
