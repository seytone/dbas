<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/dashboard');

Auth::routes(['register' => false]);

// Change Password Routes...
Route::get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
Route::patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password_patch');

// Acciones Administrador
Route::group(['middleware' => ['auth'], 'prefix' => 'admin/', 'as' => 'admin.'], function ()
{
	Route::get('help', 'Admin\HelpController@index')->name('help');
	Route::get('dashboard', 'Admin\DashboardController@index')->name('dashboard');
    Route::resource('permissions', 'Admin\PermissionsController');
    Route::resource('roles', 'Admin\RolesController');
    Route::resource('users', 'Admin\UsersController');
	Route::resource('commissions', 'Admin\CommissionController');
	Route::resource('sales', 'Admin\SalesController');
	Route::resource('clients', 'Admin\ClientsController');
	Route::resource('brands', 'Admin\BrandsController');
	Route::resource('categories', 'Admin\CategoriesController');
	Route::resource('products', 'Admin\ProductsController');
	Route::resource('services', 'Admin\ServicesController');
	Route::resource('providers', 'Admin\ProvidersController');
	Route::resource('sellers', 'Admin\SellersController');

	Route::delete('permissions_mass_destroy', 'Admin\PermissionsController@massDestroy')->name('permissions.mass_destroy');
	Route::delete('roles_mass_destroy', 'Admin\RolesController@massDestroy')->name('roles.mass_destroy');
	Route::delete('users_mass_destroy', 'Admin\UsersController@massDestroy')->name('users.mass_destroy');
	Route::delete('commission_mass_destroy', 'Admin\CommissionController@massDestroy')->name('commission.mass_destroy');
	Route::delete('sales_mass_destroy', 'Admin\SalesController@massDestroy')->name('sales.mass_destroy');
	Route::delete('clients_mass_destroy', 'Admin\ClientsController@massDestroy')->name('clients.mass_destroy');
	Route::delete('brands_mass_destroy', 'Admin\BrandsController@massDestroy')->name('brands.mass_destroy');
	Route::delete('categories_mass_destroy', 'Admin\CategoriesController@massDestroy')->name('categories.mass_destroy');
	Route::delete('products_mass_destroy', 'Admin\ProductsController@massDestroy')->name('products.mass_destroy');
	Route::delete('services_mass_destroy', 'Admin\ServicesController@massDestroy')->name('services.mass_destroy');
	Route::delete('sellers_mass_destroy', 'Admin\SellersController@massDestroy')->name('sellers.mass_destroy');
});
