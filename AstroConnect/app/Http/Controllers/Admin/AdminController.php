<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function AdminDashboard()
    {
        return view('pages.admin.index');
    }

    public function usersIndex()
    {
        $users = User::with('astrologer')
            ->where('role', '!=', 'admin')
            ->whereDoesntHave('astrologer', function ($query) {
                $query->where('verification_status', 'approved');
            })
            ->latest()
            ->paginate(15);

        return view('pages.admin.users.index', [
            'users' => $users,
        ]);
    }
}
