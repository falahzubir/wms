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
        Permission::firstOrCreate(['name' => 'company.create']);
        Permission::firstOrCreate(['name' => 'order.download']);
        Permission::firstOrCreate(['name' => 'barcode.scan']);
        Permission::firstOrCreate(['name' => 'user.list']);
        Permission::firstOrCreate(['name' => 'user.edit']);
        Permission::firstOrCreate(['name' => 'user.create']);
        Permission::firstOrCreate(['name' => 'order.approve_for_shipping']);
        Permission::firstOrCreate(['name' => 'shipping.cancel']);
        Permission::firstOrCreate(['name' => 'operational_model.update']);
        Permission::firstOrCreate(['name' => 'permission.generate_packing']);

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
        Permission::firstOrCreate(['name' => 'view.scan_setting']);
        Permission::firstOrCreate(['name' => 'view.settings']);
        Permission::firstOrCreate(['name' => 'view.dashboard']);
        Permission::firstOrCreate(['name' => 'view.setting_bucket_automation']);
        Permission::firstOrCreate(['name' => 'view.attempt_order_list']);

        //product permission
        Permission::firstOrCreate(['name' => 'product.list']);
        Permission::firstOrCreate(['name' => 'product.create']);
        Permission::firstOrCreate(['name' => 'product.edit']);
        Permission::firstOrCreate(['name' => 'product.delete']);

        Permission::firstOrCreate(['name' => 'report.view']);
        Permission::firstOrCreate(['name' => 'report.view_sla']);
        Permission::firstOrCreate(['name' => 'report.view_outbound']);
        Permission::firstOrCreate(['name' => 'report.view_order_matrix']);
        Permission::firstOrCreate(['name' => 'report.view_pending']);
        Permission::firstOrCreate(['name' => 'report.view_shipment']);

        Permission::firstOrCreate(['name' => 'view.claim_list']);
        Permission::firstOrCreate(['name' => 'view.bucket_category_list']);

        Permission::firstOrCreate(['name' => 'view.template_setting']);
        Permission::firstOrCreate(['name' => 'view.custom_template_setting']);

        Permission::firstOrCreate(['name' => 'view.shipping_doc_information']);
        Permission::firstOrCreate(['name' => 'view.courier_setting']);
        Permission::firstOrCreate(['name' => 'view.selected_coverage']);

        //state group permission
        Permission::firstOrCreate(['name' => 'shipping_cost.view']);
        Permission::firstOrCreate(['name' => 'state_group.list']);
        Permission::firstOrCreate(['name' => 'state_group.create']);
        Permission::firstOrCreate(['name' => 'state_group.edit']);
        Permission::firstOrCreate(['name' => 'state_group.delete']);

        //shipping cost permission
        Permission::firstOrCreate(['name' => 'shipping_cost.list']);
        Permission::firstOrCreate(['name' => 'shipping_cost.create']);
        Permission::firstOrCreate(['name' => 'shipping_cost.edit']);
        Permission::firstOrCreate(['name' => 'shipping_cost.delete']);
        Permission::firstOrCreate(['name' => 'view.picking_list_setting']);

        //weight category permission
        Permission::firstOrCreate(['name' => 'weight_category.list']);
        Permission::firstOrCreate(['name' => 'weight_category.create']);
        Permission::firstOrCreate(['name' => 'weight_category.edit']);
        Permission::firstOrCreate(['name' => 'weight_category.delete']);

        // country list permission
        Permission::firstOrCreate(['name' => 'view.country_list']);
        Permission::firstOrCreate(['name' => 'country_list.add']);
        Permission::firstOrCreate(['name' => 'country_list.edit']);
        Permission::firstOrCreate(['name' => 'country_list.delete']);

        // currency list permission
        Permission::firstOrCreate(['name' => 'currency.view']);
        Permission::firstOrCreate(['name' => 'currency_list.view']);
        Permission::firstOrCreate(['name' => 'currency_list.add']);
        Permission::firstOrCreate(['name' => 'currency_list.edit']);
        Permission::firstOrCreate(['name' => 'currency_list.delete']);

        // exchange rate list permission
        Permission::firstOrCreate(['name' => 'exchange_rate.view']);
        Permission::firstOrCreate(['name' => 'exchange_rate.add']);
        Permission::firstOrCreate(['name' => 'exchange_rate.edit']);
        Permission::firstOrCreate(['name' => 'exchange_rate.delete']);

        // create roles and assign created permissions
        $role = Role::firstOrCreate(['name' => 'IT_Admin']);
        $role->givePermissionTo(Permission::all());

        //giverole to user
        $user = \App\Models\User::find(1);
        $user->assignRole('IT_Admin');
    }
}
