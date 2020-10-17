<?php
namespace App\Models;

use App\Helpers\Acl;
use Illuminate\Database\Query\Builder;

/**
 * Class Permission
 *
 * @package App\Models
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    /**
     * To exclude permission management from the list
     *
     * @param $query
     * @return Builder
     */
    public function scopeAllowed($query)
    {
        return $query->where('name', '!=', Acl::PERMISSION_PERMISSION_MANAGE);
    }
}
