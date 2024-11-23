<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->call(ShieldSeeder::class);
        $permissions = ['edit Post', 'delete Post', 'publish Post'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);

        $editorRole->givePermissionTo(['name' => 'edit Post']);
        $adminRole->givePermissionTo($permissions);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole($adminRole);


        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $user->assignRole($userRole);
        $editor = User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@example.com'
        ]);
        $editor->assignRole($editorRole);
    }
}
