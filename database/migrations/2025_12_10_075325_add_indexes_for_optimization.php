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
        Schema::table('items', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->index('arrival_date');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropIndex(['arrival_date']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
