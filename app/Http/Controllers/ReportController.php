<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function index()
    {
        $reports = $this->reportRepository->getAllReports();
        return view('license.reports.index', compact('reports'));
    }

    public function runReport($id, Request $request)
    {
        $parameters = $request->all();
        unset($parameters['_token']); // Remove CSRF token

        $results = $this->reportRepository->executeReport($id, $parameters);
        return view('license.reports.results', compact('results'));
    }
}