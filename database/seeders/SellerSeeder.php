<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seller;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Seller::create([
			'user_id' => 3,
			'commission_1' => 3,
			'commission_2' => 5,
			'commission_3' => 5,
			'commission_4' => 50,
		]);

		Seller::create([
			'user_id' => 4,
			'commission_1' => 1,
			'commission_2' => 1,
			'commission_3' => 1,
			'commission_4' => 50,
		]);
    }
}
