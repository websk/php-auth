<?php

namespace WebSK\Auth\User;

use WebSK\Entity\EntityService;

/**
 * Class RoleService
 * @method Role|null getById(?int $entity_id, bool $exception_if_not_loaded = true)
 * @package WebSK\Auth\User
 */
class RoleService extends EntityService
{
    /**
     * @return Role[]
     */
    public function getAllRoles(): array
    {
        $role_objs_arr = [];

        $role_ids_arr = $this->getAllIdsArrByIdAsc();

        foreach ($role_ids_arr as $role_id) {
            $role = $this->getById($role_id);
            if ($role !== null) {
                $role_objs_arr[] = $role;
            }
        }

        usort(
            $role_objs_arr,
            [$this, 'sortByName']
        );

        return $role_objs_arr;
    }

    /**
     * @param Role $a_role_obj
     * @param Role $b_role_obj
     * @return int
     */
    protected function sortByName(Role $a_role_obj, Role $b_role_obj): int
    {
        return strcmp($a_role_obj->getName(), $b_role_obj->getName());
    }
}
