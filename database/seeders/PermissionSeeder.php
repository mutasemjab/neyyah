<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        $permissions_admin = [
            // Roles Management
            'roles-index',
            'roles-create',
            'roles-edit',
            'roles-delete',

            // Employees Management
            'employees-index',
            'employees-create',
            'employees-edit',
            'employees-delete',

            // Users Management
            'users-index',
            'users-create',
            'users-edit',
            'users-delete',

            // Banners Management
            'banners-index',
            'banners-create',
            'banners-edit',
            'banners-delete',
    
            'brand-index',
            'brand-create',
            'brand-edit',
            'brand-delete',

           

            // Settings Management
            'settings-index',
            'settings-create',
            'settings-edit',
            'settings-delete',

         

            // Pages Management
            'pages-index',
            'pages-create',
            'pages-edit',
            'pages-delete',
        
            'service-index',
            'service-create',
            'service-edit',
            'service-delete',
     
            'bottomSectionHome-index',
            'bottomSectionHome-create',
            'bottomSectionHome-edit',
            'bottomSectionHome-delete',
     
            'about-index',
            'about-create',
            'about-edit',
            'about-delete',
      
            'consumable-index',
            'consumable-create',
            'consumable-edit',
            'consumable-delete',
      
            'consumable_product-index',
            'consumable_product-create',
            'consumable_product-edit',
            'consumable_product-delete',

            'our_service-index',
            'our_service-create',
            'our_service-edit',
            'our_service-delete',
           
            'news-index',
            'news-create',
            'news-edit',
            'news-delete',

            'career-index',
            'career-create',
            'career-edit',
            'career-delete',

            'position-index',
            'position-create',
            'position-edit',
            'position-delete',
    
            'category-index',
            'category-create',
            'category-edit',
            'category-delete',
        
            'product-index',
            'product-create',
            'product-edit',
            'product-delete',

          
        ];

        $permissions = [];

        foreach ($permissions_admin as $permission) {
            $permissions[] = [
                'name'       => $permission,
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Permission::insert($permissions);

    }
}