<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $addUser = 'add User';
        $editUser = 'edit User';
        $deleteUser = 'delete User';
        $approveStore = 'approve user';
        $suspendStore = 'suspend user';

        $addStore = 'add store';
        $editStore = 'edit store';
        $deleteStore = 'delete store';

        $addProductLine = 'add productline';
        $editProductLine = 'edit productline';
        $deleteProductLine = 'delete productline';

        $addBrand = 'add brand';
        $editBrand = 'edit brand';
        $deleteBrand = 'delete brand';

        $addProduct = 'add product';
        $editProduct = 'edit product';
        $deleteProduct = 'delete product';
        $viewProduct = 'view product';

        Permission::create(['name' => $addUser]);
        Permission::create(['name' => $deleteUser]);
        Permission::create(['name' => $editUser]);

        Permission::create(['name' => $approveStore]);
        Permission::create(['name' => $suspendStore]);

        Permission::create(['name' => $addStore]);
        Permission::create(['name' => $editStore]);
        Permission::create(['name' => $deleteStore]);

        Permission::create(['name' => $addBrand]);
        Permission::create(['name' => $editBrand]);
        Permission::create(['name' => $deleteBrand]);

        Permission::create(['name' => $addProductLine]);
        Permission::create(['name' => $editProductLine]);
        Permission::create(['name' => $deleteProductLine]);

        Permission::create(['name' => $addProduct]);
        Permission::create(['name' => $editProduct]);
        Permission::create(['name' => $deleteProduct]);
        Permission::create(['name' => $viewProduct]);


        $superAdmin = 'super-admin';
        $systemAdmin = 'system-admin';
        $storeOwner = 'store-owner';
        $storeAdmin = 'store-admin';
        $customer = 'customer';

        Role::create(['name' => $superAdmin])->givePermissionTo(Permission::all());

        Role::create(['name' => $systemAdmin])->givePermissionTo([
            $addUser,
            $editUser,
            $deleteUser,
            $approveStore,
            $suspendStore,
            $addStore,
            $editStore,
            $deleteStore

        ]);

        Role::create(['name' => $storeOwner])->givePermissionTo([
            $addStore,
            $editStore,
            $deleteStore,
            $addProductLine,
            $editProductLine,
            $deleteProductLine,
            $addBrand,
            $editBrand,
            $deleteBrand,
            $addProduct,
            $editProduct,
            $deleteProduct,
        ]);

        Role::create(['name' => $storeAdmin])->givePermissionTo([
            $addStore,
            $editStore,
            $deleteStore,
            $editProductLine,
            $editBrand,
            $addProduct,
            $editProduct,
            $deleteProduct,
        ]);

        Role::create(['name' => $customer])->givePermissionTo([
            $viewProduct
        ]);
    }
}
