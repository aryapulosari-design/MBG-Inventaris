<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class PeralatanSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Perlengkapan Makan (Untuk Siswa)
            [
                'sku' => 'PMK-001',
                'name' => 'Ompreng / Kotak Makan Stainless Steel (5 Sekat)',
                'category' => 'Perlengkapan Makan',
                'unit' => 'pcs',
                'stock' => 500,
                'reorder_point' => 50,
                'max_stock' => 1000,
                'unit_price' => 35000,
                'supplier' => 'PT Makmur Logistik Perkakas',
                'notes' => 'Kotak makan anti karat dengan tutup rapat untuk distribusi makanan.',
            ],
            [
                'sku' => 'PMK-002',
                'name' => 'Set Sendok & Garpu Stainless',
                'category' => 'Perlengkapan Makan',
                'unit' => 'pasang',
                'stock' => 600,
                'reorder_point' => 100,
                'max_stock' => 1200,
                'unit_price' => 5000,
                'supplier' => 'PT Makmur Logistik Perkakas',
                'notes' => 'Alat makan untuk siswa.',
            ],
            [
                'sku' => 'PMK-003',
                'name' => 'Gelas Tumbler Plastik BPA Free (300ml)',
                'category' => 'Perlengkapan Makan',
                'unit' => 'pcs',
                'stock' => 500,
                'reorder_point' => 50,
                'max_stock' => 1000,
                'unit_price' => 8000,
                'supplier' => 'Toko Alat Tulis & Plastik Maju',
                'notes' => 'Untuk jatah air minum/susu anak.',
            ],

            // Peralatan Dapur (Untuk Memasak)
            [
                'sku' => 'PDR-001',
                'name' => 'Dandang Nasi Besar (Kapasitas 20 Liter)',
                'category' => 'Peralatan Dapur',
                'unit' => 'pcs',
                'stock' => 4,
                'reorder_point' => 1,
                'max_stock' => 6,
                'unit_price' => 450000,
                'supplier' => 'Toko Perlengkapan Dapur Sentosa',
                'notes' => 'Digunakan untuk menanak nasi porsi besar.',
            ],
            [
                'sku' => 'PDR-002',
                'name' => 'Wajan Penggorengan Besar (Diameter 80cm)',
                'category' => 'Peralatan Dapur',
                'unit' => 'pcs',
                'stock' => 3,
                'reorder_point' => 1,
                'max_stock' => 5,
                'unit_price' => 320000,
                'supplier' => 'Toko Perlengkapan Dapur Sentosa',
                'notes' => 'Untuk menumis/menggoreng lauk massal.',
            ],
            [
                'sku' => 'PDR-003',
                'name' => 'Spatula Kayu Super Besar (Sutil Aduk)',
                'category' => 'Peralatan Dapur',
                'unit' => 'pcs',
                'stock' => 6,
                'reorder_point' => 2,
                'max_stock' => 10,
                'unit_price' => 35000,
                'supplier' => 'Toko Perlengkapan Dapur Sentosa',
                'notes' => 'Kayu mahoni untuk mengaduk kuali masakan.',
            ],
            [
                'sku' => 'PDR-004',
                'name' => 'Panci Sup Kuah (Kapasitas 30 Liter)',
                'category' => 'Peralatan Dapur',
                'unit' => 'pcs',
                'stock' => 3,
                'reorder_point' => 1,
                'max_stock' => 5,
                'unit_price' => 280000,
                'supplier' => 'Toko Perlengkapan Dapur Sentosa',
                'notes' => 'Untuk sayur sop/lodeh dll.',
            ],
            [
                'sku' => 'PDR-005',
                'name' => 'Tabung Gas Elpiji 12 Kg (Isi Ulang)',
                'category' => 'Peralatan Dapur',
                'unit' => 'tabung',
                'stock' => 8,
                'reorder_point' => 3,
                'max_stock' => 12,
                'unit_price' => 220000,
                'supplier' => 'Agen LPG Pak Kumis',
                'notes' => 'Stok gas untuk operasional kompor besar.',
            ],

            // Perlengkapan Tambahan & Kebersihan (Lainnya)
            [
                'sku' => 'CLN-001',
                'name' => 'Sabun Cuci Piring Jerigen (5 Liter)',
                'category' => 'Lainnya',
                'unit' => 'jerigen',
                'stock' => 5,
                'reorder_point' => 2,
                'max_stock' => 10,
                'unit_price' => 45000,
                'supplier' => 'Toko Sabun Bersih Kilau',
                'notes' => 'Pembersih utama untuk mencuci ratusan ompreng.',
            ],
            [
                'sku' => 'CLN-002',
                'name' => 'Spons Cuci Piring Sabut Kasar',
                'category' => 'Lainnya',
                'unit' => 'pcs',
                'stock' => 20,
                'reorder_point' => 5,
                'max_stock' => 40,
                'unit_price' => 4000,
                'supplier' => 'Toko Sabun Bersih Kilau',
                'notes' => 'Barang habis pakai bulanan.',
            ],
            [
                'sku' => 'CLN-003',
                'name' => 'Celemek (Apron) Dapur PVC Anti Air',
                'category' => 'Lainnya',
                'unit' => 'pcs',
                'stock' => 10,
                'reorder_point' => 2,
                'max_stock' => 15,
                'unit_price' => 25000,
                'supplier' => 'Toko Seragam Konveksi',
                'notes' => 'Untuk petugas cuci piring dan koki.',
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::updateOrCreate(
                ['sku' => $item['sku']],
                $item
            );
        }
    }
}
