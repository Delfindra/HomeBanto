<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"admin","guard_name":"web","permissions":["view_diet","view_any_diet","create_diet","update_diet","delete_diet","delete_any_diet","publish_diet","view_ingredients","view_any_ingredients","create_ingredients","update_ingredients","delete_ingredients","delete_any_ingredients","publish_ingredients","view_master::data","view_any_master::data","create_master::data","update_master::data","delete_master::data","delete_any_master::data","publish_master::data","view_menu","view_any_menu","create_menu","update_menu","delete_menu","delete_any_menu","publish_menu","view_recipe","view_any_recipe","create_recipe","update_recipe","delete_recipe","delete_any_recipe","publish_recipe","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_user","view_any_user","create_user","update_user","delete_user","delete_any_user","publish_user","page_EditProfilePage"]},{"name":"user","guard_name":"web","permissions":["view_ingredients","view_any_ingredients","create_ingredients","update_ingredients","delete_ingredients","delete_any_ingredients","publish_ingredients","view_menu","view_any_menu","create_menu","update_menu","delete_menu","delete_any_menu","publish_menu","page_EditProfilePage"]},{"name":"editor","guard_name":"web","permissions":["edit Post"]}]';
        $directPermissions = '{"1":{"name":"delete Post","guard_name":"web"},"2":{"name":"publish Post","guard_name":"web"}}';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
