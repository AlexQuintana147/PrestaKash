<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbigeoDepartment extends Model
{
    protected $table = 'ubigeo_peru_departments';
    public $timestamps = false;

    public function provinces()
    {
        return $this->hasMany(UbigeoProvince::class, 'department_id');
    }
}
