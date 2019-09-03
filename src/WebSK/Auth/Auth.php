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
