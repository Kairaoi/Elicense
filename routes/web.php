<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\License\SpeciesIslandQuotaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Auth::routes([
    'register' => false,
    'reset' => false,    
    'verify' => false,   
    'login' => true     
]);


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Routes for non-authenticated users
Route::group([
    'as' => 'license.',
    'prefix' => 'license',
], function () {
    Route::get('applicants/create', [\App\Http\Controllers\License\ApplicantsController::class, 'create'])->name('applicants.create');
    Route::post('applicants', [\App\Http\Controllers\License\ApplicantsController::class, 'store'])->name('applicants.store');

    Route::get('licenses/create', [\App\Http\Controllers\License\LicensesController::class, 'create'])->name('licenses.create');
    Route::post('licenses', [\App\Http\Controllers\License\LicensesController::class, 'store'])->name('licenses.store');
});

// Routes for authenticated users with role restrictions
Route::group([
    'as' => 'applicant.',
    'prefix' => 'applicant',
    'middleware' => ['auth', 'role:lic.admin|lic.license'],
], function () {
    
    Route::post('licenses/{id}/review', [\App\Http\Controllers\License\ApplicantsController::class, 'review'])->name('applicants.review');
    Route::get('applicants/{id}/activity-log', [\App\Http\Controllers\License\ApplicantsController::class, 'activityLog'])->name('applicants.activity-log');
    Route::match(['get', 'post'], 'applicants/datatables', [\App\Http\Controllers\License\ApplicantsController::class, 'getDataTables'])->name('applicants.datatables');
    
    // Add the PDF download route here
    Route::get('applicants/{id}/pdf', [\App\Http\Controllers\License\ApplicantsController::class, 'downloadPDF'])->name('applicants.pdf');
    
    Route::resource('applicants', \App\Http\Controllers\License\ApplicantsController::class)->except(['create', 'store']);
    Route::resource('boards', \App\Http\Controllers\Board\ApplicationBoardController::class, ['only' => ['index']]);
});

Route::group([
    'as' => 'applicantdetails.',
    'prefix' => 'applicantdetails',
    'middleware' => ['auth', 'role:applicant'],
], function () {
    
    
    Route::match(['get', 'post'], 'applicantdetails/datatables', [\App\Http\Controllers\License\ApplicantsDetailsController::class, 'getDataTables'])->name('applicants.datatables');
    Route::resource('applicantdetails', \App\Http\Controllers\License\ApplicantsDetailsController::class);
    
});

