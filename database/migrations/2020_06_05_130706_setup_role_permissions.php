<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Role;
use App\Models\Permission;
use App\Helpers\Acl;

class SetupRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Acl::roles() as $role) {
            Role::findOrCreate($role);
        }

        $adminRole = Role::findByName(Acl::ROLE_ADMIN);
        $userRole = Role::findByName(Acl::ROLE_USER);

        foreach (Acl::permissions() as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        // Setup basic permission
        $adminRole->givePermissionTo(Acl::permissions());
        $userRole->givePermissionTo([
            Acl::PERMISSION_VIEW_MENU_PLANNING,
            Acl::PERMISSION_VIEW_MENU_BRAND_PLANNING,
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
