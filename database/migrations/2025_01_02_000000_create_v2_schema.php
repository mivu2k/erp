<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
        });

        // 2. Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // 3. Batches (Blocks/Bundles)
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stone_id')->constrained()->cascadeOnDelete(); // The material
            $table->string('block_number'); // Supplier's block #
            $table->string('bundle_number')->nullable();
            $table->date('arrival_date');
            $table->decimal('cost_price', 12, 2)->nullable(); // Total cost of block
            $table->timestamps();
        });

        // 4. Update Items (Slabs) to link to Batch
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('slab_number')->nullable(); // Sequence in block
            $table->boolean('is_remnant')->default(false);
        });

        // 5. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(); // Salesperson
            $table->string('order_number')->unique();
            $table->string('status')->default('quote'); // quote, confirmed, production, completed, cancelled
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->date('delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Order Items (Allocation)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained(); // The specific slab
            $table->decimal('price', 10, 2); // Sold price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn(['batch_id', 'slab_number', 'is_remnant']);
        });
        Schema::dropIfExists('batches');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('suppliers');
    }
};
