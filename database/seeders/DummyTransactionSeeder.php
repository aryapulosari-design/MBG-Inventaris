<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\User;
use App\Services\InventarisService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DummyTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $service = new InventarisService();
        $admin = User::where('role', 'super_admin')->first(); // mapped as Admin
        if (!$admin) {
            $admin = User::first();
        }
        
        Auth::login($admin);

        // Cari beberapa item
        $beras = InventoryItem::where('name', 'like', '%Beras%')->first();
        $telur = InventoryItem::where('name', 'like', '%Telur%')->first();
        $ayam = InventoryItem::where('name', 'like', '%Ayam%')->first();
        $gas = InventoryItem::where('name', 'like', '%Gas%')->first();

        $transactions = [];

        if ($beras) {
            $transactions[] = [
                'item' => $beras,
                'method' => 'prosesStokMasuk',
                'data' => [
                    'quantity' => 100,
                    'reason' => 'purchase',
                    'supplier' => 'PT Maju Tani',
                    'notes' => 'Restock mingguan beras premium.',
                    'transacted_at' => now()->subDays(2),
                ]
            ];
            $transactions[] = [
                'item' => $beras,
                'method' => 'prosesStokKeluar',
                'data' => [
                    'quantity' => 25,
                    'reason' => 'cooking',
                    'notes' => 'Dimasak untuk jatah sekolah SD N 1.',
                    'transacted_at' => now()->subDays(1),
                ]
            ];
        }

        if ($telur) {
            $transactions[] = [
                'item' => $telur,
                'method' => 'prosesStokKeluar',
                'data' => [
                    'quantity' => 10,
                    'reason' => 'cooking',
                    'notes' => 'Rebus telur massal.',
                    'transacted_at' => now()->subHours(10),
                ]
            ];
            $transactions[] = [
                'item' => $telur,
                'method' => 'prosesStokKeluar',
                'data' => [
                    'quantity' => 1,
                    'reason' => 'waste',
                    'notes' => 'Telur pecah di gudang.',
                    'transacted_at' => now()->subHours(5),
                ]
            ];
        }

        if ($ayam) {
            $transactions[] = [
                'item' => $ayam,
                'method' => 'prosesStokMasuk',
                'data' => [
                    'quantity' => 50,
                    'reason' => 'purchase',
                    'supplier' => 'Pemasok Ayam Pak Junaidi',
                    'notes' => 'Pengiriman ayam potong segar.',
                    'transacted_at' => now()->subHours(2),
                ]
            ];
        }

        if ($gas) {
            $transactions[] = [
                'item' => $gas,
                'method' => 'prosesStokKeluar',
                'data' => [
                    'quantity' => 2,
                    'reason' => 'cooking',
                    'notes' => 'Penggantian tabung gas dapur utama.',
                    'transacted_at' => now()->subMinutes(30),
                ]
            ];
        }

        foreach ($transactions as $t) {
            try {
                if ($t['method'] === 'prosesStokMasuk') {
                    $service->prosesStokMasuk($t['item'], $t['data']);
                } else {
                    $service->prosesStokKeluar($t['item'], $t['data']);
                }
            } catch (\Exception $e) {
                // Ignore if constraints fail during seeding
            }
        }
    }
}
