<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        // Use with() to prevent N+1 queries
        $users = User::with(['owner', 'veterinarian'])->get();

        // Add statistics for dashboard
        $stats = [
            'total_users' => User::count(),
            'total_vets' => User::where('role', 'veterinarian')->count(),
            'total_owners' => User::where('role', 'owner')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
        ];

        return view('admin.dashboard', [
            'users' => $users,
            'stats' => $stats
        ]);
    }

    // // Add new method for system users
    // public function users(): View
    // {
    //     $users = User::with(['owner', 'veterinarian'])->get();
    //     return view('admin.users.index', ['users' => $users]);
    // }

    // Add to AdminController
    public function destroy(User $user)
    {
        try {
            // Prevent deletion of current admin
            if ($user->id === Auth::id()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Cannot delete your own account!');
            }

            $user->delete();
            return redirect()->route('admin.dashboard')
                ->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function users()
    {
        $users = User::with(['owner', 'veterinarian'])->paginate(10);
        return view('admin.users.index', compact('users'));
    }
}
