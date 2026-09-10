<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serve = Permission::create([
            'name' => 'Serve Order',
        ]);

        $pay = Permission::create([
            'name' => 'Pay Order',
        ]);

        $cancel = Permission::create([
            'name' => 'Cancel Order',
        ]);

        $confirm = Permission::create([
            'name' => 'Confirm Reserve',
        ]);

        $reject = Permission::create([
            'name' => 'Cancel Reserve',
        ]);

        $complete = Permission::create([
            'name' => 'Complete Reserve',
        ]);

        $waiter = Role::where('slug', 'waiter')->first();
        $cashier = Role::where('slug', 'cashier')->first();
        $admin = Role::where('slug', 'admin')->first();


        $waiter->permissions()->attach($serve);
        $waiter->permissions()->attach($complete);
        $cashier->permissions()->attach($pay);
        $cashier->permissions()->attach($confirm);
        $admin->permissions()->attach([$serve->id, $pay->id, $cancel->id]);
        $admin->permissions()->attach([$confirm->id, $reject->id, $complete->id]);
    }
}
