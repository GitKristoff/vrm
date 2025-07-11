<?php

namespace App\Http\Controllers;

use App\Models\User; // Add this import
use Illuminate\View\View;

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
}