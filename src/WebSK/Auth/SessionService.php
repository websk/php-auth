<?php

namespace WebSK\Auth;

use WebSK\Entity\EntityService;

/**
 * Class SessionService
 * @package WebSK\Auth
 */
class SessionService extends EntityService
{
    /** @var SessionRepository */
    protected $repository;

    /**
     * @param $user_id
     * @throws \Exception
     */
    public function clearUserSession($user_id)
    {
        $this->repository->deleteBySession($_COOKIE['auth_session']);

        $this->clearOldSessionsByUserId($user_id);

        $this->clearAuthCookie();
    }

    /**
     * Удаляем просроченные сессии
     * @param $user_id
     * @throws \Exception
     */
    protected function clearOldSessionsByUserId($user_id)
    {
        $delta = time() - Session::SESSION_LIFE_TIME;
        $this->repository->clearOldSessionsByUserId($user_id, $delta);
    }

    public function clearAuthCookie()
    {
        setcookie('auth_session', '', time() - 3600, '/');
    }
}
