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
    /** @return list<int> */
    public function findIdsArrForUserId(int $user_id): array
    {
        return self::normalizeIds($this->db_service->readColumn(
            'SELECT ' . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(UserRole::_USER_ID) . '=?',
            [$user_id]
        ));
    }

    /** @return list<int> */
    public function findIdsArrForRoleId(int $role_id): array
    {
        return self::normalizeIds($this->db_service->readColumn(
            'SELECT ' . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(UserRole::_ROLE_ID) . '=?',
            [$role_id]
        ));
    }

    /**
     * @param array<array-key, mixed> $values
     * @return list<int>
     */
    private static function normalizeIds(array $values): array
    {
        $ids = [];
        foreach ($values as $value) {
            if (is_int($value)) {
                $ids[] = $value;
                continue;
            }
            if (is_string($value) && ctype_digit($value)) {
                $ids[] = (int) $value;
                continue;
            }

            throw new \UnexpectedValueException('User role ID must be an integer.');
        }

        return $ids;
    }
}
