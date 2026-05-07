<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'company_logo' => CompanySetting::get('company_logo'),
            'company_ruc' => CompanySetting::get('company_ruc'),
            'company_signature' => CompanySetting::get('company_signature'),
        ];

        return view('superadmin.settings', $settings);
    }

    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Auth::user()->update(['name' => $request->name]);

        return back()->with('success_name', 'Nombre actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success_password', 'Contraseña actualizada correctamente.');
    }

    public function updateCompanySettings(Request $request)
    {
        $request->validate([
            'company_ruc' => 'nullable|string|max:20',
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'company_signature' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        if ($request->hasFile('company_logo')) {
            $logo = $request->file('company_logo');
            $path = $logo->storeAs('company', 'logo.webp', 'public');
            CompanySetting::set('company_logo', $path);
        }

        if ($request->hasFile('company_signature')) {
            $signature = $request->file('company_signature');
            $path = $signature->storeAs('company', 'signature.webp', 'public');
            CompanySetting::set('company_signature', $path);
        }

        if ($request->filled('company_ruc')) {
            CompanySetting::set('company_ruc', $request->company_ruc);
        }

        return back()->with('success_company', 'Configuración de empresa actualizada correctamente.');
    }
}
