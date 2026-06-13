<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 20)->unique();
            $table->string('name', 150);
            $table->string('category', 80); // 'Sayur & Buah', 'Protein', dll
            $table->string('supplier', 150);
            $table->decimal('stock', 10, 3)->default(0); // desimal untuk kg/liter
            $table->string('unit', 20)->default('kg'); // satuan: kg, liter, butir, dll
            $table->decimal('reorder_point', 10, 3)->default(0);
            $table->decimal('max_stock', 10, 3)->nullable(); // batas maksimum stok
            $table->decimal('unit_price', 12, 2);
            $table->char('currency', 3)->default('IDR');
            $table->enum('status', ['active', 'backordered', 'discontinued'])->default('active');
            $table->timestamp('last_restocked')->nullable();
            $table->timestamp('low_stock_alerted_at')->nullable(); // untuk cegah duplikasi alert
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('category');
            $table->index('status');
            $table->index(['stock', 'reorder_point']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
