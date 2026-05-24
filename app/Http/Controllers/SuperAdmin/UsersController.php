<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use App\Models\Cashbox;
use App\Models\CashboxMovement;
use App\Models\ProfileChangeRequest;
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
        $search = $request->get('search');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $blockedUsers = collect();

        if ($filter === 'blocked') {
            $users = collect();
            $queryBlocked = BlockedUser::latest();
            if ($search) {
                $queryBlocked->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }
            if ($fromDate) {
                $queryBlocked->whereDate('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $queryBlocked->whereDate('created_at', '<=', $toDate);
            }
            $blockedUsers = $queryBlocked->paginate(10)->withQueryString();
        } else {
            $query = User::with(['documents', 'role', 'referrer'])
                ->whereHas('role', fn($q) => $q->where('name', '!=', 'superadmin'))
                ->where('profile_completed', true);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%")
                        ->orWhere('ruc', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            }

            if ($filter === 'ghosts') {
                $query->where('is_ghost', true);
            } else {
                $query->where('is_ghost', false);

                if ($filter === 'pending') {
                    $query->whereHas('documents', fn($q) => $q->where('status', 'pending'));
                } elseif ($filter === 'approved') {
                    $query->whereHas('documents', fn($q) => $q->where('status', 'approved'));
                } elseif ($filter === 'rejected') {
                    $query->whereHas('documents', fn($q) => $q->where('status', 'rejected'));
                } elseif ($filter === 'profile-changes') {
                    $query->whereHas('profileChangeRequests', fn($q) => $q->where('status', 'pending'));
                }
            }

            $users = $query->latest()->paginate(10)->withQueryString();
        }

        $counts = [
            'all' => User::whereHas('role', fn($q) => $q->where('name', '!=', 'superadmin'))->where('profile_completed', true)->where('is_ghost', false)->count(),
            'pending' => UserDocument::where('status', 'pending')->whereHas('user', fn($q) => $q->where('is_ghost', false))->count(),
            'approved' => UserDocument::where('status', 'approved')->whereHas('user', fn($q) => $q->where('is_ghost', false))->count(),
            'rejected' => UserDocument::where('status', 'rejected')->whereHas('user', fn($q) => $q->where('is_ghost', false))->count(),
            'ghosts' => User::where('is_ghost', true)->count(),
            'blocked' => BlockedUser::count(),
            'profile-changes' => User::whereHas('profileChangeRequests', fn($q) => $q->where('status', 'pending'))->where('is_ghost', false)->count(),
        ];

        return view('superadmin.users.index', compact('users', 'filter', 'counts', 'blockedUsers'));
    }

    public function createGhost()
    {
        $departments = UbigeoDepartment::all();
        return view('superadmin.users.create-ghost', compact('departments'));
    }

    public function editGhost(User $user)
    {
        if (!$user->is_ghost) {
            abort(404);
        }
        $departments = UbigeoDepartment::all();
        return view('superadmin.users.edit-ghost', compact('user', 'departments'));
    }

    public function updateGhost(Request $request, User $user)
    {
        if (!$user->is_ghost) {
            abort(404);
        }

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
            'dni' => 'required|string|size:8|unique:users,dni,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|size:9|unique:users,phone,' . $user->id,
            'department' => 'required|string',
            'province' => 'required|string',
            'district' => 'required|string',
            'address' => 'required|string|max:255',
        ]);

        $user->update([
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
        ]);

        return redirect()->route('superadmin.users.show', $user)->with('success', 'Usuario fantasma actualizado correctamente.');
    }

    public function destroyGhost(User $user)
    {
        if (!$user->is_ghost) {
            abort(404);
        }

        $user->documents()->delete();
        $user->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'Usuario fantasma eliminado correctamente.');
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
            'role_id' => \App\Models\Role::where('name', 'usuario')->value('id') ?? \App\Models\Role::first()->id,
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

        UserDocument::create([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        return redirect()->route('superadmin.users.show', $user)->with('success', 'Usuario fantasma creado correctamente.');
    }

    public function storeGhostMovement(Request $request, User $user)
    {
        if (!$user->is_ghost) {
            abort(404);
        }

        $request->validate([
            'type' => 'required|in:income,expense',
            'reason' => 'required|in:loan,investment,other',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'cashbox_id' => 'required|exists:cashboxes,id',
            'currency' => 'required|in:PEN,USD',
        ]);

        $cashbox = Cashbox::findOrFail($request->cashbox_id);
        $amount = (float) $request->amount;

        if ($request->type === 'expense' && $cashbox->balance < $amount) {
            return back()->withErrors(['amount' => 'Saldo insuficiente en la caja.']);
        }

        \DB::transaction(function () use ($request, $user, $cashbox, $amount) {
            if (!$user->ghost_code) {
                $user->ghost_code = $this->generateGhostCode($request->reason);
                $user->ghost_type = $request->reason;
                $user->ghost_cashbox_id = $cashbox->id;
                $user->ghost_currency = $request->currency;
                $user->save();
            }

            CashboxMovement::create([
                'cashbox_id' => $cashbox->id,
                'user_id' => $user->id,
                'type' => $request->type,
                'amount' => $amount,
                'description' => $request->description,
                'reference_type' => 'ghost',
                'reference_id' => $user->id,
                'date' => now(),
            ]);

            if ($request->type === 'income') {
                $cashbox->increment('balance', $amount);
            } else {
                $cashbox->decrement('balance', $amount);
            }
        });

        return back()->with('success', 'Movimiento registrado correctamente.');
    }

    private function generateGhostCode(string $type): string
    {
        $prefix = match($type) {
            'loan' => 'GHP',
            'investment' => 'GHI',
            default => 'GHX',
        };

        $last = User::where('ghost_code', 'like', $prefix . '-%')
            ->orderByDesc('id')
            ->value('ghost_code');

        $next = $last ? (int) substr($last, strlen($prefix) + 1) + 1 : 1;

        return $prefix . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
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
            'field' => 'required|string|in:first_name,last_name,dni,phone,address,loan_interest_rate,credit_line',
            'value' => 'required|string|max:255',
        ]);

        $field = $request->field;
        $value = trim($request->value);

        if ($user->is_ghost && $field === 'loan_interest_rate') {
            return back()->withErrors(['value' => 'No se puede personalizar la tasa de un usuario fantasma.']);
        }

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
        } elseif ($field === 'credit_line') {
            if (!is_numeric($value) || $value < 0) {
                return back()->withErrors(['value' => 'La línea de crédito debe ser un número positivo.']);
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

    public function approveProfileChange(ProfileChangeRequest $profileChangeRequest)
    {
        if (!$profileChangeRequest->isPending()) {
            return back()->withErrors(['error' => 'Esta solicitud ya fue procesada.']);
        }

        $profile = $profileChangeRequest->user->clientProfile;

        if ($profile) {
            $profile->update($profileChangeRequest->new_data);
        }

        $profileChangeRequest->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Cambios de perfil aprobados y aplicados correctamente.');
    }

    public function rejectProfileChange(Request $request, ProfileChangeRequest $profileChangeRequest)
    {
        if (!$profileChangeRequest->isPending()) {
            return back()->withErrors(['error' => 'Esta solicitud ya fue procesada.']);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        $profileChangeRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Solicitud de cambio rechazada.');
    }
}
