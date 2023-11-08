<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'title' => ucwords($this->faker->words(3, true)),
			'description' => 'Lorem ipsum dolor sit amet',
        ];
    }
}
