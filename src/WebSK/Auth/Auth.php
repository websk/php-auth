<?php

namespace WebSK\Auth;

use WebSK\Config\ConfWrapper;
use WebSK\Slim\Facade;

/**
 * Class Auth
 * @see LoggerEntryService
 * @method static getCurrentUserId()
 * @method static getCurrentUserObj()
 * @method static currentUserIsAdmin()
 * @method static currentUserHasAccessByRoleDesignation(string $role_designation)
 * @package WebSK\Auth\Users
 */
class Auth extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Session::ENTITY_SERVICE_CONTAINER_ID;
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
}
