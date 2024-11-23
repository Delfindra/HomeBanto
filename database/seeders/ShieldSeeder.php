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

        $rolesWithPermissions = '[{"name":"admin","guard_name":"web","permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_diet","view_any_diet","create_diet","update_diet","restore_diet","restore_any_diet","replicate_diet","reorder_diet","delete_diet","delete_any_diet","force_delete_diet","force_delete_any_diet","view_ingredients","view_any_ingredients","create_ingredients","update_ingredients","restore_ingredients","restore_any_ingredients","replicate_ingredients","reorder_ingredients","delete_ingredients","delete_any_ingredients","force_delete_ingredients","force_delete_any_ingredients","view_master::data","view_any_master::data","create_master::data","update_master::data","restore_master::data","restore_any_master::data","replicate_master::data","reorder_master::data","delete_master::data","delete_any_master::data","force_delete_master::data","force_delete_any_master::data","view_menu","view_any_menu","create_menu","update_menu","restore_menu","restore_any_menu","replicate_menu","reorder_menu","delete_menu","delete_any_menu","force_delete_menu","force_delete_any_menu","view_recipe","view_any_recipe","create_recipe","update_recipe","restore_recipe","restore_any_recipe","replicate_recipe","reorder_recipe","delete_recipe","delete_any_recipe","force_delete_recipe","force_delete_any_recipe","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","page_EditProfilePage"]},{"name":"user","guard_name":"web","permissions":["view_ingredients","view_any_ingredients","create_ingredients","update_ingredients","delete_ingredients","delete_any_ingredients","view_menu","view_any_menu","create_menu","update_menu","delete_menu","delete_any_menu","page_EditProfilePage","publish_ingredients","publish_menu"]},{"name":"super_admin","guard_name":"web","permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_diet","view_any_diet","create_diet","update_diet","restore_diet","restore_any_diet","replicate_diet","reorder_diet","delete_diet","delete_any_diet","force_delete_diet","force_delete_any_diet","view_ingredients","view_any_ingredients","create_ingredients","update_ingredients","restore_ingredients","restore_any_ingredients","replicate_ingredients","reorder_ingredients","delete_ingredients","delete_any_ingredients","force_delete_ingredients","force_delete_any_ingredients","view_master::data","view_any_master::data","create_master::data","update_master::data","restore_master::data","restore_any_master::data","replicate_master::data","reorder_master::data","delete_master::data","delete_any_master::data","force_delete_master::data","force_delete_any_master::data","view_menu","view_any_menu","create_menu","update_menu","restore_menu","restore_any_menu","replicate_menu","reorder_menu","delete_menu","delete_any_menu","force_delete_menu","force_delete_any_menu","view_recipe","view_any_recipe","create_recipe","update_recipe","restore_recipe","restore_any_recipe","replicate_recipe","reorder_recipe","delete_recipe","delete_any_recipe","force_delete_recipe","force_delete_any_recipe","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","page_EditProfilePage","publish_diet","publish_ingredients","publish_master::data","publish_menu","publish_recipe","publish_user"]}]';
        $directPermissions = '[{"name":"edit Post","guard_name":"web"},{"name":"delete Post","guard_name":"web"},{"name":"publish Post","guard_name":"web"}]';

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
