<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Sample recipes untuk demo kalkulasi kebutuhan
        DB::table('recipe_items')->insert([
            ['name' => 'Nasi Ayam Sayur', 'description' => 'Menu utama harian', 'target_portions' => 300, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bubur Sayuran', 'description' => 'Menu sarapan', 'target_portions' => 200, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mie Goreng Telur', 'description' => 'Menu alternatif', 'target_portions' => 150, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $recipe1 = DB::table('recipe_items')->where('name', 'Nasi Ayam Sayur')->value('id');
        $recipe2 = DB::table('recipe_items')->where('name', 'Bubur Sayuran')->value('id');
        $recipe3 = DB::table('recipe_items')->where('name', 'Mie Goreng Telur')->value('id');

        $berasId  = DB::table('inventory_items')->where('sku', 'INV-001')->value('id');
        $ayamId   = DB::table('inventory_items')->where('sku', 'INV-005')->value('id');
        $wortelId = DB::table('inventory_items')->where('sku', 'INV-010')->value('id');
        $mieId    = DB::table('inventory_items')->where('sku', 'INV-002')->value('id');
        $telurId  = DB::table('inventory_items')->where('sku', 'INV-008')->value('id');
        $bayamId  = DB::table('inventory_items')->where('sku', 'INV-011')->value('id');

        $ingredients = [];

        // Nasi Ayam Sayur: 150g beras, 80g ayam, 50g wortel per porsi
        if ($recipe1 && $berasId)  $ingredients[] = ['recipe_id' => $recipe1, 'inventory_item_id' => $berasId,  'quantity_per_serving' => 150, 'unit' => 'gram', 'created_at' => now(), 'updated_at' => now()];
        if ($recipe1 && $ayamId)   $ingredients[] = ['recipe_id' => $recipe1, 'inventory_item_id' => $ayamId,  'quantity_per_serving' => 80,  'unit' => 'gram', 'created_at' => now(), 'updated_at' => now()];
        if ($recipe1 && $wortelId) $ingredients[] = ['recipe_id' => $recipe1, 'inventory_item_id' => $wortelId,'quantity_per_serving' => 50,  'unit' => 'gram', 'created_at' => now(), 'updated_at' => now()];

        // Bubur Sayuran: 100g beras, 60g bayam per porsi
        if ($recipe2 && $berasId)  $ingredients[] = ['recipe_id' => $recipe2, 'inventory_item_id' => $berasId,  'quantity_per_serving' => 100, 'unit' => 'gram', 'created_at' => now(), 'updated_at' => now()];
        if ($recipe2 && $bayamId)  $ingredients[] = ['recipe_id' => $recipe2, 'inventory_item_id' => $bayamId,  'quantity_per_serving' => 60,  'unit' => 'gram', 'created_at' => now(), 'updated_at' => now()];

        // Mie Goreng Telur: 80g mie, 1 telur per porsi
        if ($recipe3 && $mieId)    $ingredients[] = ['recipe_id' => $recipe3, 'inventory_item_id' => $mieId,  'quantity_per_serving' => 80,  'unit' => 'gram', 'created_at' => now(), 'updated_at' => now()];
        if ($recipe3 && $telurId)  $ingredients[] = ['recipe_id' => $recipe3, 'inventory_item_id' => $telurId,'quantity_per_serving' => 1,   'unit' => 'butir', 'created_at' => now(), 'updated_at' => now()];

        if (!empty($ingredients)) {
            DB::table('recipe_ingredients')->insert($ingredients);
        }

        $this->command->info('✅ 3 sample recipes seeded with ingredients.');
    }
}
