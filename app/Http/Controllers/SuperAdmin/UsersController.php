<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UbigeoDepartment;
use App\Models\UbigeoProvince;
use App\Models\UbigeoDistrict;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $blockedUsers = collect();

        if ($filter === 'blocked') {
            $users = collect(); // no hay paginación para bloqueados por ahora
            $blockedUsers = BlockedUser::latest()->get();
        } else {
            $query = User::with(['documents', 'role', 'referrer'])
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
        }

        $counts = [
            'all' => User::whereHas('role', fn($q) => $q->where('name', '!=', 'superadmin'))->where('profile_completed', true)->count(),
            'pending' => UserDocument::where('status', 'pending')->count(),
            'approved' => UserDocument::where('status', 'approved')->count(),
            'rejected' => UserDocument::where('status', 'rejected')->count(),
            'blocked' => BlockedUser::count(),
        ];

        return view('superadmin.users.index', compact('users', 'filter', 'counts', 'blockedUsers'));
    }

    public function createGhost()
    {
        $departments = UbigeoDepartment::all();
        return view('superadmin.users.create-ghost', compact('departments'));
    }

    public function storeGhost(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $words = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);
                    if (count($words) < 2) {
                        $fail('Debes ingresar al menos dos apellidos.');
                    }
                },
            ],
            'dni' => 'required|string|size:8|unique:users,dni',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|size:9|unique:users,phone',
            'department' => 'required|string',
            'province' => 'required|string',
            'district' => 'required|string',
            'address' => 'required|string|max:255',
        ]);

        $user = User::create([
            'role_id' => \App\Models\Role::where('name', 'usuario')->value('id') ?? 2, // Default to usuario
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'dni' => $request->dni,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'province' => $request->province,
            'district' => $request->district,
            'address' => $request->address,
            'profile_completed' => true,
            'step1_completed' => true,
            'is_ghost' => true,
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
        ]);

        // Create empty documents as approved
        UserDocument::create([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'Usuario fantasma creado correctamente.');
    }

    public function show(User $user)
    {
        $user->load('documents');

        // Resolver nombres de ubigeo (IDs en BD son strings con ceros: '01', '0101', '010110')
        $department = $user->department
            ? UbigeoDepartment::where('id', str_pad($user->department, 2, '0', STR_PAD_LEFT))->value('name')
            : null;

        $province = $user->province
            ? UbigeoProvince::where('id', str_pad($user->province, 4, '0', STR_PAD_LEFT))->value('name')
            : null;

        $district = $user->district
            ? UbigeoDistrict::where('id', str_pad($user->district, 6, '0', STR_PAD_LEFT))->value('name')
            : null;

        return view('superadmin.users.show', compact('user', 'department', 'province', 'district'));
    }

    public function approve(User $user)
    {
        $user->documents()->update(['status' => 'approved']);
        return back()->with('success', 'Usuario aprobado correctamente.');
    }

    public function reject(User $user)
    {
        $request = request();
        $request->validate(['reason' => 'required|string|max:255']);

        $now = now();

        $user->documents()->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'rejected_at' => $now,
        ]);

        // Eliminar al usuario en 60 segundos
        \App\Jobs\DeleteRejectedUser::dispatch($user->id)->delay($now->addSeconds(60));

        return back()->with('success', 'Usuario rechazado. Será eliminado en 60 segundos.');
    }

    public function unapprove(User $user)
    {
        $user->documents()->update(['status' => 'pending']);
        return back()->with('success', 'Aprobación revertida.');
    }

    public function block(User $user)
    {
        request()->validate(['reason' => 'nullable|string|max:255']);

        // Guardar en lista negra
        \App\Models\BlockedUser::updateOrCreate(
            ['email' => $user->email],
            [
                'name' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'dni' => $user->dni,
                'reason' => request('reason'),
            ]
        );

        // Eliminar imágenes
        if ($doc = $user->documents) {
            foreach (['selfie', 'dni_front', 'dni_back', 'utility_bill'] as $field) {
                if ($doc->$field) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->$field);
                }
            }
            \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory('documents/' . $user->id);
            $doc->delete();
        }

        // Eliminar usuario (datos personales quedan en blocked_users)
        $user->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'Usuario bloqueado correctamente.');
    }

    public function updateField(Request $request, User $user)
    {
        $request->validate([
            'field' => 'required|string|in:first_name,last_name,dni,phone,address,loan_interest_rate',
            'value' => 'required|string|max:255',
        ]);

        $field = $request->field;
        $value = trim($request->value);

        // Validaciones adicionales en el backend
        if ($field === 'first_name') {
            if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $value)) {
                return back()->withErrors(['value' => 'El nombre solo puede contener letras.']);
            }
        } elseif ($field === 'last_name') {
            if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $value)) {
                return back()->withErrors(['value' => 'Los apellidos solo pueden contener letras.']);
            }
            $words = preg_split('/\s+/', $value, -1, PREG_SPLIT_NO_EMPTY);
            if (count($words) < 2) {
                return back()->withErrors(['value' => 'Debes ingresar al menos dos apellidos.']);
            }
        } elseif ($field === 'dni') {
            if (!preg_match('/^\d{8}$/', $value)) {
                return back()->withErrors(['value' => 'El DNI debe tener exactamente 8 dígitos.']);
            }
        } elseif ($field === 'phone') {
            if (!preg_match('/^\d{9}$/', $value)) {
                return back()->withErrors(['value' => 'El teléfono debe tener exactamente 9 dígitos.']);
            }
        } elseif ($field === 'address') {
            if (mb_strlen($value) > 50) {
                return back()->withErrors(['value' => 'La dirección no puede exceder los 50 caracteres.']);
            }
        } elseif ($field === 'loan_interest_rate') {
            if (!is_numeric($value) || $value < 0 || $value > 100) {
                return back()->withErrors(['value' => 'La tasa debe ser un número entre 0 y 100.']);
            }
            $value = number_format((float) $value, 2, '.', '');
        }

        $user->$field = $value;

        // Si se cambia nombre o apellido, actualizar también el campo 'name'
        if (in_array($field, ['first_name', 'last_name'])) {
            $user->name = trim($user->first_name . ' ' . $user->last_name);
        }

        $user->save();

        return back()->with('success', 'Dato actualizado correctamente.');
    }
}
