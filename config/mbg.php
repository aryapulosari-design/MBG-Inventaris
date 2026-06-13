<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MBG Application Configuration
    |--------------------------------------------------------------------------
    */

    // Horizon perencanaan pembelian bahan baku (dalam hari)
    'planning_days' => env('MBG_PLANNING_DAYS', 7),

    // Email penerima alert stok rendah
    'low_stock_email' => env('MBG_LOW_STOCK_EMAIL', 'admin@mbg.id'),

    // Kategori bahan baku yang tersedia
    'categories' => [
        'Sayur & Buah',
        'Protein',
        'Karbohidrat',
        'Bumbu & Rempah',
        'Minuman',
        'Peralatan Dapur',
        'Perlengkapan Makan',
        'Lainnya',
    ],

    // Satuan yang tersedia
    'units' => [
        'kg', 'gram', 'liter', 'ml', 'butir', 'ikat', 'bungkus', 'dus', 'pcs',
    ],

    // Role yang tersedia
    'roles' => [
        'super_admin'   => 'Admin',
        'viewer'        => 'Viewer',
    ],

    // Warna per kategori (untuk chart)
    'category_colors' => [
        'Sayur & Buah'       => '#28a745',
        'Protein'            => '#dc3545',
        'Karbohidrat'        => '#ffc107',
        'Bumbu & Rempah'     => '#fd7e14',
        'Minuman'            => '#17a2b8',
        'Peralatan Dapur'    => '#6f42c1', // Ungu
        'Perlengkapan Makan' => '#e83e8c', // Pink
        'Lainnya'            => '#6c757d',
    ],
];
