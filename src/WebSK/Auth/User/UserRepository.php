<?php

namespace WebSK\Auth\User;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class UserRepository
 * @package WebSK\Auth\User
 */
class UserRepository extends EntityRepository
{
    /**
     * @param string $email
     * @return int|null
     */
    public function findUserIdByEmail(string $email): ?int
    {
        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " .  Sanitize::sanitizeSqlColumnName(User::_EMAIL). "=?"
            . " LIMIT 1";
        $param_arr = [$email];

        return self::normalizeId($this->db_service->readField($query, $param_arr));
    }

    /**
     * @param string $confirm_code
     * @return int|null
     */
    public function findUserIdByConfirmCode(string $confirm_code): ?int
    {
        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " .  Sanitize::sanitizeSqlColumnName(User::_CONFIRM_CODE) . "=?"
            . " LIMIT 1";
        $param_arr = [$confirm_code];

        return self::normalizeId($this->db_service->readField($query, $param_arr));
    }

    /**
     * @param string $provider_name
     * @param string $provider_uid
     * @return int|null
     */
    public function findUserIdIfExistByProvider(string $provider_name, string $provider_uid): ?int
    {
        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " . Sanitize::sanitizeSqlColumnName(User::_PROVIDER) . " = ?"
            . " AND " . Sanitize::sanitizeSqlColumnName(User::_PROVIDER_UID) . " = ?";

        return self::normalizeId($this->db_service->readField(
            $query,
            [$provider_name, $provider_uid]
        ));
    }

    /**
     * @param string $email
     * @param string $salt_password
     * @return int|null
     */
    public function findUserIdByEmailAndPassword(string $email, string $salt_password): ?int
    {
        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " .  Sanitize::sanitizeSqlColumnName(User::_EMAIL). "=?"
            . " AND " .  Sanitize::sanitizeSqlColumnName(User::_PASSW). "=?"
            . " LIMIT 1";
        $param_arr = [$email, $salt_password];

        return self::normalizeId($this->db_service->readField($query, $param_arr));
    }

    private static function normalizeId(mixed $value): ?int
    {
        if ($value === null || $value === false) {
            return null;
        }
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && ctype_digit($value)) {
            return (int) $value;
        }

        throw new \UnexpectedValueException('User ID must be an integer.');
    }
}
