<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Stone;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sqft_calculation()
    {
        $stone = Stone::create(['type' => 'Marble', 'name' => 'Test Stone']);

        $item = new Item([
            'stone_id' => $stone->id,
            'width_in' => 120,
            'length_in' => 72,
            'quantity_pieces' => 2,
            'price_per_sqft' => 50,
        ]);

        $item->save();

        // 120 * 72 = 8640 sq in
        // 8640 / 144 = 60 sq ft per piece
        // Total sqft = 60 * 2 = 120
        // Total value = 120 * 50 = 6000

        $this->assertEquals(60, $item->sqft);
        $this->assertEquals(120, $item->total_sqft);
        $this->assertEquals(6000, $item->total_value);
    }

    public function test_barcode_generation()
    {
        $stone = Stone::create(['type' => 'Marble', 'name' => 'Test Stone']);
        $item = Item::create([
            'stone_id' => $stone->id,
            'width_in' => 10,
            'length_in' => 10,
            'quantity_pieces' => 1,
            'price_per_sqft' => 10,
        ]);

        $this->assertNotEmpty($item->barcode);
        $this->assertStringStartsWith('ITM-', $item->barcode);
    }

    public function test_qr_payload_signing()
    {
        $stone = Stone::create(['type' => 'Marble', 'name' => 'Test Stone']);
        $item = Item::create([
            'stone_id' => $stone->id,
            'width_in' => 10,
            'length_in' => 10,
            'quantity_pieces' => 1,
            'price_per_sqft' => 10,
        ]);

        $this->assertNotEmpty($item->qrcode_payload);
        $decoded = base64_decode($item->qrcode_payload);
        $parts = explode('|', $decoded);

        $this->assertCount(3, $parts); // Barcode | Timestamp | Signature
        $this->assertEquals($item->barcode, $parts[0]);
    }
}