Route::group([
    'as' => 'license.',
    'prefix' => 'license',
    'middleware' => ['auth', 'role:lic.admin|lic.license'],
], function () {

    Route::get('/licenses/create2', [App\Http\Controllers\License\LicensesController::class, 'create2'])->name('licenses.create2');
    Route::post('/licenses/store2', [App\Http\Controllers\License\LicensesController::class, 'store2'])->name('licenses.store2');
    Route::get('/licenses/review/{applicantId}/{licenseId}', [App\Http\Controllers\License\LicensesController::class, 'review'])->name('licenses.review');

    Route::match(['get', 'post'], 'licenses/datatables', [\App\Http\Controllers\License\LicensesController::class, 'getDataTables'])->name('licenses.datatables');
    Route::resource('licenses', \App\Http\Controllers\License\LicensesController::class)->except(['create', 'store']);

    Route::match(['get', 'post'], 'agents/datatables', [\App\Http\Controllers\License\AgentsController::class, 'getDataTables'])->name('agents.datatables');
    Route::resource('agents', \App\Http\Controllers\License\AgentsController::class);

    // Add the new agent species route here
    Route::get('/agents/{agent}/species', [\App\Http\Controllers\License\AgentsController::class, 'getSpecies'])->name('agents.species');
    
    Route::get('trackings/check-duplicate', [App\Http\Controllers\License\SpeciesTrackingController::class, 'checkDuplicate'])->name('trackings.check-duplicate');

    Route::match(['get', 'post'], 'trackings/datatables', [\App\Http\Controllers\License\SpeciesTrackingController::class, 'getDataTables'])->name('trackings.datatables');
    Route::resource('trackings', \App\Http\Controllers\License\SpeciesTrackingController::class);

        // Add the get-species route here
          // Add the get-species route here
     Route::get('get-species', [\App\Http\Controllers\License\MonthlyHarvestController::class, 'getLicenseItems'])->name('licenses.getLicenseItems');

    Route::match(['get', 'post'], 'monthly-harvests/datatables', [\App\Http\Controllers\License\MonthlyHarvestController::class, 'getDataTables'])->name('monthly-harvests.datatables');
    Route::resource('monthly-harvests', \App\Http\Controllers\License\MonthlyHarvestController::class);

  

    
    
    Route::get('/licenses/{license}/invoice', [\App\Http\Controllers\License\LicensesController::class, 'showInvoice'])->name('licenses.invoice');
    Route::get('licenses/{id}/activity-log', [\App\Http\Controllers\License\LicensesController::class, 'activityLog'])->name('licenses.activity-log');
    Route::get('/license/{id}/download-invoice', [\App\Http\Controllers\License\LicensesController::class, 'downloadInvoice'])->name('licenses.downloadInvoice');
    Route::get('licenses/{license}/issue', [\App\Http\Controllers\License\LicensesController::class, 'showIssueLicenseForm'])->name('licenses.showIssueForm');
    Route::post('licenses/{license}/issue', [\App\Http\Controllers\License\LicensesController::class, 'issueLicense'])->name('licenses.issue');
    Route::get('/license/licenses/{license}/download', [\App\Http\Controllers\License\LicensesController::class, 'downloadLicense'])->name('licenses.download');
    Route::patch('/licenses/{license}/mark-as-paid', [\App\Http\Controllers\License\LicensesController::class, 'markAsPaid'])->name('licenses.mark-as-paid');
    Route::get('/licenses/by-applicant', [\App\Http\Controllers\License\LicensesController::class, 'getLicensesByApplicant'])->name('licenses.getLicensesByApplicant');

    // New route for revoking a license
    Route::put('/licenses/{license}/revoke', [App\Http\Controllers\License\LicensesController::class, 'revokeLicense'])->name('licenses.revoke');
    Route::get('/licenses/{license}/download-revoked', [App\Http\Controllers\License\LicensesController::class, 'downloadRevokedLicense'])
    ->name('licenses.download-revoked');


    Route::match(['get', 'post'], 'licenses_items/datatables', [\App\Http\Controllers\License\LicenseItemsController::class, 'getDataTables'])->name('licenses_items.datatables');
    Route::resource('licenses_items', \App\Http\Controllers\License\LicenseItemsController::class);

    // New route for selecting license
    Route::get('licenses/select', [\App\Http\Controllers\License\LicensesController::class, 'selectLicense'])->name('licenses.select');
    // Route::match(['get', 'post'], 'species/datatables', [\App\Http\Controllers\License\SpeciesController::class, 'getDataTables'])->name('species.datatables');
    // Route::resource('species', \App\Http\Controllers\License\SpeciesController::class);

    Route::resource('boards', \App\Http\Controllers\Board\LicenseBoardController::class, ['only' => ['index']]);
    Route::resource('board', \App\Http\Controllers\Board\AgentBoardController::class, ['only' => ['index']]);
});

Route::group([
    'as' => 'export.',
    'prefix' => 'export',
    'middleware' => ['auth', 'role:lic.export|lic.admin'],
], function () {
    Route::get('/declarations/get-species-for-applicant', [\App\Http\Controllers\License\ExportDeclarationsController::class, 'getSpeciesForApplicant'])->name('get-species-for-applicant');
    Route::get('/declarations/{id}/invoice', [\App\Http\Controllers\License\ExportDeclarationsController::class, 'showInvoice'])->name('declarations.invoice');
    Route::match(['get', 'post'], 'declarations/datatables', [\App\Http\Controllers\License\ExportDeclarationsController::class, 'getDataTables'])->name('declarations.datatables');
    Route::resource('declarations', \App\Http\Controllers\License\ExportDeclarationsController::class);
    Route::resource('boards', \App\Http\Controllers\Board\ExportDeclarationBoardController::class, ['only' => ['index']]);
});

