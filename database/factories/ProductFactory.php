<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'name' => $name= $this -> faker -> name(),
           'slug' => Str::slug($name) ,
           'price' => $this -> faker -> randomNumber(3,true) ,
           'stock' => $this -> faker -> randomNumber(3,true),
           'category_id' => Category::inRandomOrder()->first()->id
        ];
    }
}
