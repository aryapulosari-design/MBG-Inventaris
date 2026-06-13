<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_purchase_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->unique()->constrained('inventory_items')->cascadeOnDelete();
            $table->decimal('daily_need', 10, 3)->default(0);       // kebutuhan per hari (kg)
            $table->unsignedTinyInteger('planning_days')->default(7); // horizon perencanaan
            $table->decimal('calculated_need', 10, 3)->default(0);   // total kebutuhan
            $table->decimal('max_purchase', 10, 3)->default(0);      // batas beli = calculated_need - stok
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_purchase_limits');
    }
};
