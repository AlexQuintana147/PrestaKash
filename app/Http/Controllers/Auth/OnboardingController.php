<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'first_name' => ['required', 'string', 'max:60', 'regex:/^[\pL\s]+$/u'],
            'last_name' => [
                'required',
                'string',
                'max:60',
                'regex:/^[\pL\s]+$/u',
                function ($attribute, $value, $fail) {
                    $words = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);
                    if (count($words) < 2) {
                        $fail('Debes ingresar al menos dos apellidos.');
                    }
                },
            ],
            'dni' => ['required', 'digits:8', 'unique:users,dni,' . auth()->id()],
            'phone' => ['required', 'digits_between:9,9', 'regex:/^9\d{8}$/', 'unique:users,phone,' . auth()->id()],
            'person_type' => ['required', 'in:natural,juridica'],
            'ruc' => ['required_if:person_type,juridica', 'nullable', 'digits:11'],
            'business_name' => ['required_if:person_type,juridica', 'nullable', 'string', 'max:200'],
            'department' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:200'],
        ], [
            'first_name.regex' => 'El nombre solo debe contener letras.',
            'last_name.regex' => 'Los apellidos solo deben contener letras.',
            'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'phone.digits_between' => 'El teléfono debe tener 9 dígitos.',
            'phone.regex' => 'El número debe empezar con 9 (celular peruano).',
            'phone.unique' => 'Este número de teléfono ya está registrado.',
        ]);

        $fullName = $request->first_name . ' ' . $request->last_name;

        Auth::user()->update([
            'name' => $fullName,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'dni' => $request->dni,
            'phone' => $request->phone,
            'person_type' => $request->person_type,
            'ruc' => $request->ruc,
            'business_name' => $request->business_name,
            'department' => $request->department,
            'province' => $request->province,
            'district' => $request->district,
            'address' => $request->address,
            'step1_completed' => true,
            'referral_code' => Auth::user()->referral_code ?? User::generateReferralCode(),
        ]);

        if (session('referral_code') && !Auth::user()->referred_by) {
            $referrer = User::where('referral_code', session('referral_code'))->first();
            if ($referrer) {
                Auth::user()->update(['referred_by' => $referrer->id]);
            }
            session()->forget('referral_code');
        }

        return redirect()->route('onboarding.step2');
    }

    public function step2()
    {
        return view('auth.onboarding.step2');
    }

    public function step2Store(Request $request, ImageService $imageService)
    {
        $request->validate([
            'selfie' => 'required|string',
            'dni_front' => 'required|image|max:5120',
            'dni_back' => 'required|image|max:5120',
            'utility_bill' => 'required|image|max:5120',
        ], [
            'selfie.required' => 'Debes tomar una foto selfie.',
        ]);

        $user = Auth::user();

        // Procesar selfie desde base64 y guardar como WebP
        $selfiePath = $imageService->storeBase64AsWebp(
            $request->input('selfie'),
            'documents/' . $user->id,
            1200
        );

        $dniFront = $imageService->storeAsWebp($request->file('dni_front'), 'documents/' . $user->id, 1200);
        $dniBack = $imageService->storeAsWebp($request->file('dni_back'), 'documents/' . $user->id, 1200);
        $utilityBill = $imageService->storeAsWebp($request->file('utility_bill'), 'documents/' . $user->id, 1200);

        UserDocument::updateOrCreate(
            ['user_id' => $user->id],
            [
                'selfie' => $selfiePath,
                'dni_front' => $dniFront,
                'dni_back' => $dniBack,
                'utility_bill' => $utilityBill,
                'status' => 'pending',
            ]
        );

        $user->update(['profile_completed' => true]);

        return view('auth.onboarding.pending');
    }

    public function checkDni(Request $request)
    {
        $exists = \App\Models\User::where('dni', $request->dni)
            ->where('id', '!=', auth()->id())
            ->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkPhone(Request $request)
    {
        $exists = \App\Models\User::where('phone', $request->phone)
            ->where('id', '!=', auth()->id())
            ->exists();
        return response()->json(['exists' => $exists]);
    }
}
