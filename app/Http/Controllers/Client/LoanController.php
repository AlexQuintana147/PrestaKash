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
        return view('client.loans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:100',
            'months'         => 'required|integer|min:1|max:24',
            'type'           => 'required|in:cuotas,unico',
            'reason'         => 'required|string|min:10|max:1000',
            'bank_name'      => 'required|string',
            'account_number' => 'required|string|min:10',
            'account_type'   => 'required|in:ahorros,corriente',
            'cci'            => 'nullable|string|min:20',
        ]);

        $montoSolicitado = $request->amount;
        $plazo = $request->months;
        $tipo = $request->type;
        $reason = $request->reason;

        // Lógica de simulación profesional (20% interés fijo)
        $tasaInteres = 0.20;
        $interes = $montoSolicitado * $tasaInteres;

        // Mantener cargos mínimos por operación (opcional, pero el usuario pidió 100->120)
        // Si el usuario quiere 100 -> 120, entonces no hay otros cargos.
        $tarifaTecnologia = 0; // Desactivado según ejemplo 100->120
        $igv = 0;

        $totalAPagar = $montoSolicitado + $interes;
        $cuotaMensual = $totalAPagar / $plazo;

        Loan::create([
            'user_id'         => Auth::id(),
            'amount'          => $montoSolicitado,
            'months'          => $plazo,
            'interest_rate'   => 20.00,
            'technology_fee'  => $tarifaTecnologia,
            'igv'             => $igv,
            'total_amount'    => $totalAPagar,
            'monthly_payment' => $cuotaMensual,
            'status'          => 'pending',
            'type'            => $tipo,
            'reason'          => $reason,
            'bank_name'      => $request->bank_name,
            'account_number' => $request->account_number,
            'account_type'   => $request->account_type,
            'cci'            => $request->cci,
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
