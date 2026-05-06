<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\UbigeoDepartment;
use App\Models\UbigeoDistrict;
use App\Models\UbigeoProvince;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('documents');

        $department = $user->department
            ? UbigeoDepartment::where('id', str_pad($user->department, 2, '0', STR_PAD_LEFT))->value('name')
            : null;

        $province = $user->province
            ? UbigeoProvince::where('id', str_pad($user->province, 4, '0', STR_PAD_LEFT))->value('name')
            : null;

        $district = $user->district
            ? UbigeoDistrict::where('id', str_pad($user->district, 6, '0', STR_PAD_LEFT))->value('name')
            : null;

        return view('client.dashboard', compact('user', 'department', 'province', 'district'));
    }
}
