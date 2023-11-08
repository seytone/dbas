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
			'code' => mb_strtoupper($this->faker->lexify('CLI-????')),
			'title' => $this->faker->company(),
			'document' => $this->faker->numberBetween(),
			'email' => $this->faker->email(),
			'phone' => $this->faker->phoneNumber(),
			'address' => $this->faker->address(),
        ];
    }
}
