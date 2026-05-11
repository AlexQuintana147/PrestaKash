<?php

namespace App\Http\Controllers;

use App\Models\UbigeoDepartment;
use App\Models\UbigeoDistrict;
use App\Models\UbigeoProvince;

class UbigeoController extends Controller
{
    public function departments()
    {
        return response()->json(
            UbigeoDepartment::orderBy('name')->get(['id', 'name'])
        );
    }

    public function provinces(string $departmentId)
    {
        // IDs are varchar with leading zero: '01', '02', etc.
        $departmentId = str_pad($departmentId, 2, '0', STR_PAD_LEFT);

        $provinces = UbigeoProvince::where('department_id', $departmentId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($provinces);
    }

    public function districts(string $provinceId)
    {
        // Province IDs are varchar(4): '0101', '0201', etc.
        $provinceId = str_pad($provinceId, 4, '0', STR_PAD_LEFT);

        $districts = UbigeoDistrict::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($districts);
    }
}

