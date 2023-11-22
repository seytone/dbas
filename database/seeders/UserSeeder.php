<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Super',
            'lastname' => 'Admin',
            'email' => 'sadmin@distribuidorabit.com',
            'password' => bcrypt('Password123!'),
        ])->assignRole('Superadmin');

		User::create([
            'name' => 'Tulio',
            'lastname' => 'Ramirez',
            'email' => 'tramirez@distribuidorabit.com',
            'password' => bcrypt('Password123!'),
        ])->assignRole('Superadmin');

		User::create([
            'name' => 'Emily',
            'lastname' => 'Sandoval',
            'email' => 'ventas@distribuidorabit.com',
            'password' => bcrypt('Password123!'),
        ])->assignRole('Coordinador', 'Vendedor');

		User::create([
            'name' => 'Vendedor',
            'lastname' => '1',
            'email' => 'vendedor1@distribuidorabit.com',
            'password' => bcrypt('Password123!'),
        ])->assignRole('Vendedor');
    }
}
