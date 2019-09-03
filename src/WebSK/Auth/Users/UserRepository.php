<?php

namespace WebSK\Auth\Users;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class UserRepository
 * @package WebSK\Auth\Users
 */
class UserRepository extends EntityRepository
{
    /**
     * @param string $email
     * @return false|mixed
     */
    public function findUserIdByEmail(string $email)
    {
        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " .  Sanitize::sanitizeSqlColumnName(User::_EMAIL). "=?"
            . " LIMIT 1";
        $param_arr = [$email];

        return $this->db_service->readField($query, $param_arr);
    }

    /**
     * @param string $confirm_code
     * @return false|mixed
     * @throws \Exception
     */
    public function findUserIdByConfirmCode(string $confirm_code)
    {
        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " .  Sanitize::sanitizeSqlColumnName(User::_CONFIRM_CODE). "=?"
            . " LIMIT 1";
        $param_arr = [$confirm_code];

        return $this->db_service->readField($query, $param_arr);
    }
}
