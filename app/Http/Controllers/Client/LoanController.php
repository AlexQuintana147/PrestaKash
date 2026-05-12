<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('clientProfile');
        $profile = $user->clientProfile;

        // Si no tiene perfil, mostrar formulario
        if (!$profile) {
            return view('client.loans.profile-form');
        }

        $loans = $user->loans()->latest()->take(5)->get();
        return view('client.loans.index', compact('profile', 'loans'));
    }

    public function create()
    {
        $bankAccounts = Auth::user()->bankAccounts;
        return view('client.loans.create', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric|min:100',
            'currency' => 'required|in:PEN,USD',
            'type' => 'required|in:cuotas,unico',
            'reason' => 'required|string|min:10|max:1000',
            'bank_account_id' => 'required|exists:bank_accounts,id,user_id,' . Auth::id(),
        ];

        if ($request->type === 'cuotas') {
            $rules['months'] = 'required|integer|min:3|max:24';
        } else {
            $rules['payment_days'] = 'required|in:15,30';
        }

        $request->validate($rules);

        $montoSolicitado = $request->amount;
        $tipo = $request->type;
        $user = Auth::user();
        $defaultRate = $user->loan_interest_rate ? ($user->loan_interest_rate / 100) : 0.20;

        // ── Lógica de comisión ──
        // Cuotas mensuales: usa tasa default (20% o personalizada)
        // Pago único 15 días: 10%
        // Pago único 30 días: usa tasa default (20% o personalizada)
        if ($tipo === 'cuotas') {
            $tasaInteres = $defaultRate;
            $plazo = $request->months;
            $paymentDays = null;
        } else {
            $paymentDays = (int) $request->payment_days;
            $tasaInteres = $paymentDays === 15 ? 0.10 : $defaultRate;
            $plazo = 1; // un solo pago
        }

        $interes = $montoSolicitado * $tasaInteres;
        $totalAPagar = $montoSolicitado + $interes;
        $cuotaMensual = $tipo === 'cuotas' ? ($totalAPagar / $plazo) : $totalAPagar;

        $bankAccount = \App\Models\BankAccount::find($request->bank_account_id);

        $loanData = [
            'user_id' => Auth::id(),
            'amount' => $montoSolicitado,
            'currency' => $request->currency,
            'months' => $tipo === 'cuotas' ? $plazo : null,
            'payment_days' => $paymentDays,
            'interest_rate' => $tasaInteres * 100,
            'technology_fee' => 0,
            'igv' => 0,
            'total_amount' => $totalAPagar,
            'monthly_payment' => $cuotaMensual,
            'status' => 'pending',
            'type' => $tipo,
            'reason' => $request->reason,
            'bank_name' => $bankAccount->bank_name,
            'account_number' => $bankAccount->account_number,
            'account_type' => $bankAccount->account_type,
            'cci' => $bankAccount->cci,
        ];

        if ($tipo === 'cuotas') {
            $loanData['amortization_system'] = $request->amortization_system;
        }

        Loan::create($loanData);

        return redirect()->route('client.loans')->with('success', 'Tu solicitud de préstamo ha sido recibida y está en proceso de revisión.');
    }

    public function editProfile()
    {
        $profile = Auth::user()->clientProfile;
        return view('client.loans.profile-form', ['editing' => true, 'profile' => $profile]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'institution' => ['required', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'career' => ['required', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'academic_status' => 'required|string',
            'has_children' => 'required|boolean',
            'instagram' => 'nullable|string|max:100',
            'is_employed' => 'required|boolean',
            'employment_type' => 'required_if:is_employed,1|nullable|in:dependiente,independiente',
            'marital_status' => 'required|in:soltero,casado_separado,casado_compartido',
            'spouse_first_name' => ['required_if:marital_status,casado_compartido', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+$/u'],
            'spouse_last_name' => ['required_if:marital_status,casado_compartido', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]+\s+[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]+(\s+[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]+)*$/u'],
            'spouse_dni' => 'required_if:marital_status,casado_compartido|nullable|digits:8',
        ], [
            'institution.regex' => 'La institución solo debe contener letras.',
            'career.regex' => 'La carrera solo debe contener letras.',
            'spouse_first_name.regex' => 'Los nombres solo deben contener letras.',
            'spouse_last_name.regex' => 'Debe ingresar al menos dos apellidos (solo letras).',
        ]);

        Auth::user()->clientProfile->update([
            'institution' => $request->institution,
            'career' => $request->career,
            'academic_status' => $request->academic_status,
            'has_children' => $request->has_children,
            'instagram' => $request->instagram,
            'is_employed' => $request->is_employed,
            'employment_type' => $request->is_employed ? $request->employment_type : null,
            'marital_status' => $request->marital_status,
            'spouse_first_name' => $request->marital_status === 'casado_compartido' ? $request->spouse_first_name : null,
            'spouse_last_name' => $request->marital_status === 'casado_compartido' ? $request->spouse_last_name : null,
            'spouse_dni' => $request->marital_status === 'casado_compartido' ? $request->spouse_dni : null,
        ]);

        return redirect()->route('client.loans')->with('success', 'Perfil actualizado correctamente.');
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'institution' => ['required', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'career' => ['required', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'academic_status' => 'required|string',
            'has_children' => 'required|boolean',
            'instagram' => 'nullable|string|max:100',
            'is_employed' => 'required|boolean',
            'employment_type' => 'required_if:is_employed,1|nullable|in:dependiente,independiente',
            'marital_status' => 'required|in:soltero,casado_separado,casado_compartido',
            'spouse_first_name' => ['required_if:marital_status,casado_compartido', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+$/u'],
            'spouse_last_name' => ['required_if:marital_status,casado_compartido', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]+\s+[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]+(\s+[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]+)*$/u'],
            'spouse_dni' => 'required_if:marital_status,casado_compartido|nullable|digits:8',
        ], [
            'institution.regex' => 'La institución solo debe contener letras.',
            'career.regex' => 'La carrera solo debe contener letras.',
            'spouse_first_name.required_if' => 'Los nombres del cónyuge son obligatorios.',
            'spouse_first_name.regex' => 'Los nombres solo deben contener letras.',
            'spouse_last_name.required_if' => 'Los apellidos del cónyuge son obligatorios.',
            'spouse_last_name.regex' => 'Debe ingresar al menos dos apellidos (solo letras).',
            'spouse_dni.required_if' => 'El DNI del cónyuge es obligatorio.',
            'spouse_dni.digits' => 'El DNI debe tener 8 dígitos.',
            'employment_type.required_if' => 'Debe especificar si es dependiente o independiente.',
        ]);

        ClientProfile::create([
            'user_id' => Auth::id(),
            'institution' => $request->institution,
            'career' => $request->career,
            'academic_status' => $request->academic_status,
            'has_children' => $request->has_children,
            'instagram' => $request->instagram,
            'is_employed' => $request->is_employed,
            'employment_type' => $request->is_employed ? $request->employment_type : null,
            'marital_status' => $request->marital_status,
            'spouse_first_name' => $request->marital_status === 'casado_compartido' ? $request->spouse_first_name : null,
            'spouse_last_name' => $request->marital_status === 'casado_compartido' ? $request->spouse_last_name : null,
            'spouse_dni' => $request->marital_status === 'casado_compartido' ? $request->spouse_dni : null,
        ]);

        return redirect()->route('client.loans')->with('success', 'Perfil completado. Ya puedes gestionar tus préstamos.');
    }
}
