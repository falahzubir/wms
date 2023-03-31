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
        Permission::firstOrCreate(['name' => 'order.approve_for_shipping']);
        Permission::firstOrCreate(['name' => 'shipping.cancel']);

        //view permission
        Permission::firstOrCreate(['name' => 'view.overall_list']);
        Permission::firstOrCreate(['name' => 'view.pending_list']);
        Permission::firstOrCreate(['name' => 'view.bucket_list']);
        Permission::firstOrCreate(['name' => 'view.packing_list']);
        Permission::firstOrCreate(['name' => 'view.rts_list']);
        Permission::firstOrCreate(['name' => 'view.shipping_list']);
        Permission::firstOrCreate(['name' => 'view.delivered_list']);
        Permission::firstOrCreate(['name' => 'view.return_list']);
        Permission::firstOrCreate(['name' => 'view.reject_list']);
        Permission::firstOrCreate(['name' => 'view.scan_parcel']);
        Permission::firstOrCreate(['name' => 'view.settings']);
        Permission::firstOrCreate(['name' => 'view.dashboard']);


        // create roles and assign created permissions
        $role = Role::firstOrCreate(['name' => 'IT_Admin']);
        $role->givePermissionTo(Permission::all());

        //giverole to user
        $user = \App\Models\User::find(1);
        $user->assignRole('IT_Admin');
    }
}
