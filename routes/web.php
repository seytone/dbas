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
	Route::match(['get', 'post'], 'dashboard', 'Admin\DashboardController@index')->name('dashboard');
	Route::match(['get', 'post'], 'sales_filter', 'Admin\SalesController@index')->name('sales_filter');
	Route::match(['get', 'post'], 'quotations_filter', 'Admin\QuotationsController@index')->name('quotations_filter');
    
	Route::resource('permissions', 'Admin\PermissionsController');
    Route::resource('roles', 'Admin\RolesController');
    Route::resource('users', 'Admin\UsersController');
	Route::resource('commissions', 'Admin\CommissionController');
	Route::resource('sales', 'Admin\SalesController');
	Route::resource('quotations', 'Admin\QuotationsController');
	Route::resource('clients', 'Admin\ClientsController');
	Route::resource('brands', 'Admin\BrandsController');
	Route::resource('categories', 'Admin\CategoriesController');
	Route::resource('products', 'Admin\ProductsController');
	Route::resource('services', 'Admin\ServicesController');
	Route::resource('providers', 'Admin\ProvidersController');
	Route::resource('sellers', 'Admin\SellersController');
	Route::resource('employees', 'Admin\EmployeesController');
	Route::put('employees/{id}/restore', 'Admin\EmployeesController@restore')->name('employees.restore');

	Route::get('help', 'Admin\HelpController@index')->name('help');
	
	Route::group(['prefix' => 'hours', 'as' => 'hours.'], function ()
	{
		Route::match(['get', 'post'], 'index', 'Admin\HoursController@index')->name('index');
		Route::post('upload', 'Admin\HoursController@upload')->name('upload');
		Route::post('comment', 'Admin\HoursController@comment')->name('comment');
		Route::post('apply-extra', 'Admin\HoursController@applyExtra')->name('apply_extra');
		Route::post('apply-missing', 'Admin\HoursController@applyMissing')->name('apply_missing');
		Route::post('manual-fix', 'Admin\HoursController@manualFix')->name('manual_fix');
		Route::post('pay', 'Admin\HoursController@pay')->name('pay');
		Route::delete('payment_delete/{id}', 'Admin\HoursController@payment_delete')->name('payment_delete');
	});

	Route::post('update_config', 'Admin\ConfigController@updateConfig')->name('update_config');

	Route::get('sale_exists', 'Admin\SalesController@exists')->name('sales.exists');
	Route::get('client_exists', 'Admin\ClientsController@exists')->name('clients.exists');
	Route::get('product_exists', 'Admin\ProductsController@exists')->name('products.exists');
	Route::get('service_exists', 'Admin\ServicesController@exists')->name('services.exists');

	Route::get('products/add/{layout?}', 'Admin\ProductsController@create')->name('products.add');
	Route::get('services/add/{layout?}', 'Admin\ServicesController@create')->name('services.add');

	Route::delete('permissions_mass_destroy', 'Admin\PermissionsController@massDestroy')->name('permissions.mass_destroy');
	Route::delete('roles_mass_destroy', 'Admin\RolesController@massDestroy')->name('roles.mass_destroy');
	Route::delete('users_mass_destroy', 'Admin\UsersController@massDestroy')->name('users.mass_destroy');
	Route::delete('commission_mass_destroy', 'Admin\CommissionController@massDestroy')->name('commission.mass_destroy');
	Route::delete('sales_mass_destroy', 'Admin\SalesController@massDestroy')->name('sales.mass_destroy');
	Route::delete('quotations_mass_destroy', 'Admin\QuotationsController@massDestroy')->name('quotations.mass_destroy');
	Route::get('quotations_heartbeat', 'Admin\QuotationsController@heartbeat')->name('quotations.heartbeat');
	Route::get('quotations_fetch_rates', 'Admin\QuotationsController@fetchRates')->name('quotations.fetch_rates');
	Route::post('quotations_save_rates', 'Admin\QuotationsController@saveRates')->name('quotations.save_rates');
	Route::get('quotations/{quotation}/duplicate', 'Admin\QuotationsController@duplicate')->name('quotations.duplicate');
	Route::get('quotations/{quotation}/pdf', 'Admin\QuotationsController@exportPdf')->name('quotations.pdf');
	Route::get('quotations/{quotation}/print', 'Admin\QuotationsController@printView')->name('quotations.print');
	Route::delete('clients_mass_destroy', 'Admin\ClientsController@massDestroy')->name('clients.mass_destroy');
	Route::delete('brands_mass_destroy', 'Admin\BrandsController@massDestroy')->name('brands.mass_destroy');
	Route::delete('categories_mass_destroy', 'Admin\CategoriesController@massDestroy')->name('categories.mass_destroy');
	Route::delete('products_mass_destroy', 'Admin\ProductsController@massDestroy')->name('products.mass_destroy');
	Route::delete('services_mass_destroy', 'Admin\ServicesController@massDestroy')->name('services.mass_destroy');
	Route::delete('sellers_mass_destroy', 'Admin\SellersController@massDestroy')->name('sellers.mass_destroy');
	Route::delete('employees_mass_destroy', 'Admin\EmployeesController@massDestroy')->name('employees.mass_destroy');
});
