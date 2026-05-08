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
        $user    = Auth::user()->load('clientProfile');
        $profile = $user->clientProfile;

        // Si no tiene perfil, mostrar formulario
        if (!$profile) {
            return view('client.loans.profile-form');
        }

        $loans   = $user->loans()->latest()->get();
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
            'amount'          => 'required|numeric|min:100',
            'currency'        => 'required|in:PEN,USD',
            'type'            => 'required|in:cuotas,unico',
            'reason'          => 'required|string|min:10|max:1000',
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

        // ── Lógica de comisión ──
        // Cuotas mensuales: 20% fijo
        // Pago único 15 días: 10%
        // Pago único 30 días: 15%
        if ($tipo === 'cuotas') {
            $tasaInteres  = 0.20;
            $plazo        = $request->months;
            $paymentDays  = null;
        } else {
            $paymentDays  = (int) $request->payment_days;
            $tasaInteres  = $paymentDays === 15 ? 0.10 : 0.20;
            $plazo        = 1; // un solo pago
        }

        $interes      = $montoSolicitado * $tasaInteres;
        $totalAPagar  = $montoSolicitado + $interes;
        $cuotaMensual = $tipo === 'cuotas' ? ($totalAPagar / $plazo) : $totalAPagar;

        $bankAccount = \App\Models\BankAccount::find($request->bank_account_id);

        Loan::create([
            'user_id'         => Auth::id(),
            'amount'          => $montoSolicitado,
            'currency'        => $request->currency,
            'months'          => $tipo === 'cuotas' ? $plazo : null,
            'payment_days'    => $paymentDays,
            'interest_rate'   => $tasaInteres * 100,
            'technology_fee'  => 0,
            'igv'             => 0,
            'total_amount'    => $totalAPagar,
            'monthly_payment' => $cuotaMensual,
            'status'          => 'pending',
            'type'            => $tipo,
            'reason'          => $request->reason,
            'bank_name'       => $bankAccount->bank_name,
            'account_number'  => $bankAccount->account_number,
            'account_type'    => $bankAccount->account_type,
            'cci'             => $bankAccount->cci,
        ]);

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
            'institution'     => 'required|string|max:200',
            'career'          => 'required|string|max:200',
            'academic_status' => 'required|string',
            'has_children'    => 'required|boolean',
            'instagram'       => 'nullable|string|max:100',
            'is_employed'     => 'required|boolean',
            'marital_status'  => 'required|in:soltero,casado_separado,casado_compartido',
            'spouse_name'     => 'required_if:marital_status,casado_compartido|nullable|string|max:200',
            'spouse_dni'      => 'required_if:marital_status,casado_compartido|nullable|digits:8',
        ]);

        Auth::user()->clientProfile->update([
            'institution'     => $request->institution,
            'career'          => $request->career,
            'academic_status' => $request->academic_status,
            'has_children'    => $request->has_children,
            'instagram'       => $request->instagram,
            'is_employed'     => $request->is_employed,
            'marital_status'  => $request->marital_status,
            'spouse_name'     => $request->marital_status === 'casado_compartido' ? $request->spouse_name : null,
            'spouse_dni'      => $request->marital_status === 'casado_compartido' ? $request->spouse_dni : null,
        ]);

        return redirect()->route('client.loans')->with('success', 'Perfil actualizado correctamente.');
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'institution'     => 'required|string|max:200',
            'career'          => 'required|string|max:200',
            'academic_status' => 'required|string',
            'has_children'    => 'required|boolean',
            'instagram'       => 'nullable|string|max:100',
            'is_employed'     => 'required|boolean',
            'marital_status'  => 'required|in:soltero,casado_separado,casado_compartido',
            'spouse_name'     => 'required_if:marital_status,casado_compartido|nullable|string|max:200',
            'spouse_dni'      => 'required_if:marital_status,casado_compartido|nullable|digits:8',
        ], [
            'spouse_name.required_if' => 'El nombre del cónyuge es obligatorio.',
            'spouse_dni.required_if'  => 'El DNI del cónyuge es obligatorio.',
            'spouse_dni.digits'       => 'El DNI debe tener 8 dígitos.',
        ]);

        ClientProfile::create([
            'user_id'         => Auth::id(),
            'institution'     => $request->institution,
            'career'          => $request->career,
            'academic_status' => $request->academic_status,
            'has_children'    => $request->has_children,
            'instagram'       => $request->instagram,
            'is_employed'     => $request->is_employed,
            'marital_status'  => $request->marital_status,
            'spouse_name'     => $request->marital_status === 'casado_compartido' ? $request->spouse_name : null,
            'spouse_dni'      => $request->marital_status === 'casado_compartido' ? $request->spouse_dni : null,
        ]);

        return redirect()->route('client.loans')->with('success', 'Perfil completado. Ya puedes gestionar tus préstamos.');
    }
}
