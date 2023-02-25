<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::firstOrCreate(['name' => 'permission.update']);
        Permission::firstOrCreate(['name' => 'picking_list.generate']);
        Permission::firstOrCreate(['name' => 'picking_list.download']);
        Permission::firstOrCreate(['name' => 'consignment_note.generate']);
        Permission::firstOrCreate(['name' => 'consignment_note.download']);
        Permission::firstOrCreate(['name' => 'order.reject']);
        Permission::firstOrCreate(['name' => 'bucket.update']);
        Permission::firstOrCreate(['name' => 'bucket.delete']);
        Permission::firstOrCreate(['name' => 'tracking.upload']);
        Permission::firstOrCreate(['name' => 'tracking.update']);
        Permission::firstOrCreate(['name' => 'company.update']);
        Permission::firstOrCreate(['name' => 'order.download']);
        Permission::firstOrCreate(['name' => 'barcode.scan']);
        Permission::firstOrCreate(['name' => 'user.list']);
        Permission::firstOrCreate(['name' => 'user.edit']);
        Permission::firstOrCreate(['name' => 'user.create']);

        // create roles and assign created permissions
        $role = Role::firstOrCreate(['name' => 'IT_Admin']);
        $role->givePermissionTo(Permission::all());
    }
}
