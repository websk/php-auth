<?php

namespace WebSK\Auth;

use WebSK\Entity\EntityService;

/**
 * Class SessionsService
 * @package WebSK\Auth
 */
class SessionsService extends EntityService
{
    /** @var SessionsRepository */
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
        $delta = time() - Sessions::SESSION_LIFE_TIME;
        $this->repository->clearOldSessionsByUserId($user_id, $delta);
    }

    public function clearAuthCookie()
    {
        setcookie('auth_session', '', time() - 3600, '/');
    }
}
