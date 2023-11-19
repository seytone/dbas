<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'code' => 'CLI-' . $this->faker->unixTime(),
			'title' => $this->faker->company(),
			'document' => $this->faker->numberBetween(),
			'email' => $this->faker->email(),
			'phone' => $this->faker->phoneNumber(),
			'address' => $this->faker->address(),
        ];
    }
}
