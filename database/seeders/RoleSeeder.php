<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create(['name' => 'Superadmin']);
        $admin->givePermissionTo('view_dashboard');
        $admin->givePermissionTo('manage_management');
        $admin->givePermissionTo('manage_security');
        $admin->givePermissionTo('manage_sellers');
        $admin->givePermissionTo('manage_users');
        $admin->givePermissionTo('manage_sales');
        $admin->givePermissionTo('manage_clients');
        $admin->givePermissionTo('manage_products');
        $admin->givePermissionTo('manage_services');
        $admin->givePermissionTo('manage_providers');
        $admin->givePermissionTo('manage_profile');
        $admin->givePermissionTo('report_sales');

		$manager = Role::create(['name' => 'Coordinador']);
		$manager->givePermissionTo('view_dashboard');
		$manager->givePermissionTo('manage_management');
		$manager->givePermissionTo('manage_sellers');
		$manager->givePermissionTo('manage_sales');
		$manager->givePermissionTo('manage_clients');
		$manager->givePermissionTo('manage_products');
		$manager->givePermissionTo('manage_services');
		$manager->givePermissionTo('manage_providers');
		$manager->givePermissionTo('manage_profile');
		$manager->givePermissionTo('report_sales');

		$seller = Role::create(['name' => 'Vendedor']);
		$seller->givePermissionTo('view_dashboard');
		$seller->givePermissionTo('manage_sales');
		$seller->givePermissionTo('manage_profile');
		$seller->givePermissionTo('report_sales');

		$seller = Role::create(['name' => 'Otro']);
		$seller->givePermissionTo('view_dashboard');
		$seller->givePermissionTo('manage_profile');
    }
}
