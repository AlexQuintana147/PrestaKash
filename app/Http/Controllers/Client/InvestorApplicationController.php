<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InvestorApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestorApplicationController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        
        // Verificar si ya tiene una solicitud pendiente o aprobada
        $existingApplication = InvestorApplication::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest()
            ->first();

        if ($existingApplication) {
            if ($existingApplication->status === 'approved') {
                return redirect()->route('client.dashboard')->with('info', 'Ya eres un inversor verificado.');
            }
            
            if ($existingApplication->status === 'pending') {
                return redirect()->route('client.invertir')->with('info', 'Tu solicitud ya está siendo procesada.');
            }

            if ($existingApplication->status === 'rejected') {
                $nextEligibleDate = $existingApplication->updated_at->addMonth();
                if (now()->lessThan($nextEligibleDate)) {
                    return redirect()->route('client.invertir')->with('error', 'De momento no eres apto para invertir. Podrás volver a realizar el formulario el próximo mes (' . $nextEligibleDate->format('d/m/Y') . ').');
                }
            }
        }

        return view('client.invest.apply');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Doble verificación de seguridad
        $existingApplication = InvestorApplication::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest()
            ->first();

        if ($existingApplication) {
            if ($existingApplication->status === 'approved') return redirect()->route('client.dashboard');
            if ($existingApplication->status === 'pending') return redirect()->route('client.invertir');
            if ($existingApplication->status === 'rejected' && now()->lessThan($existingApplication->updated_at->addMonth())) {
                return redirect()->route('client.invertir');
            }
        }

        $request->validate([
            'phone' => 'required|string',
            'occupation' => 'required|string|max:255',
            'currency' => 'required|string|in:PEN,USD',
            'intended_amount' => 'required|numeric|min:100',
            'source_of_funds' => 'required|string|max:255',
            'custom_source' => 'required_if:source_of_funds,Otros|nullable|string|max:255',
            'experience' => 'nullable|string',
            'preferred_term' => 'required|string|in:6 meses,12 meses',
        ]);

        $sourceOfFunds = $request->source_of_funds;
        if ($sourceOfFunds === 'Otros' && $request->custom_source) {
            $sourceOfFunds = 'Otros: ' . $request->custom_source;
        }

        InvestorApplication::create([
            'user_id' => Auth::id(),
            'phone' => $request->phone,
            'occupation' => $request->occupation,
            'currency' => $request->currency,
            'intended_amount' => $request->intended_amount,
            'source_of_funds' => $sourceOfFunds,
            'experience' => $request->experience,
            'preferred_term' => $request->preferred_term,
            'status' => 'pending',
        ]);

        return redirect()->route('client.invertir')->with('success', 'Tu solicitud ha sido enviada con éxito. Nos pondremos en contacto contigo pronto.');
    }
}
