<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request): View
    {
        $users = $this->userService->getPaginatedUsers(15);
        
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        try {
            $this->userService->createUser($request->validated());
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat user: ' . $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $user = $this->userService->findUser($id);
        
        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }

        return view('admin.users.show', compact('user'));
    }

    public function edit(int $id): View
    {
        $user = $this->userService->findUser($id);
        
        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, int $id): RedirectResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());
            
            if (!$user) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'User tidak ditemukan');
            }

            return redirect()->route('admin.users.show', $id)
                ->with('success', 'User berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $success = $this->userService->deleteUser($id);
            
            if (!$success) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'User tidak ditemukan');
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    // public function search(Request $request): View|JsonResponse
    // {
    //     $request->validate([
    //         'q' => 'required|string|min:1|max:255'
    //     ]);

    //     try {
    //         $users = $this->userService->searchUsers($request->q);
            
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'users' => $users,
    //                 'count' => $users->count()
    //             ]);
    //         }

    //         return view('admin.users.search', compact('users'));
    //     } catch (\Exception $e) {
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => $e->getMessage()
    //             ], 400);
    //         }

    //         return redirect()->back()
    //             ->with('error', $e->getMessage());
    //     }
    // }

    // public function customers(): View
    // {
    //     $users = $this->userService->getCustomers();
    //     $role = 'customer';
        
    //     return view('admin.users.by-role', compact('users', 'role'));
    // }

    // public function admins(): View
    // {
    //     $users = $this->userService->getAdmins();
    //     $role = 'admin';
        
    //     return view('admin.users.by-role', compact('users', 'role'));
    // }

    // public function byRole(string $role): View
    // {
    //     $allowedRoles = ['customer', 'admin'];
        
    //     if (!in_array($role, $allowedRoles)) {
    //         abort(404, 'Role tidak valid');
    //     }

    //     $users = $this->userService->getUsersByRole($role);
        
    //     return view('admin.users.by-role', compact('users', 'role'));
    // }

    // public function firstTimeCustomers(): View
    // {
    //     $users = $this->userService->getFirstTimeCustomers();
    //     $type = 'first-time';
        
    //     return view('admin.users.by-type', compact('users', 'type'));
    // }

    // public function repeatCustomers(): View
    // {
    //     $users = $this->userService->getRepeatCustomers();
    //     $type = 'repeat';
        
    //     return view('admin.users.by-type', compact('users', 'type'));
    // }

    // public function dormantCustomers(): View
    // {
    //     $users = $this->userService->getDormantCustomers();
    //     $type = 'dormant';
        
    //     return view('admin.users.by-type', compact('users', 'type'));
    // }

    // public function withTireStorage(): View
    // {
    //     $users = $this->userService->getUsersWithTireStorage();
    //     $type = 'with-tire-storage';
        
    //     return view('admin.users.by-type', compact('users', 'type'));
    // }

    // public function resetPassword(Request $request, int $id): JsonResponse
    // {
    //     $request->validate([
    //         'new_password' => 'required|string|min:8|confirmed'
    //     ]);

    //     try {
    //         $success = $this->userService->resetPassword($id, $request->new_password);
            
    //         if (!$success) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User tidak ditemukan'
    //             ], 404);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Password berhasil direset'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 400);
    //     }
    // }

    // public function changePassword(Request $request, int $id): JsonResponse
    // {
    //     $request->validate([
    //         'current_password' => 'required|string',
    //         'new_password' => 'required|string|min:8|confirmed'
    //     ]);

    //     try {
    //         $success = $this->userService->changePassword(
    //             $id,
    //             $request->current_password,
    //             $request->new_password
    //         );
            
    //         if (!$success) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User tidak ditemukan'
    //             ], 404);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Password berhasil diubah'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 400);
    //     }
    // }
}