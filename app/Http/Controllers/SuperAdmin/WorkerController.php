<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkerPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    public function index()
    {
        $trabajadorRole = Role::where('name', 'trabajador')->first();
        $workers = User::where('role_id', $trabajadorRole?->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('superadmin.workers.index', compact('workers'));
    }

    public function create()
    {
        $sections = WorkerPermission::availableSections();
        return view('superadmin.workers.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:users,dni',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*.section' => 'required|string',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
        ]);

        $trabajadorRole = Role::where('name', 'trabajador')->firstOrFail();

        $worker = User::create([
            'role_id' => $trabajadorRole->id,
            'name' => $validated['name'],
            'dni' => $validated['dni'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'profile_completed' => true,
        ]);

        if (!empty($validated['permissions'])) {
            foreach ($validated['permissions'] as $perm) {
                WorkerPermission::create([
                    'user_id' => $worker->id,
                    'section' => $perm['section'],
                    'can_view' => $perm['can_view'] ?? false,
                    'can_create' => $perm['can_create'] ?? false,
                    'can_edit' => $perm['can_edit'] ?? false,
                    'can_delete' => $perm['can_delete'] ?? false,
                ]);
            }
        }

        return redirect()->route('superadmin.workers.index')->with('success', 'Trabajador creado correctamente');
    }

    public function edit(User $worker)
    {
        if (!$worker->isTrabajador()) {
            abort(404);
        }
        $sections = WorkerPermission::availableSections();
        $existingPermissions = $worker->workerPermissions()->pluck(null, 'section')->toArray();
        return view('superadmin.workers.edit', compact('worker', 'sections', 'existingPermissions'));
    }

    public function update(Request $request, User $worker)
    {
        if (!$worker->isTrabajador()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dni' => ['required', 'string', 'max:20', Rule::unique('users', 'dni')->ignore($worker->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($worker->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'nullable|string|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*.section' => 'required|string',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
        ]);

        $worker->update([
            'name' => $validated['name'],
            'dni' => $validated['dni'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $worker->update(['password' => Hash::make($validated['password'])]);
        }

        $worker->workerPermissions()->delete();
        if (!empty($validated['permissions'])) {
            foreach ($validated['permissions'] as $perm) {
                WorkerPermission::create([
                    'user_id' => $worker->id,
                    'section' => $perm['section'],
                    'can_view' => $perm['can_view'] ?? false,
                    'can_create' => $perm['can_create'] ?? false,
                    'can_edit' => $perm['can_edit'] ?? false,
                    'can_delete' => $perm['can_delete'] ?? false,
                ]);
            }
        }

        return redirect()->route('superadmin.workers.index')->with('success', 'Trabajador actualizado correctamente');
    }

    public function destroy(User $worker)
    {
        if (!$worker->isTrabajador()) {
            abort(404);
        }
        $worker->workerPermissions()->delete();
        $worker->delete();
        return redirect()->route('superadmin.workers.index')->with('success', 'Trabajador eliminado');
    }
}