Route::group([
    'as' => 'harvester.',
    'prefix' => 'harvester',
    'middleware' => ['auth', 'role:lic.harvester|lic.admin'],
], function () {
    // Datatables routes
    Route::match(['get', 'post'], 'licenses/datatables', [
        \App\Http\Controllers\License\HarvesterLicensesController::class, 
        'getDataTables'
    ])->name('licenses.datatables');

    // License issue routes
    Route::get('/licenses/{license}/issue', [
        \App\Http\Controllers\License\HarvesterLicensesController::class, 
        'showIssueLicenseForm'
    ])->name('licenses.issue.form');

    Route::post('/licenses/{license}/issue', [
        \App\Http\Controllers\License\HarvesterLicensesController::class, 
        'issueLicense'
    ])->name('licenses.issue');

    // License download route
    Route::get('/licenses/{license}/download', [
        \App\Http\Controllers\License\HarvesterLicensesController::class, 
        'downloadLicense'
    ])->name('licenses.download');

    // Main resource routes
    Route::resource('licenses', \App\Http\Controllers\License\HarvesterLicensesController::class);

    // Applicant routes
    Route::match(['get', 'post'], 'applicants/datatables', [
        \App\Http\Controllers\License\HarvesterApplicantsController::class, 
        'getDataTables'
    ])->name('applicants.datatables');
    
    Route::resource('applicants', \App\Http\Controllers\License\HarvesterApplicantsController::class);
    Route::get('harvester-licenses/{id}/pdf', [\App\Http\Controllers\License\HarvesterLicensesController::class, 'downloadPDF'])->name('licenses.pdf');

    // Board routes
    Route::resource('boards', \App\Http\Controllers\Board\HarvesterBoardController::class, [
        'only' => ['index']
    ]);
});


Route::group([
    'as' => 'reference.',
    'prefix' => 'reference',
    'middleware' => ['auth', 'role:sys.admin'],
], function () {
    Route::match(['get', 'post'], 'islands/datatables', [\App\Http\Controllers\Reference\IslandController::class, 'getDataTables'])->name('islands.datatables');
    Route::resource('islands', \App\Http\Controllers\Reference\IslandController::class);

    Route::match(['get', 'post'], 'species/datatables', [\App\Http\Controllers\License\SpeciesController::class, 'getDataTables'])->name('speices.datatables');
    Route::resource('species', \App\Http\Controllers\License\SpeciesController::class);
    Route::match(['get', 'post'], 'licenses_types/datatables', [\App\Http\Controllers\License\LicenseTypesController::class, 'getDataTables'])->name('licenses_types.datatables');
    Route::resource('licenses_types', \App\Http\Controllers\License\LicenseTypesController::class);

    Route::resource('boards', \App\Http\Controllers\Board\ReferenceBoardController::class, ['only' => ['index']]);
});


// Add this route group to your existing routes
Route::group([
    'as' => 'admin.',
    'prefix' => 'admin',
    'middleware' => ['auth', 'role:sys.admin'], // Only lic.admin can access these routes
], function () {
    Route::get('/login-logs', [\App\Http\Controllers\Admin\UserController::class, 'showLoginLogs'])->name('login-logs');
    Route::match(['get', 'post'], 'login-logs/datatables', [\App\Http\Controllers\Admin\UserController::class, 'getLoginLogsDataTables'])->name('login-logs.datatables');
    // User Management Routes
    Route::match(['get', 'post'], 'users/datatables', [\App\Http\Controllers\Admin\UserController::class, 'getDataTables'])->name('users.datatables');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Role Management Routes
    Route::match(['get', 'post'], 'roles/datatables', [\App\Http\Controllers\Admin\RoleController::class, 'getDataTables'])->name('roles.datatables');
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    
    // Permission Management Routes
    Route::match(['get', 'post'], 'permissions/datatables', [\App\Http\Controllers\Admin\PermissionController::class, 'getDataTables'])->name('permissions.datatables');
    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    
    // Dashboard Route
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
   
});

Route::group([
    'as' => 'reports.',
    'prefix' => 'reports',
    'middleware' => ['auth'] // Adjust middleware as needed
], function () {
    Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
    Route::GET('/reports/{id}/run', [\App\Http\Controllers\ReportController::class, 'runReport'])->name('run');
   

    Route::get('/group/{groupId}', [\App\Http\Controllers\ReportController::class, 'getReportsByGroup'])->name('by.group');
});



Route::group([
    'as' => 'species-island-quotas.',
    'prefix' => 'species-island-quotas',
    'middleware' => ['auth', 'role:lic.admin'],
], function () {
    
    Route::match(['get', 'post'], 'quota/datatables', [\App\Http\Controllers\License\SpeciesIslandQuotaController::class, 'getDataTables'])->name('quota.datatables');
    
    // Define the resource routes except for 'edit', 'update', and 'destroy'
    Route::resource('quota', \App\Http\Controllers\License\SpeciesIslandQuotaController::class)
        ->except(['edit', 'update', 'destroy']);
});



