<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\RoleRepository;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        return view('admin.roles.index');
    }

    public function getDataTables()
{
    $roles = Role::with('permissions')->select(['id', 'name', 'guard_name', 'created_at']);
    
    return DataTables::of($roles)
        ->addColumn('permissions', function ($role) {
            return $role->permissions->pluck('name')->implode(', ');
        })
        ->addColumn('actions', function ($role) {
            return view('admin.roles.index', [
                'id' => $role->id,
                'editRoute' => route('admin.roles.edit', $role),
                'showRoute' => route('admin.roles.show', $role),
                'deleteRoute' => route('admin.roles.destroy', $role),
            ]);
        })
        ->rawColumns(['actions'])
        ->make(true);
}

public function create()
{
    $permissions = Permission::all();
    return view('admin.roles.create', compact('permissions'));
}
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:roles',
        'permissions' => 'required|array',
    ]);

    try {
        $role = auth()->user()->roles()->create([
            'name' => $validated['name'],
        ]);

        $role->permissions()->attach($validated['permissions']);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully");
    } catch (\Exception $e) {
        return redirect()->route('admin.roles.index')
            ->with('error', 'Failed to create role');
    }
}

public function edit(Role $role)
{
    $permissions = Permission::all();
    return view('admin.roles.edit', compact('role', 'permissions'));
}

public function update(Request $request, Role $role)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        'permissions' => 'required|array',
    ]);

    try {
        $role->update([
            'name' => $validated['name'],
        ]);

        $role->permissions()->sync($validated['permissions']);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully");
    } catch (\Exception $e) {
        return redirect()->route('admin.roles.index')
            ->with('error', 'Failed to update role');
    }
}

    public function destroy(Role $role)
    {
        try {
            $deleted = $this->roleRepository->deleteById($role->id);
            if (!$deleted) {
                return response()->json(['message' => 'Role not found or failed to delete'], Response::HTTP_NOT_FOUND);
            }
            return response()->json(['message' => 'Role deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while deleting the role'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
