<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'code' => $this->faker->bothify('?????-#####'),
			'title' => $this->faker->company(),
			'description' => 'Lorem ipsum dolor sit amet',
			'price' => $this->faker->numberBetween(99, 999),
        ];
    }
}
