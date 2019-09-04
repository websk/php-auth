<?php

namespace WebSK\Auth;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class SessionRepository
 * @package WebSK\Auth
 */
class SessionRepository extends EntityRepository
{

    /**
     * @param $session
     * @throws \Exception
     */
    public function deleteBySession($session)
    {
        $query = "DELETE FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " . Session::_SESSION . "=?";
        $this->db_service->query($query, [$session]);
    }

    /**
     * Удаляем просроченные сессии
     * @param int $user_id
     * @param int $delta
     * @throws \Exception
     */
    public function clearOldSessionsByUserId(int $user_id, int $delta)
    {
        $query = "DELETE FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " . Session::_USER_ID . "=? AND " . Session::_TIMESTAMP . "<=?";
        $this->db_service->query($query, [$user_id, $delta]);
    }

    /**
     * UserID авторизованного пользователя
     * @param string $auth_session
     * @return int|null
     */
    public function findCurrentUserId(string $auth_session): ?int
    {
        $query = "SELECT " . Session::_USER_ID
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " . Session::_SESSION . "=?";

        $user_id = $this->db_service->readField($query, [$auth_session]);

        return $user_id;
    }
}
