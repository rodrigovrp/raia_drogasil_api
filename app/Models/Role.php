<?php
namespace App\Models;

use App\Helpers\Acl;
use Spatie\Permission\Models\Permission;

/**
 * Class Role
 *
 * @property Permission[] $permissions
 * @property string $name
 * @package App\Models
 */
class Role extends \Spatie\Permission\Models\Role
{
    /**
     * Check whether current role is admin
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->name === Acl::ROLE_ADMIN;
    }
}
