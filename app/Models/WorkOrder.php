<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable = ['status', 'description', 'source_item_id'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function sourceItem()
    {
        return $this->belongsTo(Item::class, 'source_item_id');
    }

    // Helper to get source items (parents of the items created in this order)
    // Kept for backward compatibility or if needed, but sourceItem() is preferred now.
    public function sourceItems()
    {
        return $this->items()->with('parent')->get()->pluck('parent')->unique();
    }
}
