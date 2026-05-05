<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function step1()
    {
        return view('auth.onboarding.step1');
    }

    public function step1Store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        Auth::user()->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()->route('onboarding.step2');
    }

    public function step2()
    {
        $user = Auth::user();

        // Bloquear si el paso 1 no está completo
        if (!$user->phone || !$user->name) {
            return redirect()->route('onboarding.step1')
                ->with('error', 'Debes completar tus datos personales primero.');
        }

        return view('auth.onboarding.step2');
    }

    public function step2Store(Request $request, ImageService $imageService)
    {
        $user = Auth::user();

        // Bloquear si el paso 1 no está completo
        if (!$user->phone || !$user->name) {
            return redirect()->route('onboarding.step1')
                ->with('error', 'Debes completar tus datos personales primero.');
        }
        $request->validate([
            'dni_front'    => 'required|image|max:5120',
            'dni_back'     => 'required|image|max:5120',
            'utility_bill' => 'required|image|max:5120',
        ]);

        $user = Auth::user();

        $dniFront    = $imageService->storeAsWebp($request->file('dni_front'),    'documents/' . $user->id, 1200);
        $dniBack     = $imageService->storeAsWebp($request->file('dni_back'),     'documents/' . $user->id, 1200);
        $utilityBill = $imageService->storeAsWebp($request->file('utility_bill'), 'documents/' . $user->id, 1200);

        UserDocument::updateOrCreate(
            ['user_id' => $user->id],
            [
                'dni_front'    => $dniFront,
                'dni_back'     => $dniBack,
                'utility_bill' => $utilityBill,
                'status'       => 'pending',
            ]
        );

        $user->update(['profile_completed' => true]);

        return redirect('/')->with('success', '¡Registro completado! Tu información está en revisión.');
    }
}
