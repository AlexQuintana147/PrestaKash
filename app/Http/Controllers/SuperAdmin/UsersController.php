<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = User::with(['documents', 'role'])
            ->whereHas('role', fn($q) => $q->where('name', '!=', 'superadmin'))
            ->where('profile_completed', true);

        if ($filter === 'pending') {
            $query->whereHas('documents', fn($q) => $q->where('status', 'pending'));
        } elseif ($filter === 'approved') {
            $query->whereHas('documents', fn($q) => $q->where('status', 'approved'));
        } elseif ($filter === 'rejected') {
            $query->whereHas('documents', fn($q) => $q->where('status', 'rejected'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'      => User::whereHas('role', fn($q) => $q->where('name', '!=', 'superadmin'))->where('profile_completed', true)->count(),
            'pending'  => UserDocument::where('status', 'pending')->count(),
            'approved' => UserDocument::where('status', 'approved')->count(),
            'rejected' => UserDocument::where('status', 'rejected')->count(),
        ];

        return view('superadmin.users.index', compact('users', 'filter', 'counts'));
    }

    public function show(User $user)
    {
        $user->load('documents');
        return view('superadmin.users.show', compact('user'));
    }

    public function approve(User $user)
    {
        $user->documents()->update(['status' => 'approved']);
        return back()->with('success', 'Usuario aprobado correctamente.');
    }

    public function reject(User $user)
    {
        $user->documents()->update(['status' => 'rejected']);
        return back()->with('success', 'Usuario rechazado.');
    }
}
