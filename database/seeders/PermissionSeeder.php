<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('cache:clear');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'view_dashboard']);
        Permission::create(['name' => 'manage_management']);
        Permission::create(['name' => 'manage_security']);
        Permission::create(['name' => 'manage_sellers']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'manage_sales']);
        Permission::create(['name' => 'manage_clients']);
        Permission::create(['name' => 'manage_products']);
        Permission::create(['name' => 'manage_services']);
        Permission::create(['name' => 'manage_providers']);
        Permission::create(['name' => 'manage_profile']);
        Permission::create(['name' => 'report_sales']);
    }
}
