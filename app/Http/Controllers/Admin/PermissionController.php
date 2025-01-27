<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\PermissionRepository; // Assume this repository is created
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    protected $permissionRepository;

    /**
     * PermissionController constructor.
     *
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a listing of permissions.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.permissions.index');
    }

    /**
     * Get DataTable of permissions.
     *
     * @return Response
     */
    public function getDataTables()
{
    $permissions = Permission::select('permissions.*');

    return DataTables::of($permissions)
        ->addColumn('actions', function ($permission) {
            \Log::info($permission); // Log the permission data for debugging
            return view('admin.permissions.index', compact('permission'));
        })
        ->rawColumns(['actions'])
        ->make(true);
}


    /**
     * Show the form for creating a new permission.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
        ]);

        // Save the permission using the repository
        $this->permissionRepository->create($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully');
    }

    /**
     * Show the form for editing the specified permission.
     *
     * @param Permission $permission
     * @return Response
     */
    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     *
     * @param Request $request
     * @param Permission $permission
     * @return Response
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        // Update permission details
        $this->permissionRepository->update($permission->id, $validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param Permission $permission
     * @return Response
     */
    public function destroy(Permission $permission)
    {
        $deleted = $this->permissionRepository->deleteById($permission->id);

        if (!$deleted) {
            return response()->json(['message' => 'Permission not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
