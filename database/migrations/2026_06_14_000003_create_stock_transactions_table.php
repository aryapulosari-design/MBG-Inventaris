<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->enum('type', ['in', 'out']); // 'in' = masuk, 'out' = keluar
            $table->decimal('quantity', 10, 3); // selalu positif
            $table->decimal('unit_price', 12, 2)->nullable(); // harga saat transaksi (untuk in)
            $table->decimal('stock_before', 10, 3); // snapshot stok sebelum
            $table->decimal('stock_after', 10, 3);  // snapshot stok sesudah
            $table->enum('reason', [
                'purchase',    // beli dari supplier (in)
                'cooking',     // dipakai memasak (out)
                'waste',       // terbuang/rusak (out)
                'adjustment',  // koreksi manual (in/out)
                'return',      // retur ke supplier (out)
                'other'        // lainnya
            ]);
            $table->string('reference_no', 50)->nullable(); // nomor surat jalan / PO
            $table->string('supplier', 150)->nullable();    // nama supplier (untuk in)
            $table->text('notes')->nullable();
            $table->timestamp('transacted_at');             // tanggal transaksi (bisa backdate)
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('inventory_item_id');
            $table->index('type');
            $table->index('transacted_at');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
