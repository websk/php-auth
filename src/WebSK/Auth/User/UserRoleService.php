<?php

namespace WebSK\Auth\User;

use WebSK\Entity\EntityService;
use WebSK\Entity\EntityRepository;

/**
 * Class UserRoleService
 * @method UserRole|null getById(?int $entity_id, bool $exception_if_not_loaded = true)
 * @package WebSK\Auth\User
 */
class UserRoleService extends EntityService
{
    /** @var UserRoleRepository */
    protected EntityRepository $repository;

    /** @return list<int> */
    public function getIdsArrByUserId(int $user_id): array
    {
        return $this->repository->findIdsArrForUserId($user_id);
    }

    /** @return list<int> */
    public function getIdsArrByRoleId(int $role_id): array
    {
        return $this->repository->findIdsArrForRoleId($role_id);
    }
}
