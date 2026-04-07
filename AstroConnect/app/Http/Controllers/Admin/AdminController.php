<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Render admin dashboard landing page.
     */
    public function AdminDashboard()
    {
        return view('pages.admin.dashboard');
    }

    /**
     * List non-admin users and unapproved astrologer accounts.
     */
    public function usersIndex()
    {
        $users = User::with('astrologer')
            ->where('role', '!=', 'admin')
            ->whereDoesntHave('astrologer', function ($query) {
                $query->where('verification_status', 'approved');
            })
            ->latest()
            ->paginate(15);

        return view('pages.admin.users-management', [
            'users' => $users,
        ]);
    }
}
