<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\InventoryItem;
use App\Models\NotificationMbg;
use App\Models\StockPurchaseLimit;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InventarisService
{
    // ─── Stok Masuk ──────────────────────────────────────────────────

    /**
     * Process stok masuk dengan validasi lengkap sesuai PRD 7.1
     */
    public function prosesStokMasuk(InventoryItem $item, array $data): StockTransaction
    {
        // BR-03: Item discontinued tidak bisa ditransaksikan
        if ($item->status === 'discontinued') {
            throw new \RuntimeException("Item '{$item->name}' sudah tidak aktif (discontinued).");
        }

        // BR-02: Validasi batas pembelian (khusus reason=purchase)
        if ($data['reason'] === 'purchase') {
            $maxPurchase = $this->hitungMaxPembelian($item);
            if ($maxPurchase > 0 && $data['quantity'] > $maxPurchase) {
                throw new \RuntimeException(
                    "Jumlah beli ({$data['quantity']} {$item->unit}) melebihi batas kebutuhan " .
                    "({$maxPurchase} {$item->unit}). Stok saat ini sudah cukup untuk kebutuhan mendatang."
                );
            }
        }

        // Hitung stok baru
        $newStock = (float) $item->stock + (float) $data['quantity'];

        // BR-08: Cek max_stock jika diset
        if ($item->max_stock && $newStock > $item->max_stock) {
            throw new \RuntimeException(
                "Stok akan melebihi batas maksimum ({$item->max_stock} {$item->unit}). " .
                "Stok setelah transaksi: {$newStock} {$item->unit}."
            );
        }

        return DB::transaction(function () use ($item, $data, $newStock) {
            // Buat record transaksi
            $tx = StockTransaction::create([
                'inventory_item_id' => $item->id,
                'type'              => 'in',
                'quantity'          => $data['quantity'],
                'unit_price'        => $data['unit_price'] ?? null,
                'stock_before'      => $item->stock,
                'stock_after'       => $newStock,
                'reason'            => $data['reason'],
                'reference_no'      => $data['reference_no'] ?? null,
                'supplier'          => $data['supplier'] ?? null,
                'notes'             => $data['notes'] ?? null,
                'transacted_at'     => $data['transacted_at'],
                'created_by'        => auth()->id(),
                'created_at'        => now(),
            ]);

            // Update stok item
            $item->update([
                'stock'          => $newStock,
                'last_restocked' => $data['transacted_at'],
                'unit_price'     => $data['unit_price'] ?? $item->unit_price,
            ]);

            // Catat ke audit log
            AuditLog::catat(
                $item,
                'stock_in',
                ['stock' => (float) $item->getOriginal('stock')],
                ['stock' => $newStock, 'quantity_added' => $data['quantity'], 'reason' => $data['reason']]
            );

            // Invalidate purchase limit cache
            Cache::forget("purchase_limit_{$item->id}");

            return $tx;
        });
    }

    // ─── Stok Keluar ─────────────────────────────────────────────────

    /**
     * Process stok keluar dengan validasi lengkap sesuai PRD 7.2
     */
    public function prosesStokKeluar(InventoryItem $item, array $data): StockTransaction
    {
        // BR-03: Item discontinued tidak bisa ditransaksikan
        if ($item->status === 'discontinued') {
            throw new \RuntimeException("Item '{$item->name}' sudah tidak aktif (discontinued).");
        }

        // BR-01: Stok tidak boleh negatif
        if ($data['quantity'] > $item->stock) {
            throw new \RuntimeException(
                "Stok tidak mencukupi. Stok saat ini: {$item->stock} {$item->unit}, " .
                "diminta: {$data['quantity']} {$item->unit}."
            );
        }

        $newStock = (float) $item->stock - (float) $data['quantity'];

        return DB::transaction(function () use ($item, $data, $newStock) {
            // Buat record transaksi
            $tx = StockTransaction::create([
                'inventory_item_id' => $item->id,
                'type'              => 'out',
                'quantity'          => $data['quantity'],
                'unit_price'        => null,
                'stock_before'      => $item->stock,
                'stock_after'       => $newStock,
                'reason'            => $data['reason'],
                'reference_no'      => null,
                'supplier'          => null,
                'notes'             => $data['notes'] ?? null,
                'transacted_at'     => $data['transacted_at'],
                'created_by'        => auth()->id(),
                'created_at'        => now(),
            ]);

            // Update stok item
            $item->update(['stock' => $newStock]);

            // Catat ke audit log
            AuditLog::catat(
                $item,
                'stock_out',
                ['stock' => (float) $item->getOriginal('stock')],
                ['stock' => $newStock, 'quantity_out' => $data['quantity'], 'reason' => $data['reason']]
            );

            // BR-10: Cek low stock → trigger alert jika perlu
            if ($newStock < $item->reorder_point) {
                $this->triggerLowStockAlert($item, $newStock);
            }

            // Invalidate purchase limit cache
            Cache::forget("purchase_limit_{$item->id}");

            return $tx;
        });
    }

    // ─── Kalkulasi Kebutuhan ─────────────────────────────────────────

    /**
     * Hitung batas pembelian maksimum berdasarkan resep aktif (PRD 7.4)
     */
    public function hitungMaxPembelian(InventoryItem $item): float
    {
        $cacheKey = "purchase_limit_{$item->id}";

        return Cache::remember($cacheKey, 3600, function () use ($item) {
            $planningDays = config('mbg.planning_days', 7);

            // Query kebutuhan dari recipe_ingredients
            $dailyNeed = DB::table('recipe_ingredients')
                ->join('recipe_items', 'recipe_items.id', '=', 'recipe_ingredients.recipe_id')
                ->where('recipe_ingredients.inventory_item_id', $item->id)
                ->where('recipe_items.is_active', true)
                ->selectRaw('SUM(recipe_ingredients.quantity_per_serving / 1000 * recipe_items.target_portions) as total')
                ->value('total') ?? 0;

            $totalNeed   = (float) $dailyNeed * $planningDays;
            $maxPurchase = max(0, $totalNeed - (float) $item->stock);

            // Simpan ke tabel untuk transparansi
            StockPurchaseLimit::updateOrCreate(
                ['inventory_item_id' => $item->id],
                [
                    'daily_need'         => $dailyNeed,
                    'planning_days'      => $planningDays,
                    'calculated_need'    => $totalNeed,
                    'max_purchase'       => $maxPurchase,
                    'last_calculated_at' => now(),
                    'updated_at'         => now(),
                ]
            );

            return $maxPurchase;
        });
    }

    // ─── Rekapitulasi Stok ───────────────────────────────────────────

    /**
     * Hitung summary cards dan rekap per kategori (PRD 7.3)
     */
    public function rekapStok(): array
    {
        $items = InventoryItem::where('status', 'active')->get();

        $perKategori = $items->groupBy('category')->map(fn($group) => [
            'total_stok'  => (float) $group->sum('stock'),
            'total_nilai' => (float) $group->sum(fn($i) => $i->stock * $i->unit_price),
            'jumlah_item' => $group->count(),
        ])->sortByDesc('total_stok');

        $totalStokSemua = (float) $items->sum('stock');

        return [
            'total_sku_aktif'  => $items->count(),
            'perlu_reorder'    => $items->filter(fn($i) => $i->stock < $i->reorder_point)->count(),
            'total_nilai_stok' => (float) $items->sum(fn($i) => $i->stock * $i->unit_price),
            'item_stok_habis'  => $items->where('stock', 0)->count(),
            'per_kategori'     => $perKategori,
            'total_stok_semua' => $totalStokSemua,
        ];
    }

    // ─── Low Stock Alert ─────────────────────────────────────────────

    /**
     * Trigger low stock alert dengan throttle 24 jam per item (BR-10)
     */
    public function triggerLowStockAlert(InventoryItem $item, float $currentStock): void
    {
        // Cek apakah sudah di-alert dalam 24 jam terakhir
        if ($item->low_stock_alerted_at && $item->low_stock_alerted_at->diffInHours(now()) < 24) {
            return;
        }

        // Kirim notifikasi ke semua admin
        $admins = User::whereIn('role', ['super_admin', 'admin_program', 'admin_dapur'])->get();

        foreach ($admins as $admin) {
            NotificationMbg::create([
                'user_id'    => $admin->id,
                'type'       => 'low_stock',
                'title'      => "⚠️ Stok Rendah: {$item->name}",
                'message'    => "Stok {$item->name} ({$item->sku}) saat ini {$currentStock} {$item->unit}, " .
                                "di bawah minimum {$item->reorder_point} {$item->unit}. " .
                                "Segera lakukan pemesanan ke supplier: {$item->supplier}.",
                'data'       => [
                    'item_id'       => $item->id,
                    'item_name'     => $item->name,
                    'sku'           => $item->sku,
                    'current_stock' => $currentStock,
                    'reorder_point' => $item->reorder_point,
                    'unit'          => $item->unit,
                    'supplier'      => $item->supplier,
                ],
                'created_at' => now(),
            ]);
        }

        // Update timestamp alert
        $item->updateQuietly(['low_stock_alerted_at' => now()]);
    }

    // ─── Export ──────────────────────────────────────────────────────

    /**
     * Generate CSV data untuk export inventaris
     */
    public function exportInventarisCsv(): array
    {
        $headers = [
            'SKU', 'Nama Bahan', 'Kategori', 'Supplier', 'Stok', 'Satuan',
            'Reorder Point', 'Harga Per Unit', 'Nilai Stok', 'Status',
            'Terakhir Restock', 'Tanggal Export',
        ];

        $rows = InventoryItem::with('purchaseLimit')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn($item) => [
                $item->sku,
                $item->name,
                $item->category,
                $item->supplier,
                number_format($item->stock, 3, '.', ''),
                $item->unit,
                number_format($item->reorder_point, 3, '.', ''),
                number_format($item->unit_price, 2, '.', ''),
                number_format($item->nilai_stok, 2, '.', ''),
                $item->status,
                $item->last_restocked?->format('Y-m-d') ?? '-',
                now()->format('Y-m-d H:i:s'),
            ])->toArray();

        return ['headers' => $headers, 'rows' => $rows];
    }

    /**
     * Generate CSV data untuk export transaksi
     */
    public function exportTransaksiCsv(array $filters = []): array
    {
        $headers = [
            'Tanggal', 'SKU', 'Nama Item', 'Jenis', 'Jumlah', 'Satuan',
            'Harga/unit', 'Total Nilai', 'Alasan', 'Supplier', 'No. Referensi',
            'Dicatat Oleh', 'Catatan',
        ];

        $query = StockTransaction::with(['item', 'creator'])
            ->orderBy('transacted_at', 'desc');

        if (!empty($filters['from']))      $query->whereDate('transacted_at', '>=', $filters['from']);
        if (!empty($filters['to']))        $query->whereDate('transacted_at', '<=', $filters['to']);
        if (!empty($filters['item_id']))   $query->where('inventory_item_id', $filters['item_id']);
        if (!empty($filters['type']))      $query->where('type', $filters['type']);

        $rows = $query->get()->map(fn($tx) => [
            $tx->transacted_at->format('Y-m-d'),
            $tx->item->sku ?? '-',
            $tx->item->name ?? '-',
            $tx->type === 'in' ? 'Masuk' : 'Keluar',
            number_format($tx->quantity, 3, '.', ''),
            $tx->item->unit ?? '-',
            number_format($tx->unit_price ?? 0, 2, '.', ''),
            number_format($tx->total_nilai, 2, '.', ''),
            $tx->reason_label,
            $tx->supplier ?? '-',
            $tx->reference_no ?? '-',
            $tx->creator->name ?? '-',
            $tx->notes ?? '',
        ])->toArray();

        return ['headers' => $headers, 'rows' => $rows];
    }
}
