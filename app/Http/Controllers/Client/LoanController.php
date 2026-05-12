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

        $loans = $user->loans()->with('installments')->latest()->take(5)->get();
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
            $rules['payment_frequency'] = 'required_if:amortization_system,frances|in:diario,semanal,quincenal,mensual';
        } else {
            $rules['payment_days'] = 'required|in:15,30';
        }

        $request->validate($rules);

        $bankAccount = \App\Models\BankAccount::find($request->bank_account_id);

        if ($bankAccount->currency !== $request->currency) {
            return back()->withErrors(['bank_account_id' => 'La cuenta bancaria seleccionada debe estar en ' . ($request->currency === 'PEN' ? 'Soles' : 'Dólares') . '.'])->withInput();
        }

        $montoSolicitado = (float) $request->amount;
        $tipo = $request->type;
        $user = Auth::user();
        $defaultRate = $user->loan_interest_rate ? ($user->loan_interest_rate / 100) : 0.20;
        $system = $request->amortization_system ?? 'frances';
        $freq = ($tipo === 'cuotas' && $system === 'frances')
            ? ($request->payment_frequency ?? 'mensual')
            : 'mensual';

        // ── Lógica de comisión y cálculo de cuotas ──
        $nTotal = 0;
        if ($tipo === 'cuotas') {
            $monthlyRate = $defaultRate;
            $months = (int) $request->months;
            $paymentDays = null;

            if ($system === 'frances') {
                $installmentsPerMonth = match ($freq) {
                    'diario' => 30,
                    'semanal' => 4,
                    'quincenal' => 2,
                    'mensual' => 1,
                    default => 1,
                };

                $nTotal = $months * $installmentsPerMonth;
                $periodicRate = $monthlyRate / $installmentsPerMonth;

                if ($montoSolicitado > 0) {
                    $cuota = ($montoSolicitado * $periodicRate) / (1 - pow(1 + $periodicRate, -$nTotal));
                    $totalAPagar = $cuota * $nTotal;
                    $interes = $totalAPagar - $montoSolicitado;
                } else {
                    $cuota = 0;
                    $totalAPagar = 0;
                    $interes = 0;
                }
            } else {
                // Sistema Americano (Siempre mensual)
                $nTotal = $months;
                $interes = ($montoSolicitado * $monthlyRate) * $months;
                $totalAPagar = $montoSolicitado + $interes;
                $cuota = $montoSolicitado * $monthlyRate; // Interés mensual
            }
        } else {
            $nTotal = 1;
            $paymentDays = (int) $request->payment_days;
            $tasaInteres = $paymentDays === 15 ? 0.10 : $defaultRate;
            $interes = $montoSolicitado * $tasaInteres;
            $totalAPagar = $montoSolicitado + $interes;
            $cuota = $totalAPagar; // un solo pago
            $months = null;
        }

        $loanData = [
            'user_id' => Auth::id(),
            'amount' => $montoSolicitado,
            'currency' => $request->currency,
            'months' => $months,
            'payment_days' => $paymentDays,
            'interest_rate' => ($tipo === 'cuotas' ? $defaultRate : $tasaInteres) * 100,
            'technology_fee' => 0,
            'igv' => 0,
            'total_amount' => $totalAPagar,
            'monthly_payment' => $cuota,
            'status' => 'pending',
            'type' => $tipo,
            'payment_frequency' => $freq,
            'amortization_system' => $system,
            'reason' => $request->reason,
            'bank_name' => $bankAccount->bank_name,
            'account_number' => $bankAccount->account_number,
            'account_type' => $bankAccount->account_type,
            'cci' => $bankAccount->cci,
        ];

        $loan = Loan::create($loanData);

        // NOTE: Installments are now generated by Admin upon approval.
        // This ensures the schedule starts from the approval date.

        return redirect()->route('client.loans')->with('success', 'Tu solicitud de préstamo ha sido recibida y está en proceso de revisión.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'institution' => ['required_unless:academic_status,Ninguno de los anteriores', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'career' => ['required_unless:academic_status,Ninguno de los anteriores', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
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
            'institution' => ['required_unless:academic_status,Ninguno de los anteriores', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'career' => ['required_unless:academic_status,Ninguno de los anteriores', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
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

    public function history()
    {
        $loans = Auth::user()->loans()->with('installments')->latest()->get();
        return view('client.loans.history', compact('loans'));
    }

    public function historyShow(Loan $loan)
    {
        if ($loan->user_id !== Auth::id())
            abort(403);
        $loan->load('installments');
        return view('client.loans.history-show', compact('loan'));
    }

    public function uploadVoucher(Request $request, \App\Models\LoanInstallment $installment)
    {
        if ($installment->loan->user_id !== Auth::id())
            abort(403);

        $request->validate([
            'payment_voucher' => 'required|image|max:5120',
        ]);

        $path = $request->file('payment_voucher')->store('vouchers/' . Auth::id(), 'public');

        $installment->update([
            'payment_voucher' => $path,
            'status' => 'processing',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Comprobante enviado. En breve será verificado por un administrador.');
    }
}
