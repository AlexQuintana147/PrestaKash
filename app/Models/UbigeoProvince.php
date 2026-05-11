<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbigeoProvince extends Model
{
    protected $table = 'ubigeo_peru_provinces';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    public function districts()
    {
        return $this->hasMany(UbigeoDistrict::class, 'province_id');
    }
}
