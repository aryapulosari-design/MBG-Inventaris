<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('loggable_type', 100);   // 'InventoryItem', 'StockTransaction'
            $table->unsignedBigInteger('loggable_id');
            $table->string('action', 50);            // 'created', 'updated', 'stock_in', 'stock_out', 'deleted'
            $table->json('old_values')->nullable();   // data sebelum perubahan
            $table->json('new_values')->nullable();   // data setelah perubahan
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name', 100)->nullable(); // disimpan langsung (anti orphan)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['loggable_type', 'loggable_id']);
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
