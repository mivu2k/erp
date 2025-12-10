<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(); // Who performed the action

            $table->string('type'); // add, update, reserve, sell, cut, return
            $table->integer('quantity_change'); // + or -
            $table->decimal('sqft_change', 12, 4);

            $table->text('description')->nullable(); // e.g., "Cut into 2 pieces"
            $table->json('metadata')->nullable(); // Store related item IDs if split/cut

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
