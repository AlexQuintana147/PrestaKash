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
        // Los IDs en BD son varchar con cero adelante: '01', '02', etc.
        $departmentId = str_pad($departmentId, 2, '0', STR_PAD_LEFT);

        return response()->json(
            UbigeoProvince::where('department_id', $departmentId)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function districts(string $provinceId)
    {
        // Los IDs de provincia son varchar de 4 chars: '0101', '0201', etc.
        $provinceId = str_pad($provinceId, 4, '0', STR_PAD_LEFT);

        return response()->json(
            UbigeoDistrict::where('province_id', $provinceId)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }
}

