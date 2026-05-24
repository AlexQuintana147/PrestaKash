<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractClause extends Model
{
    protected $fillable = ['contract_id', 'box_type', 'title', 'content', 'order'];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
