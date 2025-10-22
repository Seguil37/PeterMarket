<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            ['Arroz Costeño 1kg',      6.50, 120, 'https://picsum.photos/seed/arroz/300/300'],
            ['Azúcar Blanca 1kg',      4.90, 90,  'https://picsum.photos/seed/azucar/300/300'],
            ['Aceite Vegetal 1L',      10.90,60,  'https://picsum.photos/seed/aceite/300/300'],
            ['Leche Entera 1L',        4.20, 150, 'https://picsum.photos/seed/leche/300/300'],
            ['Fideos Spaghetti 500g',  3.70, 80,  'https://picsum.photos/seed/fideos/300/300'],
            ['Atún en agua 170g',      5.50, 100, 'https://picsum.photos/seed/atun/300/300'],
            ['Pan de molde 680g',      7.40, 70,  'https://picsum.photos/seed/pan/300/300'],
            ['Yogurt fresa 1L',        8.30, 50,  'https://picsum.photos/seed/yogurt/300/300'],
            ['Jugo naranja 1L',        6.20, 65,  'https://picsum.photos/seed/jugo/300/300'],
            ['Detergente 1kg',         12.90,55,  'https://picsum.photos/seed/detergente/300/300'],
            ['Papel higiénico x12',    18.50,40,  'https://picsum.photos/seed/papel/300/300'],
            ['Gaseosa cola 2.25L',     9.90, 75,  'https://picsum.photos/seed/gaseosa/300/300'],
        ];

        $rows = collect($items)->map(fn($i) => [
            'name'        => $i[0],
            'description' => $i[0],
            'price'       => $i[1],
            'stock'       => $i[2],
            'image_url'   => $i[3],
            'created_at'  => $now,
            'updated_at'  => $now,
        ])->all();

        Product::insert($rows);
    }
}