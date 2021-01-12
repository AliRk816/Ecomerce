<?php

use App\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'High Tech',
            'slug' => 'high-tech'
        ]);

        Category::create([
            'name' => 'Books',
            'slug' => 'books'
        ]);

        Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture'
        ]);

        Category::create([
            'name' => 'Games & Consoles',
            'slug' => 'games-consoles'
        ]);

        Category::create([
            'name' => 'Food',
            'slug' => 'food'
        ]);
    }
}
