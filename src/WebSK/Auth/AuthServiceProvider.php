<?php

namespace WebSK\Auth;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\DB\DBConnectorMySQL;
use WebSK\DB\DBService;
use WebSK\DB\DBSettings;
use WebSK\Auth\User\UserServiceProvider;

/**
 * Class AuthServiceProvider
 * @package WebSK\Auth
 */
class AuthServiceProvider
{
    const DUMP_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dumps' . DIRECTORY_SEPARATOR . 'db_auth.sql';
    const AUTH_SERVICE_CONTAINER_ID = 'auth_service_container_id';
    const DB_SERVICE_CONTAINER_ID = 'auth.db_service';
    const DB_ID = 'db_auth';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container[self::DB_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            $db_config = $container['settings']['db'][self::DB_ID];

            $db_connector = new DBConnectorMySQL(
                $db_config['host'],
                $db_config['db_name'],
                $db_config['user'],
                $db_config['password']
            );

            $db_settings = new DBSettings(
                'mysql'
            );

            return new DBService($db_connector, $db_settings);
        };

        /**
         * @param ContainerInterface $container
         * @return SessionService
         */
        $container[Session::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new SessionService(
                Session::class,
                $container->get(Session::ENTITY_REPOSITORY_CONTAINER_ID),
                CacheServiceProvider::getCacheService($container),
                UserServiceProvider::getUserService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return SessionRepository
         */
        $container[Session::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new SessionRepository(
                Session::class,
                $container->get(self::DB_SERVICE_CONTAINER_ID)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return SessionService
     */
    public static function getSessionService(ContainerInterface $container)
    {
        return $container->get(Session::ENTITY_SERVICE_CONTAINER_ID);
    }

    /**
     * @param ContainerInterface $container
     * @return DBService
     */
    public static function getDBService(ContainerInterface $container)
    {
        return $container->get(self::DB_SERVICE_CONTAINER_ID);
    }
}
