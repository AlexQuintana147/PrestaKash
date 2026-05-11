<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'company_logo' => CompanySetting::get('company_logo'),
            'company_ruc' => CompanySetting::get('company_ruc'),
            'company_signature' => CompanySetting::get('company_signature'),
            'company_razon_social' => CompanySetting::get('company_razon_social'),
            'company_address' => CompanySetting::get('company_address'),
            'company_email' => CompanySetting::get('company_email'),
            'company_phone' => CompanySetting::get('company_phone'),
            'representative_name' => CompanySetting::get('representative_name'),
            'representative_last_name' => CompanySetting::get('representative_last_name'),
            'representative_dni' => CompanySetting::get('representative_dni'),
            'representative_email' => CompanySetting::get('representative_email'),
            'representative_phone' => CompanySetting::get('representative_phone'),
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
            'company_ruc' => 'nullable|numeric|digits_between:8,20',
            'company_razon_social' => 'nullable|string|max:255|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u',
            'company_address' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:20',
            'representative_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u',
            'representative_last_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u',
            'representative_dni' => 'nullable|numeric|digits:8',
            'representative_email' => 'nullable|email|max:255',
            'representative_phone' => 'nullable|string|max:20',
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'company_signature' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ], [
            'company_ruc.numeric' => 'El RUC debe contener solo números.',
            'company_ruc.digits_between' => 'El RUC debe tener entre 8 y 20 dígitos.',
            'company_razon_social.regex' => 'La Razón Social solo debe contener letras.',
            'representative_name.regex' => 'El nombre del representante solo debe contener letras.',
            'representative_last_name.regex' => 'Los apellidos del representante solo deben contener letras.',
            'representative_dni.digits' => 'El DNI debe tener 8 dígitos.',
        ]);

        $manager = new ImageManager(new Driver());

        if ($request->hasFile('company_logo')) {
            $logo = $request->file('company_logo');
            $img = $manager->read($logo->getRealPath())->encodeByExtension('webp');
            $path = 'company/logo_' . time() . '.webp';
            
            // Delete old logo if exists
            $oldLogo = CompanySetting::get('company_logo');
            if ($oldLogo) Storage::disk('public')->delete($oldLogo);
            
            Storage::disk('public')->put($path, $img->toString());
            CompanySetting::set('company_logo', $path);
        }

        if ($request->hasFile('company_signature')) {
            $signature = $request->file('company_signature');
            $img = $manager->read($signature->getRealPath())->encodeByExtension('webp');
            $path = 'company/signature_' . time() . '.webp';

            // Delete old signature if exists
            $oldSignature = CompanySetting::get('company_signature');
            if ($oldSignature) Storage::disk('public')->delete($oldSignature);

            Storage::disk('public')->put($path, $img->toString());
            CompanySetting::set('company_signature', $path);
        }

        if ($request->has('company_ruc')) {
            CompanySetting::set('company_ruc', $request->company_ruc);
        }

        if ($request->has('company_razon_social')) {
            CompanySetting::set('company_razon_social', $request->company_razon_social);
        }

        if ($request->has('company_address')) {
            CompanySetting::set('company_address', $request->company_address);
        }

        if ($request->has('company_email')) {
            CompanySetting::set('company_email', $request->company_email);
        }

        if ($request->has('company_phone')) {
            CompanySetting::set('company_phone', $request->company_phone);
        }

        if ($request->has('representative_name')) {
            CompanySetting::set('representative_name', $request->representative_name);
        }

        if ($request->has('representative_last_name')) {
            CompanySetting::set('representative_last_name', $request->representative_last_name);
        }

        if ($request->has('representative_dni')) {
            CompanySetting::set('representative_dni', $request->representative_dni);
        }

        if ($request->has('representative_email')) {
            CompanySetting::set('representative_email', $request->representative_email);
        }

        if ($request->has('representative_phone')) {
            CompanySetting::set('representative_phone', $request->representative_phone);
        }

        return back()->with('success_company', 'Configuración de empresa actualizada correctamente.');
    }
}
