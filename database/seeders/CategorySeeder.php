<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Men',
            'details' => 'Men\'s Store'
        ]);
        Category::create([
            'name' => 'Women',
            'details' => 'Women\'s Store'
        ]);
        Category::create([
            'name' => 'Lifestyle',
            'details' => 'Lifestyle Store'
        ]);
        Category::create([
            'name' => 'Perfumes',
            'details' => 'Perfume Store'
        ]);
    }
}