Route::group([
    'as' => 'pfps.',
    'prefix' => 'pfps',
    'middleware' => ['auth', 'role:lic.admin'],
], function () {

    Route::match(['get', 'post'], 'countries/datatables', [\App\Http\Controllers\Pfps\CountryController::class, 'getDataTables'])->name('countries.datatables');
    Route::resource('/countries', \App\Http\Controllers\Pfps\CountryController::class);

    Route::match(['get', 'post'], 'organizations/datatables', [\App\Http\Controllers\Pfps\OrganizationController::class, 'getDataTables'])->name('organizations.datatables');
    Route::resource('/organizations', \App\Http\Controllers\Pfps\OrganizationController::class);

    Route::match(['get', 'post'], 'lodges/datatables', [\App\Http\Controllers\Pfps\LodgeController::class, 'getDataTables'])->name('lodges.datatables');
    Route::resource('/lodges', \App\Http\Controllers\Pfps\LodgeController::class);

    Route::match(['get', 'post'], 'durations/datatables', [\App\Http\Controllers\Pfps\DurationController::class, 'getDataTables'])->name('durations.datatables');
    Route::resource('/durations', \App\Http\Controllers\Pfps\DurationController::class);

    Route::match(['get', 'post'], 'permit-categories/datatables', [\App\Http\Controllers\Pfps\PermitCategoryController::class, 'getDataTables'])->name('permit-categories.datatables');
    Route::resource('/permit-categories', \App\Http\Controllers\Pfps\PermitCategoryController::class);

    Route::match(['get', 'post'], 'activity-types/datatables', [\App\Http\Controllers\Pfps\ActivityTypeController::class, 'getDataTables'])->name('activity-types.datatables');
    Route::resource('/activity-types', \App\Http\Controllers\Pfps\ActivityTypeController::class);

    Route::match(['get', 'post'], 'target_species/datatables', [\App\Http\Controllers\Pfps\TargetSpeciesController::class, 'getDataTables'])->name('target_species.datatables');
    Route::resource('/target_species', \App\Http\Controllers\Pfps\TargetSpeciesController::class);

    Route::match(['get', 'post'], 'activity_sites/datatables', [\App\Http\Controllers\Pfps\ActivitySiteController::class, 'getDataTables'])->name('activity_sites.datatables');
    Route::resource('/activity_sites', \App\Http\Controllers\Pfps\ActivitySiteController::class);

    Route::match(['get', 'post'], 'equipment_rentals/datatables', [\App\Http\Controllers\Pfps\EquipmentRentalController::class, 'getDataTables'])->name('equipment_rentals.datatables');
    Route::resource('/equipment_rentals', \App\Http\Controllers\Pfps\EquipmentRentalController::class);

    Route::match(['get', 'post'], 'annual_trip_fees/datatables', [\App\Http\Controllers\Pfps\AnnualTripFeeController::class, 'getDataTables'])->name('annual_trip_fees.datatables');
    Route::resource('/annual_trip_fees', \App\Http\Controllers\Pfps\AnnualTripFeeController::class);

    Route::match(['get', 'post'], 'permits/datatables', [\App\Http\Controllers\Pfps\PermitController::class, 'getDataTables'])->name('permits.datatables');
    Route::resource('/permits', \App\Http\Controllers\Pfps\PermitController::class);

    // Additional custom actions
    Route::post('/permits/generate', [\App\Http\Controllers\Pfps\PermitController::class, 'generate'])->name('permits.generate');
    Route::post('/permits/{id}/status', [\App\Http\Controllers\Pfps\PermitController::class, 'updateStatus'])->name('permits.updateStatus');
    Route::get('/permits/{id}/pdf', [\App\Http\Controllers\Pfps\PermitController::class, 'generatePDF'])->name('permits.generatePDF');
    Route::post('/permits/verify', [\App\Http\Controllers\Pfps\PermitController::class, 'verify'])->name('permits.verify');
   
    Route::match(['get', 'post'], 'visitors/datatables', [\App\Http\Controllers\Pfps\VisitorController::class, 'getDataTables'])->name('visitors.datatables');
    Route::resource('/visitors', \App\Http\Controllers\Pfps\VisitorController::class);

    Route::match(['get', 'post'], 'application-target-species/datatables', [\App\Http\Controllers\Pfps\ApplicationTargetSpeciesController::class, 'getDataTables'])->name('application-target-species.datatables');
    Route::resource('/application-target-species', \App\Http\Controllers\Pfps\ApplicationTargetSpeciesController::class);

   
    Route::resource('boards', \App\Http\Controllers\Board\PleasureFishingPremitBoardController::class, ['only' => ['index']]);
});