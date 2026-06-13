<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\StockPurchaseLimit;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $items = [
            // ─── Karbohidrat ──────────────────────────────────────────
            ['sku' => 'INV-001', 'name' => 'Beras Putih',         'category' => 'Karbohidrat',    'supplier' => 'UD Sumber Beras',       'stock' => 150,  'unit' => 'kg',      'reorder_point' => 50,  'max_stock' => 500, 'unit_price' => 14000,  'daily_need' => 25],
            ['sku' => 'INV-002', 'name' => 'Mie Instan',          'category' => 'Karbohidrat',    'supplier' => 'PT Indo Food',          'stock' => 80,   'unit' => 'kg',      'reorder_point' => 30,  'max_stock' => 200, 'unit_price' => 12000,  'daily_need' => 10],
            ['sku' => 'INV-003', 'name' => 'Roti Tawar',          'category' => 'Karbohidrat',    'supplier' => 'Toko Roti Jaya',        'stock' => 20,   'unit' => 'bungkus', 'reorder_point' => 15,  'max_stock' => 100, 'unit_price' => 18000,  'daily_need' => 10],
            ['sku' => 'INV-004', 'name' => 'Jagung Pipilan',      'category' => 'Karbohidrat',    'supplier' => 'Petani Lokal Sukabumi', 'stock' => 45,   'unit' => 'kg',      'reorder_point' => 20,  'max_stock' => 150, 'unit_price' => 9000,   'daily_need' => 5],

            // ─── Protein ──────────────────────────────────────────────
            ['sku' => 'INV-005', 'name' => 'Daging Ayam',         'category' => 'Protein',        'supplier' => 'CV Ayam Segar',         'stock' => 35,   'unit' => 'kg',      'reorder_point' => 20,  'max_stock' => 100, 'unit_price' => 38000,  'daily_need' => 12],
            ['sku' => 'INV-006', 'name' => 'Tahu Putih',          'category' => 'Protein',        'supplier' => 'Pabrik Tahu Bandung',   'stock' => 8,    'unit' => 'kg',      'reorder_point' => 15,  'max_stock' => 80,  'unit_price' => 12000,  'daily_need' => 10],
            ['sku' => 'INV-007', 'name' => 'Tempe',               'category' => 'Protein',        'supplier' => 'Pengrajin Tempe Lokal', 'stock' => 12,   'unit' => 'kg',      'reorder_point' => 15,  'max_stock' => 80,  'unit_price' => 14000,  'daily_need' => 10],
            ['sku' => 'INV-008', 'name' => 'Telur Ayam',          'category' => 'Protein',        'supplier' => 'Peternakan Maju',       'stock' => 120,  'unit' => 'butir',   'reorder_point' => 100, 'max_stock' => 500, 'unit_price' => 2500,   'daily_need' => 100],
            ['sku' => 'INV-009', 'name' => 'Ikan Lele',           'category' => 'Protein',        'supplier' => 'Tambak Ikan Segar',     'stock' => 0,    'unit' => 'kg',      'reorder_point' => 10,  'max_stock' => 50,  'unit_price' => 25000,  'daily_need' => 8],

            // ─── Sayur & Buah ─────────────────────────────────────────
            ['sku' => 'INV-010', 'name' => 'Wortel',              'category' => 'Sayur & Buah',   'supplier' => 'Petani Sayur Cipanas',  'stock' => 25,   'unit' => 'kg',      'reorder_point' => 10,  'max_stock' => 80,  'unit_price' => 8000,   'daily_need' => 5],
            ['sku' => 'INV-011', 'name' => 'Bayam Segar',         'category' => 'Sayur & Buah',   'supplier' => 'Petani Sayur Cipanas',  'stock' => 5,    'unit' => 'ikat',    'reorder_point' => 10,  'max_stock' => 50,  'unit_price' => 3000,   'daily_need' => 10],
            ['sku' => 'INV-012', 'name' => 'Kentang',             'category' => 'Sayur & Buah',   'supplier' => 'Petani Lokal Dieng',   'stock' => 40,   'unit' => 'kg',      'reorder_point' => 15,  'max_stock' => 100, 'unit_price' => 10000,  'daily_need' => 6],
            ['sku' => 'INV-013', 'name' => 'Tomat',               'category' => 'Sayur & Buah',   'supplier' => 'Petani Sayur Cipanas',  'stock' => 15,   'unit' => 'kg',      'reorder_point' => 8,   'max_stock' => 50,  'unit_price' => 12000,  'daily_need' => 3],
            ['sku' => 'INV-014', 'name' => 'Pisang',              'category' => 'Sayur & Buah',   'supplier' => 'Petani Buah Lokal',    'stock' => 30,   'unit' => 'kg',      'reorder_point' => 15,  'max_stock' => 100, 'unit_price' => 7000,   'daily_need' => 8],

            // ─── Bumbu & Rempah ───────────────────────────────────────
            ['sku' => 'INV-015', 'name' => 'Bawang Merah',        'category' => 'Bumbu & Rempah', 'supplier' => 'Pasar Induk Kramat',   'stock' => 8,    'unit' => 'kg',      'reorder_point' => 5,   'max_stock' => 30,  'unit_price' => 35000,  'daily_need' => 1],
            ['sku' => 'INV-016', 'name' => 'Bawang Putih',        'category' => 'Bumbu & Rempah', 'supplier' => 'Pasar Induk Kramat',   'stock' => 6,    'unit' => 'kg',      'reorder_point' => 5,   'max_stock' => 25,  'unit_price' => 40000,  'daily_need' => 1],
            ['sku' => 'INV-017', 'name' => 'Garam Halus',         'category' => 'Bumbu & Rempah', 'supplier' => 'PT Garam Indonesia',   'stock' => 15,   'unit' => 'kg',      'reorder_point' => 5,   'max_stock' => 50,  'unit_price' => 4000,   'daily_need' => 0.5],
            ['sku' => 'INV-018', 'name' => 'Minyak Goreng',       'category' => 'Bumbu & Rempah', 'supplier' => 'PT Sinar Mas Agro',    'stock' => 20,   'unit' => 'liter',   'reorder_point' => 10,  'max_stock' => 60,  'unit_price' => 18000,  'daily_need' => 3],
            ['sku' => 'INV-019', 'name' => 'Kecap Manis',         'category' => 'Bumbu & Rempah', 'supplier' => 'PT ABC Kecap',         'stock' => 10,   'unit' => 'liter',   'reorder_point' => 5,   'max_stock' => 30,  'unit_price' => 22000,  'daily_need' => 1],

            // ─── Minuman ──────────────────────────────────────────────
            ['sku' => 'INV-020', 'name' => 'Susu UHT Full Cream', 'category' => 'Minuman',        'supplier' => 'PT Ultra Jaya',         'stock' => 50,   'unit' => 'liter',   'reorder_point' => 30,  'max_stock' => 200, 'unit_price' => 19000,  'daily_need' => 15],
            ['sku' => 'INV-021', 'name' => 'Air Mineral Galon',   'category' => 'Minuman',        'supplier' => 'CV Aqua Supplier',      'stock' => 10,   'unit' => 'dus',     'reorder_point' => 5,   'max_stock' => 30,  'unit_price' => 22000,  'daily_need' => 3],

            // ─── Lainnya ──────────────────────────────────────────────
            ['sku' => 'INV-022', 'name' => 'Gula Pasir',          'category' => 'Lainnya',        'supplier' => 'PT Gulaku',             'stock' => 18,   'unit' => 'kg',      'reorder_point' => 8,   'max_stock' => 50,  'unit_price' => 16000,  'daily_need' => 2],
            ['sku' => 'INV-023', 'name' => 'Tepung Terigu',       'category' => 'Lainnya',        'supplier' => 'PT Bogasari',           'stock' => 22,   'unit' => 'kg',      'reorder_point' => 10,  'max_stock' => 60,  'unit_price' => 12000,  'daily_need' => 3],
            ['sku' => 'INV-024', 'name' => 'Mentega',             'category' => 'Lainnya',        'supplier' => 'PT Wysman',             'stock' => 3,    'unit' => 'kg',      'reorder_point' => 3,   'max_stock' => 15,  'unit_price' => 55000,  'daily_need' => 1],
        ];

        $planningDays = config('mbg.planning_days', 7);

        foreach ($items as $data) {
            $dailyNeed   = $data['daily_need'];
            $totalNeed   = $dailyNeed * $planningDays;
            $maxPurchase = max(0, $totalNeed - $data['stock']);

            $item = InventoryItem::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'name'          => $data['name'],
                    'category'      => $data['category'],
                    'supplier'      => $data['supplier'],
                    'stock'         => $data['stock'],
                    'unit'          => $data['unit'],
                    'reorder_point' => $data['reorder_point'],
                    'max_stock'     => $data['max_stock'],
                    'unit_price'    => $data['unit_price'],
                    'currency'      => 'IDR',
                    'status'        => 'active',
                    'created_by'    => $superAdmin?->id,
                ]
            );

            // Seed purchase limits
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
        }

        $this->command->info('✅ 24 inventory items seeded with purchase limits.');
    }
}
