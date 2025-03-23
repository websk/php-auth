<?php

namespace WebSK\Auth;

use WebSK\Auth\User\User;
use WebSK\Auth\User\UserService;
use WebSK\Cache\CacheService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\EntityService;

/**
 * Class SessionService
 * @package WebSK\Auth
 */
class SessionService extends EntityService
{
    /** @var SessionRepository */
    protected $repository;

    protected UserService $user_service;

    /**
     * SessionService constructor.
     * @param string $entity_class_name
     * @param EntityRepository $repository
     * @param CacheService $cache_service
     * @param UserService $user_service
     */
    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        UserService $user_service
    )
    {
        $this->user_service = $user_service;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param int $user_id
     * @throws \Exception
     */
    public function clearUserSession(int $user_id): void
    {
        $this->repository->deleteBySession($_COOKIE[Session::AUTH_COOKIE_NAME]);

        $this->clearOldSessionsByUserId($user_id);

        $this->clearAuthCookie();
    }

    /**
     * Удаляем просроченные сессии
     * @param int $user_id
     * @throws \Exception
     */
    protected function clearOldSessionsByUserId(int $user_id): void
    {
        $delta = time() - Session::SESSION_LIFE_TIME;
        $this->repository->clearOldSessionsByUserId($user_id, $delta);
    }

    public function clearAuthCookie(): void
    {
        setcookie(Session::AUTH_COOKIE_NAME, '', time() - 3600, '/');
    }

    /**
     * @param int $user_id
     * @param string $session_hash
     * @param int $delta
     * @throws \Exception
     */
    public function storeUserSession(int $user_id, string $session_hash, int $delta): void
    {
        $time = time();

        $session = new Session();

        $session->setUserId($user_id);
        $session->setSession($session_hash);
        $session->setHostname($_SERVER['REMOTE_ADDR']);
        $session->setTimestamp($time);
        $this->save($session);

        setcookie(Session::AUTH_COOKIE_NAME, $session_hash, $delta, '/');

        $this->clearOldSessionsByUserId($user_id);
    }

    /**
     * UserID авторизованного пользователя
     * @return ?int
     */
    public function getCurrentUserId(): ?int
    {
        static $user_session_unique_id;

        if (isset($user_session_unique_id)) {
            return $user_session_unique_id;
        }

        if (!array_key_exists(Session::AUTH_COOKIE_NAME, $_COOKIE)) {
            return null;
        }

        $auth_session = $_COOKIE[Session::AUTH_COOKIE_NAME];

        $user_session_unique_id = $this->repository->findCurrentUserId($auth_session);

        return $user_session_unique_id;
    }

    /**
     * @return ?User
     */
    public function getCurrentUserObj(): ?User
    {
        $user_id = $this->getCurrentUserId();
        if (!$user_id) {
            return null;
        }

        return $this->user_service->getById($user_id, false);
    }

    /**
     * @return bool
     */
    public function currentUserIsAdmin(): bool
    {
        $user_obj = $this->getCurrentUserObj();
        if (!$user_obj) {
            return false;
        }

        $user_id = $user_obj->getId();
        if ($this->user_service->hasRoleAdminByUserId($user_id)) {
            return true;
        }

        return false;
    }

    /**
     * Есть ли у пользователя роль, по обозначению роли
     * @param string $role_designation
     * @return bool
     */
    public function currentUserHasAccessByRoleDesignation(string $role_designation): bool
    {
        $user_id = $this->getCurrentUserId();
        if (!$user_id) {
            return false;
        }

        if (!$this->user_service->hasRoleByUserIdAndDesignation($user_id, $role_designation)) {
            return false;
        }

        return true;
    }

    /**
     * Есть ли у пользователя хоть одна роль, по обозначению ролей
     * @param array $role_designations_arr
     * @return bool
     */
    public function currentUserHasAccessByAnyRoleDesignations(array $role_designations_arr): bool
    {
        $user_id = $this->getCurrentUserId();

        if (!$user_id) {
            return false;
        }

        foreach ($role_designations_arr as $role_designation) {
            if ($this->currentUserHasAccessByRoleDesignation($role_designation)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Авторизация на сайте
     * @param string $email
     * @param string $password
     * @param bool $save_auth
     * @param ?string $message
     * @return bool
     */
    public function processAuthorization(string $email, string $password, bool $save_auth = false, ?string &$message = null): bool
    {
        $user_id = $this->user_service->getUserIdByEmailAndPassword($email, $password);

        if (!$user_id) {
            $message = 'Неверный адрес электронной почты или пароль.';
            return false;
        }

        $user_obj = $this->user_service->getById($user_id);

        // Регистрация не подтверждена
        if (!$user_obj->isConfirm()) {
            $message = 'Сначала подтвердите Ваш адрес электронной почты. Ранее Вам была отправлена ссылка для подтверждения.';
            return false;
        }

        $delta = null;
        if ($save_auth) {
            $delta = time() + Session::SESSION_LIFE_TIME;
        }

        $session = sha1(time() . $user_id);

        $this->storeUserSession($user_id, $session, $delta);

        return true;
    }
}
