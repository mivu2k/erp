<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Stone;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class InventoryFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_admin_can_add_item()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $user = User::factory()->create(['role_id' => $adminRole->id]);
        $stone = Stone::create(['type' => 'Granite', 'name' => 'Test Granite']);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/items', [
            'stone_id' => $stone->id,
            'width_in' => 60,
            'length_in' => 30,
            'quantity_pieces' => 5,
            'price_per_sqft' => 20,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('items', ['quantity_pieces' => 5]);
        $this->assertDatabaseHas('inventory_movements', ['type' => 'add']);
    }

    public function test_cut_operation()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $user = User::factory()->create(['role_id' => $adminRole->id]);
        $stone = Stone::create(['type' => 'Granite', 'name' => 'Test Granite']);

        $item = Item::create([
            'stone_id' => $stone->id,
            'width_in' => 100,
            'length_in' => 100,
            'quantity_pieces' => 1,
            'price_per_sqft' => 10,
            'sqft' => 69.44, // approx
            'total_sqft' => 69.44,
            'total_value' => 694.44,
            'barcode' => 'TEST-SOURCE'
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/items/{$item->id}/cut", [
            'new_items' => [
                [
                    'width_in' => 50,
                    'length_in' => 50,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(200);

        // Source item should be consumed (quantity 0 or status cut_processed)
        $item->refresh();
        $this->assertEquals(0, $item->quantity_pieces);
        $this->assertEquals('cut_processed', $item->status);

        // New items should exist
        $this->assertDatabaseHas('items', [
            'width_in' => 50,
            'length_in' => 50,
            'quantity_pieces' => 2
        ]);
    }
}
