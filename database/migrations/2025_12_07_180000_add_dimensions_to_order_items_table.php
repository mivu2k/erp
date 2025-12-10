<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('length_in', 10, 2)->nullable()->after('item_id');
            $table->decimal('width_in', 10, 2)->nullable()->after('length_in');
            $table->decimal('thickness_mm', 10, 2)->nullable()->after('width_in');
            $table->decimal('sqft', 10, 2)->nullable()->after('thickness_mm');
            $table->decimal('wastage', 10, 2)->nullable()->after('sqft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['length_in', 'width_in', 'thickness_mm', 'sqft', 'wastage']);
        });
    }
};
