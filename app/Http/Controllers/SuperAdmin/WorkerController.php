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
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.]+$/u'],
            'dni' => ['required', 'string', 'regex:/^\d{8}$/', 'unique:users,dni'],
            'email' => ['required', 'email', 'unique:users,email', 'regex:/^(?!.*@(tempmail|10minutemail|guerrillamail|mailinator|throwaway|fake|disposable)\.com$).+$/i'],
            'phone' => ['nullable', 'string', 'regex:/^(\+51)?\s?9\d{8}$/', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'permissions' => ['nullable', 'array', function ($attr, $value, $fail) {
                if (empty($value)) {
                    $fail('Debes asignar al menos un permiso de acceso.');
                    return;
                }
                $hasAnyView = collect($value)->contains(fn($p) => !empty($p['can_view']));
                if (!$hasAnyView) {
                    $fail('El trabajador debe tener al menos un permiso de "Ver" activo.');
                }
            }],
            'permissions.*.section' => ['required', 'string', 'in:' . implode(',', array_keys(WorkerPermission::availableSections()))],
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
        ], [
            'name.regex' => 'El nombre solo puede contener letras, espacios, guiones y puntos.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'dni.regex' => 'El DNI debe tener exactamente 8 dígitos numéricos.',
            'email.regex' => 'El correo electrónico no es válido o usa un dominio temporal.',
            'phone.regex' => 'El celular debe tener formato válido (+51 9XXXXXXXX).',
            'password.regex' => 'La contraseña debe incluir al menos una mayúscula, una minúscula y un número.',
        ]);

        $trabajadorRole = Role::where('name', 'trabajador')->firstOrFail();

        $worker = User::create([
            'role_id' => $trabajadorRole->id,
            'name' => trim($validated['name']),
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
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.]+$/u'],
            'dni' => ['required', 'string', 'regex:/^\d{8}$/', Rule::unique('users', 'dni')->ignore($worker->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($worker->id), 'regex:/^(?!.*@(tempmail|10minutemail|guerrillamail|mailinator|throwaway|fake|disposable)\.com$).+$/i'],
            'phone' => ['nullable', 'string', 'regex:/^(\+51)?\s?9\d{8}$/', 'max:15', Rule::unique('users', 'phone')->ignore($worker->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'status' => 'nullable|string|in:active,inactive',
            'permissions' => ['nullable', 'array', function ($attr, $value, $fail) {
                if (empty($value)) {
                    $fail('Debes asignar al menos un permiso de acceso.');
                    return;
                }
                $hasAnyView = collect($value)->contains(fn($p) => !empty($p['can_view']));
                if (!$hasAnyView) {
                    $fail('El trabajador debe tener al menos un permiso de "Ver" activo.');
                }
            }],
            'permissions.*.section' => ['required', 'string', 'in:' . implode(',', array_keys(WorkerPermission::availableSections()))],
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
        ], [
            'name.regex' => 'El nombre solo puede contener letras, espacios, guiones y puntos.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'dni.regex' => 'El DNI debe tener exactamente 8 dígitos numéricos.',
            'email.regex' => 'El correo electrónico no es válido o usa un dominio temporal.',
            'phone.regex' => 'El celular debe tener formato válido (+51 9XXXXXXXX).',
            'password.regex' => 'La contraseña debe incluir al menos una mayúscula, una minúscula y un número.',
        ]);

        $worker->update([
            'name' => trim($validated['name']),
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
