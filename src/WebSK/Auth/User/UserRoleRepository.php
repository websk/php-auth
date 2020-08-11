<?php

namespace WebSK\Auth\User;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class UserRoleRepository
 * @package WebSK\Auth\User
 */
class UserRoleRepository extends EntityRepository
{
    /**
     * @param int $user_id
     * @return array
     */
    public function findIdsArrForUserId(int $user_id): array
    {
        return $this->db_service->readColumn(
            'SELECT ' . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(UserRole::_USER_ID) . '=?',
            [$user_id]
        );
    }

    /**
     * @param int $role_id
     * @return array
     */
    public function findIdsArrForRoleId(int $role_id): array
    {
        return $this->db_service->readColumn(
            'SELECT ' . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(UserRole::_ROLE_ID) . '=?',
            [$role_id]
        );
    }
}
