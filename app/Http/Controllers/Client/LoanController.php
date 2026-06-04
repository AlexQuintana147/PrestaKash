<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\Loan;
use App\Models\ProfileChangeRequest;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function editProfile()
    {
        $pendingChange = ProfileChangeRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->exists();

        if ($pendingChange) {
            return redirect()->route('client.loans')->with('error', 'Ya tienes una solicitud de cambio pendiente. Debes esperar a que sea aprobada o rechazada.');
        }

        $profile = Auth::user()->clientProfile;
        return view('client.loans.profile-form', ['editing' => true, 'profile' => $profile]);
    }

    public function dismissProfileChange()
    {
        ProfileChangeRequest::where('user_id', Auth::id())
            ->where('status', 'rejected')
            ->update(['status' => 'dismissed']);

        return response()->json(['ok' => true]);
    }

    public function index()
    {
        $user = Auth::user()->load('clientProfile');
        $profile = $user->clientProfile;

        // Si no tiene perfil, mostrar formulario
        if (!$profile) {
            return view('client.loans.profile-form');
        }

        $loans = $user->loans()->with('installments')->latest()->take(5)->get();

        // Asegurar que las contraofertas siempre estén incluidas
        $counterOfferIds = $user->loans()->where('status', 'counter_offered')->pluck('id');
        if ($counterOfferIds->isNotEmpty()) {
            $missingIds = $counterOfferIds->diff($loans->pluck('id'));
            if ($missingIds->isNotEmpty()) {
                $missingLoans = $user->loans()->with('installments')->whereIn('id', $missingIds)->get();
                $loans = $loans->merge($missingLoans);
            }
        }

        // Marcar notificaciones de préstamos como vistas
        session(['loans_last_visited_at' => now()]);

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
            'disbursement_date' => 'required|date|after_or_equal:today',
        ];

        if ($request->type === 'cuotas') {
            $rules['months'] = 'required|integer|min:2|max:24';
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
            $tasaInteres = $paymentDays === 15 ? round($defaultRate / 2, 4) : $defaultRate;
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
            'disbursement_date' => $request->disbursement_date,
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
        $existingPending = ProfileChangeRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return redirect()->route('client.loans')->with('error', 'Ya tienes una solicitud de cambio pendiente. Debes esperar a que sea aprobada o rechazada.');
        }

        $request->validate([
            'institution' => ['required_unless:academic_status,Ninguno de los anteriores', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'career' => ['required_if:main_academic_status,Educación superior', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'academic_status' => 'required|string',
            'has_children' => 'required|boolean',
            'children_count' => 'required_if:has_children,1|nullable|integer|min:1|max:20',
            'instagram' => 'nullable|string|max:100',
            'is_employed' => 'required|boolean',
            'employment_type' => 'required_if:is_employed,1|nullable|in:dependiente,independiente',
            'workplace' => ['required_if:employment_type,dependiente', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+$/u'],
            'job_title' => ['required_if:employment_type,dependiente', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+$/u'],
            'independent_type' => 'required_if:employment_type,independiente|nullable|in:comerciante,empresario,emprendedor',
            'monthly_income' => 'required|numeric|min:100',
            'marital_status' => 'required|in:soltero,casado_separado,casado_compartido',
            'spouse_first_name' => ['required_if:marital_status,casado_compartido', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+$/u'],
            'spouse_last_name' => ['required_if:marital_status,casado_compartido', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]+\s+[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]+(\s+[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]+)*$/u'],
            'spouse_dni' => 'required_if:marital_status,casado_compartido|nullable|digits:8',
        ], [
            'institution.regex' => 'La institución solo debe contener letras.',
            'career.regex' => 'La carrera solo debe contener letras.',
            'spouse_first_name.regex' => 'Los nombres solo deben contener letras.',
            'spouse_last_name.regex' => 'Debe ingresar al menos dos apellidos (solo letras).',
            'workplace.regex' => 'El nombre de la empresa solo debe contener letras.',
            'job_title.regex' => 'El cargo solo debe contener letras.',
            'workplace.required_if' => 'El nombre de la empresa es obligatorio cuando el tipo de empleo es dependiente.',
            'job_title.required_if' => 'El cargo es obligatorio cuando el tipo de empleo es dependiente.',
            'independent_type.required_if' => 'El tipo de independiente es obligatorio cuando el tipo de empleo es independiente.',
            'spouse_first_name.required_if' => 'Los nombres del cónyuge son obligatorios.',
            'spouse_last_name.required_if' => 'Los apellidos del cónyuge son obligatorios.',
            'spouse_dni.required_if' => 'El DNI del cónyuge es obligatorio.',
            'children_count.required_if' => 'Debe indicar cuántos hijos tiene.',
            'children_count.max' => 'El número de hijos no puede ser mayor a 20.',
            'monthly_income.required' => 'Debe ingresar su ingreso mensual.',
            'monthly_income.min' => 'El ingreso mensual debe ser al menos S/ 100.00.',
        ]);

        $profile = Auth::user()->clientProfile;

        $newData = [
            'institution' => $request->institution,
            'career' => $request->career,
            'academic_status' => $request->academic_status,
            'has_children' => $request->has_children,
            'children_count' => $request->has_children ? $request->children_count : null,
            'instagram' => $request->instagram,
            'is_employed' => $request->is_employed,
            'employment_type' => $request->is_employed ? $request->employment_type : null,
            'workplace' => ($request->is_employed && $request->employment_type === 'dependiente') ? $request->workplace : null,
            'job_title' => ($request->is_employed && $request->employment_type === 'dependiente') ? $request->job_title : null,
            'independent_type' => ($request->is_employed && $request->employment_type === 'independiente') ? $request->independent_type : null,
            'monthly_income' => $request->monthly_income,
            'marital_status' => $request->marital_status,
            'spouse_first_name' => $request->marital_status === 'casado_compartido' ? $request->spouse_first_name : null,
            'spouse_last_name' => $request->marital_status === 'casado_compartido' ? $request->spouse_last_name : null,
            'spouse_dni' => $request->marital_status === 'casado_compartido' ? $request->spouse_dni : null,
        ];

        $oldData = $profile->only(array_keys($newData));

        $changed = collect($newData)->filter(function ($value, $key) use ($oldData) {
            $old = $oldData[$key] ?? null;
            return (string) $old !== (string) $value;
        });

        if ($changed->isEmpty()) {
            return redirect()->route('client.loans')->with('info', 'No se detectaron cambios en tu perfil.');
        }

        ProfileChangeRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'cancelled', 'reviewed_at' => now()]);

        ProfileChangeRequest::create([
            'user_id' => Auth::id(),
            'old_data' => $oldData,
            'new_data' => $newData,
            'status' => 'pending',
        ]);

        return redirect()->route('client.loans')->with('success', 'Tus cambios están pendientes de aprobación por el administrador.');
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'institution' => ['required_unless:academic_status,Ninguno de los anteriores', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'career' => ['required_if:main_academic_status,Educación superior', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.]+$/u'],
            'academic_status' => 'required|string',
            'has_children' => 'required|boolean',
            'children_count' => 'required_if:has_children,1|nullable|integer|min:1|max:20',
            'instagram' => 'nullable|string|max:100',
            'is_employed' => 'required|boolean',
            'employment_type' => 'required_if:is_employed,1|nullable|in:dependiente,independiente',
            'workplace' => ['required_if:employment_type,dependiente', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+$/u'],
            'job_title' => ['required_if:employment_type,dependiente', 'nullable', 'string', 'max:200', 'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+$/u'],
            'independent_type' => 'required_if:employment_type,independiente|nullable|in:comerciante,empresario,emprendedor',
            'monthly_income' => 'required|numeric|min:100',
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
            'workplace.regex' => 'El nombre de la empresa solo debe contener letras.',
            'job_title.regex' => 'El cargo solo debe contener letras.',
            'workplace.required_if' => 'El nombre de la empresa es obligatorio cuando el tipo de empleo es dependiente.',
            'job_title.required_if' => 'El cargo es obligatorio cuando el tipo de empleo es dependiente.',
            'independent_type.required_if' => 'El tipo de independiente es obligatorio cuando el tipo de empleo es independiente.',
            'spouse_dni.required_if' => 'El DNI del cónyuge es obligatorio.',
            'spouse_dni.digits' => 'El DNI debe tener 8 dígitos.',
            'employment_type.required_if' => 'Debe especificar si es dependiente o independiente.',
            'children_count.required_if' => 'Debe indicar cuántos hijos tiene.',
            'children_count.max' => 'El número de hijos no puede ser mayor a 20.',
            'monthly_income.required' => 'Debe ingresar su ingreso mensual.',
            'monthly_income.min' => 'El ingreso mensual debe ser al menos S/ 100.00.',
        ]);

        // Convertir formato de moneda (ej: "2,000.00" -> 2000.00)
        $incomeValue = str_replace(',', '', $request->monthly_income);
        $incomeValue = floatval($incomeValue);

        ClientProfile::create([
            'user_id' => Auth::id(),
            'institution' => $request->institution,
            'career' => $request->career,
            'academic_status' => $request->academic_status,
            'has_children' => $request->has_children,
            'children_count' => $request->has_children ? $request->children_count : null,
            'instagram' => $request->instagram,
            'is_employed' => $request->is_employed,
            'employment_type' => $request->is_employed ? $request->employment_type : null,
            'workplace' => ($request->is_employed && $request->employment_type === 'dependiente') ? $request->workplace : null,
            'job_title' => ($request->is_employed && $request->employment_type === 'dependiente') ? $request->job_title : null,
            'independent_type' => ($request->is_employed && $request->employment_type === 'independiente') ? $request->independent_type : null,
            'monthly_income' => $request->monthly_income,
            'marital_status' => $request->marital_status,
            'spouse_first_name' => $request->marital_status === 'casado_compartido' ? $request->spouse_first_name : null,
            'spouse_last_name' => $request->marital_status === 'casado_compartido' ? $request->spouse_last_name : null,
            'spouse_dni' => $request->marital_status === 'casado_compartido' ? $request->spouse_dni : null,
        ]);

        return redirect()->route('client.loans')->with('success', 'Perfil completado. Ya puedes gestionar tus préstamos.');
    }

    public function history()
    {
        $user = Auth::user();

        if (!$user->clientProfile) {
            return redirect()->route('client.loans');
        }

        $loans = $user->loans()
            ->whereIn('status', ['approved', 'paid', 'counter_accepted'])
            ->with('installments')
            ->latest()
            ->get();

        return view('client.loans.history', compact('loans'));
    }

    public function historyShow(Loan $loan)
    {
        if ($loan->user_id !== Auth::id())
            abort(403);
        $loan->load('installments.interestPayments');
        return view('client.loans.history-show', compact('loan'));
    }

    public function uploadVoucher(Request $request, \App\Models\LoanInstallment $installment, ImageService $imageService)
    {
        if ($installment->loan->user_id !== Auth::id())
            abort(403);

        // 1. Bloquear si ya está en revisión o hay alguna cuota en revisión
        if ($installment->status === 'processing' || $installment->loan->installments()->where('status', 'processing')->exists()) {
            return back()->withErrors(['error' => 'Ya tienes un pago en revisión. Por favor espera a que sea validado.']);
        }

        // 2. Bloquear si no es el siguiente pago disponible en orden
        // (Buscamos si hay algún pago pendiente o rechazado antes de este)
        $previousUnsubmitted = $installment->loan->installments()
            ->where('installment_number', '<', $installment->installment_number)
            ->whereIn('status', ['pending', 'rejected'])
            ->exists();

        if ($previousUnsubmitted) {
            return back()->withErrors(['error' => 'Debes completar los pagos anteriores en orden.']);
        }

        // 3. Si el préstamo está en mora, solo permitir pagos de mora
        $requestedType = $request->requested_type ?? 'full';
        if ($installment->loan->in_default && !in_array($requestedType, ['mora_interest', 'mora_full'])) {
            return back()->withErrors(['error' => 'Tu préstamo está en mora. Debes pagar la mora pendiente antes de realizar otros pagos.']);
        }

        // 4. Si es pago mora_full, validar que no haya deudas anteriores
        if ($requestedType === 'mora_full') {
            $overdueCount = $installment->loan->overdue_installments->count();
            if ($overdueCount > 1) {
                return back()->withErrors(['error' => 'La opción "Mora + cuota total" solo está disponible si no tienes deudas anteriores pendientes.']);
            }
        }

        // 5. Si es pago total, validar que sea el día de vencimiento de la cuota y sin deudas vencidas
        if ($requestedType === 'total') {
            if (!$installment->due_date->isToday()) {
                return back()->withErrors(['error' => 'La opción "Pago Total" solo está disponible el día de vencimiento de la cuota.']);
            }
            if ($installment->loan->overdue_installments->count() > 0) {
                return back()->withErrors(['error' => 'La opción "Pago Total" no está disponible si tienes deudas vencidas pendientes.']);
            }
        }

        // 6. Si es amortización parcial, validar monto y tipo
        if ($requestedType === 'partial') {
            $request->validate([
                'amortization_amount' => 'required|numeric',
                'amortization_type' => 'required|in:reduce_amount,reduce_term',
            ]);

            $amortAmount = floatval($request->amortization_amount);
            $cuotaAmount = $installment->amount + ($installment->late_fee ?? 0);
            $paidCapital = $installment->loan->installments()->where('status', 'approved')->sum('capital');
            $pendingCapital = max(0, $installment->loan->amount - $paidCapital);
            $totalDebt = $pendingCapital + $installment->interest;

            if ($amortAmount < $cuotaAmount) {
                return back()->withErrors(['error' => 'El monto no puede ser menor a la cuota.']);
            }
            if ($amortAmount >= $totalDebt) {
                return back()->withErrors(['error' => 'El monto no puede ser mayor o igual a la deuda total. Usa "Pago Total" para cancelar todo.']);
            }
            if ($installment->loan->overdue_installments->count() > 0) {
                return back()->withErrors(['error' => 'La amortización parcial no está disponible si tienes deudas vencidas pendientes.']);
            }
        }

        $request->validate([
            'payment_voucher' => 'required|image|max:5120',
            'payment_method' => 'required|in:transfer,cash',
            'bank_name' => 'required_if:payment_method,transfer|nullable|string|max:255',
            'bank_name_custom' => 'required_if:bank_name,Otros|nullable|string|max:255',
            'operation_number' => 'required_if:payment_method,transfer|nullable|alpha_num|max:25',
            'requested_type' => 'nullable|in:full,interest,mora_interest,mora_full,total,partial',
        ]);

        $path = $imageService->storeAsWebp($request->file('payment_voucher'), 'vouchers/' . Auth::id(), 1200);

        $bankName = $request->payment_method === 'cash' ? 'Efectivo' : $request->bank_name;
        if ($bankName === 'Otros' && $request->bank_name_custom) {
            $bankName = $request->bank_name_custom;
        }

        $installment->update([
            'payment_voucher' => $path,
            'payment_method' => $request->payment_method,
            'bank_name' => $bankName,
            'operation_number' => $request->operation_number,
            'requested_type' => $request->requested_type ?? 'full',
            'amortization_type' => $requestedType === 'partial' ? $request->amortization_type : null,
            'amortization_extra' => $requestedType === 'partial' ? round(floatval($request->amortization_amount) - ($installment->amount + ($installment->late_fee ?? 0)), 2) : null,
            'status' => 'processing',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Comprobante enviado. En breve será verificado por un administrador.');
    }

    public function acceptCounterOffer(Loan $loan)
    {
        if ($loan->user_id !== Auth::id() || $loan->status !== 'counter_offered') {
            abort(403);
        }

        $loan->update(['status' => 'counter_accepted']);

        return back()->with('success', 'Has aceptado la contra-oferta. Tu préstamo está en espera de desembolso.');
    }

    public function rejectCounterOffer(Loan $loan)
    {
        if ($loan->user_id !== Auth::id() || $loan->status !== 'counter_offered') {
            abort(403);
        }

        $loan->update(['status' => 'rejected']);

        return back()->with('success', 'Has rechazado la contra-oferta.');
    }
}
