<?php

namespace WebSK\Auth;

use WebSK\Config\ConfWrapper;
use WebSK\Slim\Container;
use WebSK\DB\DBWrapper;
use WebSK\Auth\Users\User;
use WebSK\Auth\Users\UsersServiceProvider;

/**
 * Class AuthUtils
 * @package WebSK\Auth\Users
 */
class Auth
{
    const SESSION_LIFE_TIME = 31536000; // 1 год

    /**
     * @param int $user_id
     * @param string $session
     * @param int $delta
     * @throws \Exception
     */
    public static function storeUserSession($user_id, $session, $delta)
    {
        $time = time();

        $query = "INSERT INTO sessions SET user_id=?, session=?, hostname=?, timestamp=?";
        DBWrapper::query($query, [$user_id, $session, $_SERVER['REMOTE_ADDR'], $time]);

        setcookie('auth_session', $session, $delta, '/');

        self::clearOldSessionsByUserId($user_id);
    }

    /**
     * Удаляем просроченные сессии
     * @param $user_id
     * @throws \Exception
     */
    protected static function clearOldSessionsByUserId($user_id)
    {
        $delta = time() - self::SESSION_LIFE_TIME;
        $query = "DELETE FROM sessions WHERE user_id=? AND timestamp<=?";
        DBWrapper::query($query, array($user_id, $delta));
    }

    /**
     * Хеш пароля
     * @param $password
     * @return string
     */
    public static function getHash(string $password)
    {
        $salt = ConfWrapper::value('salt');

        $hash = md5($salt . $password);

        return $hash;
    }

    public static function clearUserSession($user_id)
    {
        $query = "DELETE FROM sessions WHERE session=?";
        DBWrapper::query($query, array($_COOKIE['auth_session']));

        self::clearOldSessionsByUserId($user_id);

        self::clearAuthCookie();
    }

    public static function clearAuthCookie()
    {
        setcookie('auth_session', '', time() - 3600, '/');
    }

    /**
     * UserID авторизованного пользователя
     * @return int|null
     */
    public static function getCurrentUserId()
    {
        static $user_session_unique_id;

        if (isset($user_session_unique_id)) {
            return $user_session_unique_id;
        }

        if (array_key_exists('auth_session', $_COOKIE)) {
            $query = "SELECT user_id FROM sessions WHERE session=?";
            $user_id = (int)DBWrapper::readField($query, array($_COOKIE['auth_session']));
            $user_session_unique_id = $user_id;

            return $user_id;
        }

        return null;
    }

    /**
     * @return bool|User
     */
    public static function getCurrentUserObj()
    {
        $user_id = self::getCurrentUserId();
        if (!$user_id) {
            return false;
        }

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        return $user_service->getById($user_id, false);
    }

    /**
     * @return bool
     */
    public static function currentUserIsAdmin()
    {
        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = self::getCurrentUserObj();
        if (!$user_obj) {
            return false;
        }

        $user_id = self::getCurrentUserId();
        if ($user_service->hasRoleAdminByUserId($user_id)) {
            return true;
        }

        return false;
    }

    /**
     * Есть ли у пользователя роль, по обозначению роли
     * @param $role_designation
     * @return bool
     */
    public static function currentUserHasAccessByRoleDesignation($role_designation)
    {
        $user_id = self::getCurrentUserId();

        if ($user_id) {
            $container = Container::self();

            $user_service = UsersServiceProvider::getUserService($container);

            if ($user_service->hasRoleByUserIdAndDesignation($user_id, $role_designation)) {
                return true;
            }
        }

        return false;
    }
}
