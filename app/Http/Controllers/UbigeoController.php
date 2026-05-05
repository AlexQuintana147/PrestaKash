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
        return response()->json(
            UbigeoProvince::where('department_id', $departmentId)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function districts(string $provinceId)
    {
        return response()->json(
            UbigeoDistrict::where('province_id', $provinceId)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }
}

