<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Brand;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'brand_id' => Brand::factory(),
			'category_id' => Category::factory(),
			'type' => $this->faker->randomElement(['hardware', 'software']),
			'code' => $this->faker->bothify('?????-#####'),
			'title' => ucwords($this->faker->words(3, true)),
			'description' => 'Lorem ipsum dolor sit amet',
			'cost' => $this->faker->numberBetween(100, 400),
			'price' => $this->faker->numberBetween(500, 900),
        ];
    }
}
