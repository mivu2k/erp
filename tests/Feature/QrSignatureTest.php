<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Stone;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QrSignatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_qr_signature_redirects_to_item()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $adminRole = Role::where('name', 'admin')->first();
        $user = User::factory()->create(['role_id' => $adminRole->id]);

        $stone = Stone::create(['type' => 'Marble', 'name' => 'Test Stone']);
        $item = Item::create([
            'stone_id' => $stone->id,
            'width_in' => 10,
            'length_in' => 10,
            'quantity_pieces' => 1,
            'price_per_sqft' => 10,
        ]);

        // $item->qrcode_payload is automatically generated and signed

        $response = $this->actingAs($user)
            ->get('/items/lookup/' . urlencode($item->qrcode_payload));

        $response->assertStatus(302);
        $response->assertRedirect('/items/' . $item->id);
    }

    public function test_invalid_qr_signature_shows_error()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $adminRole = Role::where('name', 'admin')->first();
        $user = User::factory()->create(['role_id' => $adminRole->id]);

        $stone = Stone::create(['type' => 'Marble', 'name' => 'Test Stone']);
        $item = Item::create([
            'stone_id' => $stone->id,
            'width_in' => 10,
            'length_in' => 10,
            'quantity_pieces' => 1,
            'price_per_sqft' => 10,
        ]);

        // Forge a payload
        $data = $item->barcode . '|' . time();
        $fakeSignature = 'invalid_signature';
        $forgedPayload = base64_encode($data . '|' . $fakeSignature);

        $response = $this->actingAs($user)
            ->get('/items/lookup/' . urlencode($forgedPayload));

        $response->assertStatus(302);
        $response->assertRedirect('/scan');
        $response->assertSessionHasErrors(['error']);
    }

    public function test_plain_barcode_lookup_works()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $adminRole = Role::where('name', 'admin')->first();
        $user = User::factory()->create(['role_id' => $adminRole->id]);

        $stone = Stone::create(['type' => 'Marble', 'name' => 'Test Stone']);
        $item = Item::create([
            'stone_id' => $stone->id,
            'width_in' => 10,
            'length_in' => 10,
            'quantity_pieces' => 1,
            'price_per_sqft' => 10,
        ]);

        $response = $this->actingAs($user)
            ->get('/items/lookup/' . $item->barcode);

        $response->assertStatus(302);
        $response->assertRedirect('/items/' . $item->id);
    }
}
