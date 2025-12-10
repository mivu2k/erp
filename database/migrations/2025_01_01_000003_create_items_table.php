<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stone_id')->constrained()->cascadeOnDelete();

            // Dimensions
            $table->decimal('width_in', 8, 2);
            $table->decimal('length_in', 8, 2);
            $table->decimal('thickness_mm', 8, 2)->nullable();

            // Calculated
            $table->decimal('sqft', 10, 4); // Per piece

            // Quantity & Value
            $table->integer('quantity_pieces')->default(1);
            $table->decimal('total_sqft', 12, 4); // quantity * sqft
            $table->decimal('price_per_sqft', 10, 2);
            $table->decimal('total_value', 12, 2);

            // Tracking
            $table->string('location')->nullable();
            $table->string('barcode')->unique(); // Code128 value
            $table->string('qrcode_payload')->nullable(); // Signed payload

            $table->string('status')->default('available'); // available, reserved, sold

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
