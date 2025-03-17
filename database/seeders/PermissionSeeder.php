<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // create permissions
       Permission::create(["name"=>"view_dashboard"]);  
       Permission::create(["name"=>"view_products"]);
       Permission::create(["name"=>"create_products"]);
       Permission::create(["name"=>"edit_products"]);
       Permission::create(["name"=>"delete_products"]);
       Permission::create(["name"=>"view_categories"]);
       Permission::create(["name"=>"create_categories"]);
       Permission::create(["name"=>"edit_categories"]);
       Permission::create(["name"=>"delete_categories"]);
       Permission::create(["name"=>"view_users"]);
       Permission::create(["name"=>"create_users"]);
       Permission::create(["name"=>"edit_users"]);
       Permission::create(["name"=>"delete_users"]);
        // create roles and assign permissions
        // admin
        $superAdmin = Role::create(["name"=>"super_admin"]);
        $superAdmin -> givePermissionTo(["view_dashboard","view_products","create_products","edit_products","delete_products","view_categories","create_categories","edit_categories","delete_categories","view_users","create_users","edit_users","delete_users"]);
        // product manager
        $productManager = Role::create(["name"=>"product_manager"]);
        $productManager -> givePermissionTo(["view_dashboard","view_products","view_categories","create_products","edit_products","delete_products","view_categories","create_categories","edit_categories","delete_categories"]);
        // user manager
        $userManager = Role::create(["name"=>"user_manager"]);
        $userManager -> givePermissionTo(["view_dashboard","view_users","create_users","edit_users","delete_users"]);
        // create client role
        $client = Role::create(["name"=>"client"]);
        $client->givePermissionTo(["view_dashboard", "view_products"]);
    }
}
