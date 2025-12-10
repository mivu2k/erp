<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact_person', 'email', 'phone', 'country'];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
}
