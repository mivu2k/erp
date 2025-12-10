<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stone extends Model
{
    protected $fillable = [
        'type',
        'name',
        'size',
        'color',
        'color_finish',
        'description',
        'barcode',
        'qrcode_payload',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stone) {
            if (empty($stone->barcode)) {
                $stone->barcode = 'STN-' . strtoupper(\Illuminate\Support\Str::random(8));
            }
            if (empty($stone->qrcode_payload)) {
                $stone->generateQrPayload();
            }
        });
    }

    public function generateQrPayload()
    {
        // Static QR Code (Just the barcode)
        $this->qrcode_payload = $this->barcode;
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function getAveragePriceAttribute()
    {
        // Calculate weighted average purchase price
        // Total Cost of all Batches / Total SqFt of all Items in those Batches

        $totalCost = $this->batches()->sum('cost_price');

        // Only count items that belong to a batch (purchased items)
        // This avoids skewing the average with manually added "free" items if any
        $totalSqft = $this->items()->whereNotNull('batch_id')->sum('total_sqft');

        if ($totalSqft > 0) {
            return $totalCost / $totalSqft;
        }

        return 0;
    }
}
