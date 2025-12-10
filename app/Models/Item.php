<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    protected $fillable = [
        'stone_id',
        'batch_id',
        'slab_number',
        'is_remnant',
        'width_in',
        'length_in',
        'thickness_mm',
        'sqft',
        'quantity_pieces',
        'total_sqft',
        'price_per_sqft',
        'total_value',
        'location',
        'barcode',
        'qrcode_payload',
        'status',
        'parent_item_id',
        'work_order_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->calculateMetrics();
            if (empty($item->barcode)) {
                $item->barcode = 'ITM-' . strtoupper(Str::random(8));
            }
            if (empty($item->qrcode_payload)) {
                $item->generateQrPayload();
            }
        });

        static::updating(function ($item) {
            $item->calculateMetrics();
        });
    }

    public function calculateMetrics()
    {
        // 1. Compute SQFT per piece: (width * length) / 144
        $this->sqft = ($this->width_in * $this->length_in) / 144;

        // 2. Compute Total SQFT: sqft * quantity
        $this->total_sqft = $this->sqft * $this->quantity_pieces;

        // 3. Compute Total Value: total_sqft * price_per_sqft
        $this->total_value = $this->total_sqft * $this->price_per_sqft;
    }

    public function generateQrPayload()
    {
        // Simple payload: ID + Random + HMAC
        // In a real app, this might be a URL or a structured JSON
        $data = $this->barcode . '|' . time();
        $signature = hash_hmac('sha256', $data, config('app.key'));
        $this->qrcode_payload = base64_encode($data . '|' . $signature);
    }

    public function stone()
    {
        return $this->belongsTo(Stone::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function parent()
    {
        return $this->belongsTo(Item::class, 'parent_item_id');
    }

    public function children()
    {
        return $this->hasMany(Item::class, 'parent_item_id');
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
