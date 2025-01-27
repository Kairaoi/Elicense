<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Admin\UserRepository; // Assume this repository is created
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UserLoginLog;
use Yajra\DataTables\DataTables;
use DB;

class UserController extends Controller
{
    protected $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Get DataTable of users.
     *
     * @param Request $request
     * @return Response
     */
    public function getDataTables()
    {
        $users = User::with('roles')->select(['id', 'name', 'email', 'created_at']);

        return DataTables::of($users)
            ->addColumn('roles', function ($user) {
                return $user->roles->pluck('name')->join(', ');
            })
            ->make(true);
    }
    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
{
    // Validate request data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'roles' => 'required|array'
    ]);

    // Hash the password
    $validated['password'] = bcrypt($validated['password']); 

    // Log incoming roles for syncing
    \Log::info('Incoming roles for syncing:', $validated['roles']);

    // Get existing roles based on incoming IDs
    $roles = Role::whereIn('id', $validated['roles'])->get();

    // Log existing roles
    \Log::info('Existing roles:', $roles->toArray());

    // Check if roles are empty
    if ($roles->isEmpty()) {
        return redirect()->back()->withErrors(['roles' => 'Selected roles do not exist.']);
    }

    // Extract role names for syncing
    $roleNames = $roles->pluck('name')->toArray();

    try {
        // Save the user and sync roles using a transaction
        DB::transaction(function () use ($validated, $roleNames) {
            $user = User::create($validated);
            $user->syncRoles($roleNames); // Sync role names instead of IDs
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    } catch (\Exception $e) {
        \Log::error('User creation failed: ' . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'User creation failed.']);
    }
}

    
    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     * @return Response
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function update(Request $request, User $user)
{
    // Validate request data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:8|confirmed',
        'roles' => 'required|array'
    ]);

    // Check if password is provided and hash it if it is
    if ($request->filled('password')) {
        $validated['password'] = bcrypt($validated['password']);
    } else {
        // If password is not provided, we don't want to update it
        unset($validated['password']);
    }

    // Log incoming roles for syncing
    \Log::info('Incoming roles for syncing:', $validated['roles']);

    // Get existing roles based on incoming IDs
    $roles = Role::whereIn('id', $validated['roles'])->get();

    // Log existing roles
    \Log::info('Existing roles:', $roles->toArray());

    // Check if roles are empty
    if ($roles->isEmpty()) {
        return redirect()->back()->withErrors(['roles' => 'Selected roles do not exist.']);
    }

    // Extract role names for syncing
    $roleNames = $roles->pluck('name')->toArray();

    try {
        // Update the user and sync roles using a transaction
        DB::transaction(function () use ($user, $validated, $roleNames) {
            $user->update($validated);
            $user->syncRoles($roleNames); // Sync role names instead of IDs
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    } catch (\Exception $e) {
        \Log::error('User update failed: ' . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'User update failed.']);
    }
}


    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        $deleted = $this->userRepository->deleteById($user->id);

        if (!$deleted) {
            return response()->json(['message' => 'User not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function showLoginLogs()
    {
        return view('admin.login-logs');
    }

    public function getLoginLogsDataTables()
    {
        $logs = UserLoginLog::with('user');
    
        return DataTables::of($logs)
            ->addColumn('user_name', function ($log) {
                return $log->user->name ?? 'Unknown User';
            })
            ->editColumn('status', function ($log) {
                return $log->status == 'success'
                    ? '<span class="badge bg-success">Success</span>'
                    : '<span class="badge bg-danger">Failed</span>';
            })
            ->editColumn('login_at', function ($log) {
                return $log->login_at?->format('Y-m-d H:i:s');
            })
            ->editColumn('logout_at', function ($log) {
                // Ngkana e fail te login, e na aki reke te logout time
                if ($log->status !== 'success') {
                    return 'N/A';
                }
                
                // Ngkana e success te login ao akea te logout time, e still active
                if ($log->status === 'success' && !$log->logout_at) {
                    return 'Still Active';
                }
                
                // Ngkana iai te logout time, e na kaoti te logout time
                return $log->logout_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['status'])
            ->make(true);
    }
}
