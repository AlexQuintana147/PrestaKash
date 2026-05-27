<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkerPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    const MAX_WORKERS = 50;

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
        try {
            $this->checkRateLimit($request);

            $validated = $request->validate([
                'first_name' => ['required', 'string', 'min:2', 'max:120', 'regex:/^[\p{L}\s\-\.]+$/u'],
                'last_name' => ['required', 'string', 'min:4', 'max:120', 'regex:/^[\p{L}\s\-\.]+$/u', function ($attr, $value, $fail) {
                    $parts = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);
                    if (count($parts) < 2) {
                        $fail('Debes ingresar ambos apellidos (apellido paterno y materno).');
                    }
                }],
                'dni' => [
                    'required',
                    'string',
                    'regex:/^[1-9]\d{7}$/',
                    'unique:users,dni',
                ],
                'email' => ['required', 'email', 'unique:users,email', 'regex:/^(?!.*@(tempmail|10minutemail|guerrillamail|mailinator|throwaway|fake|disposable)\.com$).+$/i'],
                'phone' => ['nullable', 'string', 'regex:/^\d{9}$/', 'unique:users,phone'],
                'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
                'permissions' => ['nullable', 'array', function ($attr, $value, $fail) {
                    if (empty($value)) {
                        $fail('Debes asignar al menos un módulo de acceso.');
                        return;
                    }
                    $hasAnyEnabled = collect($value)->contains(fn($p) => !empty($p['enabled']));
                    if (!$hasAnyEnabled) {
                        $fail('El trabajador debe tener al menos un módulo habilitado.');
                    }
                }],
                'permissions.*.section' => ['required', 'string', 'in:' . implode(',', array_keys(WorkerPermission::availableSections()))],
                'permissions.*.enabled' => 'boolean',
            ], [
                'first_name.regex' => 'Los nombres solo pueden contener letras, espacios, guiones y puntos.',
                'first_name.min' => 'Los nombres deben tener al menos 2 caracteres.',
                'last_name.regex' => 'Los apellidos solo pueden contener letras, espacios, guiones y puntos.',
                'last_name.min' => 'Los apellidos deben tener al menos 4 caracteres.',
                'dni.regex' => 'El DNI debe tener exactamente 8 dígitos numéricos y no puede empezar con 0.',
                'dni.unique' => 'Este DNI ya está registrado en el sistema.',
                'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
                'email.regex' => 'El correo electrónico no es válido o usa un dominio temporal.',
                'phone.unique' => 'Este número de celular ya está registrado en el sistema.',
                'phone.regex' => 'El celular debe tener exactamente 9 dígitos numéricos.',
                'password.regex' => 'La contraseña debe incluir al menos una mayúscula, una minúscula y un número.',
            ]);

            $this->checkMaxWorkers();

            $trabajadorRole = Role::where('name', 'trabajador')->firstOrFail();

            $firstName = strip_tags(trim($validated['first_name']));
            $lastName = strip_tags(trim($validated['last_name']));

            $worker = User::create([
                'role_id' => $trabajadorRole->id,
                'name' => $firstName . ' ' . $lastName,
                'dni' => $validated['dni'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'profile_completed' => true,
            ]);

            if (!empty($validated['permissions'])) {
                foreach ($validated['permissions'] as $perm) {
                    if (!empty($perm['enabled'])) {
                        WorkerPermission::create([
                            'user_id' => $worker->id,
                            'section' => $perm['section'],
                            'can_view' => true,
                            'can_create' => false,
                            'can_edit' => false,
                            'can_delete' => false,
                        ]);
                    }
                }
            }

            return redirect()->route('superadmin.workers.index')->with('success', 'Trabajador creado correctamente');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['general' => 'Error al crear trabajador: ' . $e->getMessage()]);
        }
    }

    public function edit(User $worker)
    {
        if (!$worker->isTrabajador()) {
            abort(404);
        }
        $sections = WorkerPermission::availableSections();
        $existingPermissions = $worker->workerPermissions()->get()->keyBy('section')->toArray();
        $nameParts = explode(' ', trim($worker->name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';
        return view('superadmin.workers.edit', compact('worker', 'sections', 'existingPermissions', 'firstName', 'lastName'));
    }

    public function update(Request $request, User $worker)
    {
        if (!$worker->isTrabajador()) {
            abort(404);
        }

        $this->checkRateLimit($request);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:120', 'regex:/^[\p{L}\s\-\.]+$/u'],
            'last_name' => ['required', 'string', 'min:4', 'max:120', 'regex:/^[\p{L}\s\-\.]+$/u', function ($attr, $value, $fail) {
                $parts = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);
                if (count($parts) < 2) {
                    $fail('Debes ingresar ambos apellidos (apellido paterno y materno).');
                }
            }],
            'dni' => [
                'required',
                'string',
                'regex:/^[1-9]\d{7}$/',
                Rule::unique('users', 'dni')->ignore($worker->id),
                function ($attr, $value, $fail) {
                    if (!preg_match('/^[1-9]\d{7}$/', $value)) {
                        $fail('El DNI no puede empezar con 0.');
                        return;
                    }
                },
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($worker->id),
                'regex:/^(?!.*@(tempmail|10minutemail|guerrillamail|mailinator|throwaway|fake|disposable)\.com$).+$/i',
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^\d{9}$/',
                Rule::unique('users', 'phone')->ignore($worker->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'status' => 'nullable|string|in:active,inactive',
            'permissions' => ['nullable', 'array', function ($attr, $value, $fail) {
                if (empty($value)) {
                    $fail('Debes asignar al menos un módulo de acceso.');
                    return;
                }
                $hasAnyEnabled = collect($value)->contains(fn($p) => !empty($p['enabled']));
                if (!$hasAnyEnabled) {
                    $fail('El trabajador debe tener al menos un módulo habilitado.');
                }
            }],
            'permissions.*.section' => ['required', 'string', 'in:' . implode(',', array_keys(WorkerPermission::availableSections()))],
            'permissions.*.enabled' => 'boolean',
        ], [
            'first_name.regex' => 'Los nombres solo pueden contener letras, espacios, guiones y puntos.',
            'first_name.min' => 'Los nombres deben tener al menos 2 caracteres.',
            'last_name.regex' => 'Los apellidos solo pueden contener letras, espacios, guiones y puntos.',
            'last_name.min' => 'Los apellidos deben tener al menos 4 caracteres.',
            'dni.regex' => 'El DNI debe tener exactamente 8 dígitos numéricos y no puede empezar con 0.',
            'dni.unique' => 'Este DNI ya está registrado en el sistema por otro usuario.',
            'email.unique' => 'Este correo electrónico ya está registrado en el sistema por otro usuario.',
            'email.regex' => 'El correo electrónico no es válido o usa un dominio temporal.',
            'phone.unique' => 'Este número de celular ya está registrado en el sistema por otro usuario.',
            'phone.regex' => 'El celular debe tener formato válido (+51 9XXXXXXXX).',
            'password.regex' => 'La contraseña debe incluir al menos una mayúscula, una minúscula y un número.',
        ]);

        $firstName = strip_tags(trim($validated['first_name']));
        $lastName = strip_tags(trim($validated['last_name']));

        $worker->update([
            'name' => $firstName . ' ' . $lastName,
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
                if (!empty($perm['enabled'])) {
                    WorkerPermission::create([
                        'user_id' => $worker->id,
                        'section' => $perm['section'],
                        'can_view' => true,
                        'can_create' => false,
                        'can_edit' => false,
                        'can_delete' => false,
                    ]);
                }
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

    private function checkRateLimit(Request $request): void
    {
        $executed = RateLimiter::attempt(
            key: 'workers-action:' . $request->ip(),
            maxAttempts: 5,
            callback: fn () => true,
            decaySeconds: 60
        );

        if (!$executed) {
            $seconds = RateLimiter::availableIn('workers-action:' . $request->ip());
            abort(429, 'Demasiados intentos. Intenta en ' . $seconds . ' segundos.');
        }
    }

    private function checkMaxWorkers(): void
    {
        $trabajadorRole = Role::where('name', 'trabajador')->first();
        $currentCount = User::where('role_id', $trabajadorRole?->id)->count();

        if ($currentCount >= self::MAX_WORKERS) {
            abort(422, 'Se ha alcanzado el límite máximo de trabajadores (' . self::MAX_WORKERS . '). Contacta al administrador del sistema.');
        }
    }
}
