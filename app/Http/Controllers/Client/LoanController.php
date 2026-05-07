<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
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

        return view('client.loans.index', compact('profile'));
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
