<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Cheese Burger',
            'description' => 'Delicious cheese burger',
            'price' => 89,
            'image' => 'images/Burger.jpg',
            'category' => 'burgers',
        ]);

        Product::create([
            'name' => 'Pepperoni Pizza',
            'description' => 'Tasty pepperoni pizza',
            'price' => 169,
            'image' => 'images/pizza.jpg',
            'category' => 'pizzas',
        ]);

        Product::create([
            'name' => 'Chicken Wings Rice Meal',
            'description' => 'Delicious chicken rice meal',
            'price' => 129,
            'image' => 'images/chicken-wings.jpg',
            'category' => 'ricemeals',
        ]);

        Product::create([
            'name' => 'Milktea',
            'description' => 'Refreshing milktea drink',
            'price' => 49,
            'image' => 'images/milktea.jpg',
            'category' => 'drinks',
        ]);
    }
}
