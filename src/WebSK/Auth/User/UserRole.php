<?php

namespace WebSK\Auth\User;

use WebSK\Entity\Entity;

/**
 * Class UserRole
 * @package WebSK\Auth\User
 */
class UserRole extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'user.user_role_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'user.user_role_repository';
    const DB_TABLE_NAME = 'users_roles';

    const _USER_ID = 'user_id';
    /** @var int */
    protected $user_id;

    const _ROLE_ID = 'role_id';
    /** @var int */
    protected $role_id;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * @param int $role_id
     */
    public function setRoleId(int $role_id): void
    {
        $this->role_id = $role_id;
    }
}
