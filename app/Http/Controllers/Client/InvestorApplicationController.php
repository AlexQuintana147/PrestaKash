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
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingApplication) {
            if ($existingApplication->status === 'approved') {
                return redirect()->route('client.dashboard')->with('info', 'Ya eres un inversor verificado.');
            }
            return redirect()->route('client.invertir')->with('info', 'Tu solicitud ya está siendo procesada.');
        }

        return view('client.invest.apply');
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'occupation' => 'required|string|max:255',
            'intended_amount' => 'required|numeric|min:100',
            'source_of_funds' => 'required|string|max:255',
            'experience' => 'nullable|string',
            'preferred_term' => 'required|string',
        ]);

        InvestorApplication::create([
            'user_id' => Auth::id(),
            'phone' => $request->phone,
            'occupation' => $request->occupation,
            'intended_amount' => $request->intended_amount,
            'source_of_funds' => $request->source_of_funds,
            'experience' => $request->experience,
            'preferred_term' => $request->preferred_term,
            'status' => 'pending',
        ]);

        return redirect()->route('client.invertir')->with('success', 'Tu solicitud ha sido enviada con éxito. Nos pondremos en contacto contigo pronto.');
    }
}
