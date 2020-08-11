<?php

namespace WebSK\Auth\User;

use WebSK\Entity\EntityService;

/**
 * Class UserRoleService
 * @method UserRole getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Auth\User
 */
class UserRoleService extends EntityService
{
    /** @var UserRoleRepository */
    protected $repository;

    /**
     * @param int $user_id
     * @return array
     */
    public function getIdsArrByUserId(int $user_id)
    {
        return $this->repository->findIdsArrForUserId($user_id);
    }

    /**
     * @param int $role_id
     * @return array
     */
    public function getIdsArrByRoleId(int $role_id)
    {
        return $this->repository->findIdsArrForRoleId($role_id);
    }
}
