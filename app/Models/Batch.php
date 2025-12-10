<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'supplier_id',
        'stone_id',
        'block_number',
        'bundle_number',
        'arrival_date',
        'cost_price',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stone()
    {
        return $this->belongsTo(Stone::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
