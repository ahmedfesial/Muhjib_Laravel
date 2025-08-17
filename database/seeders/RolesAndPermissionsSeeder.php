<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $this->call(RolesAndPermissionsSeeder::class);
        // Define permissions (CRUD + domain-specific)
        $perms = [
            // Brands/Main/Sub Categories/Products/Prices
            'brands.viewAny','brands.view','brands.create','brands.update','brands.delete',
            'main_categories.*','sub_categories.*',
            'products.viewAny','products.view','products.create','products.update','products.delete',
            'product_prices.view','product_prices.update','product_prices.upload',

            // Clients/Baskets/Catalogs/Templates
            'clients.*','baskets.*','catalogs.*','templates.*',

            // Quotes & actions
            'quote_requests.viewAny','quote_requests.view','quote_requests.create',
            'quote_requests.update','quote_requests.delete',
            'quote_requests.assign','quote_requests.transfer','quote_actions.view',

            // Notifications & logs
            'notifications.view','notifications.update',
            'price_upload_logs.view',
        ];

        foreach ($perms as $p) {
            Permission::findOrCreate($p, 'api');
        }

        // Roles (already exist in your DB, but findOrCreate is idempotent)
        $super = Role::findOrCreate('super_admin','api');
        $admin = Role::findOrCreate('admin','api');
        $user  = Role::findOrCreate('user','api');

        // Give everything to super_admin
        $super->givePermissionTo(Permission::all());

        // Admin: broad CRUD, but not system-level stuff
        $admin->givePermissionTo([
            'brands.*','main_categories.*','sub_categories.*',
            'products.*','product_prices.view','product_prices.update','product_prices.upload',
            'clients.*','baskets.*','catalogs.*','templates.*',
            'quote_requests.viewAny','quote_requests.view','quote_requests.create','quote_requests.update',
            'quote_actions.view','notifications.view','notifications.update','price_upload_logs.view',
        ]);

        // User: read-most, limited write
        $user->givePermissionTo([
            'brands.viewAny','brands.view',
            'main_categories.*','sub_categories.*',
            'products.viewAny','products.view',
            'product_prices.view',
            'clients.viewAny','clients.view','clients.create',
            'baskets.viewAny','baskets.view','baskets.create','baskets.update',
            'catalogs.viewAny','catalogs.view','catalogs.create',
            'quote_requests.viewAny','quote_requests.view','quote_requests.create',
            'quote_actions.view','notifications.view',
            'templates.viewAny','templates.view',
        ]);

        // Optionally attach roles to existing users
        // (Your DB already has admin/super_admin users. :contentReference[oaicite:5]{index=5})
        User::where('email','superadmin@gmail.com')->first()?->syncRoles(['super_admin']);
        User::where('email','admin@gmail.com')->first()?->syncRoles(['admin']);
    }
}
