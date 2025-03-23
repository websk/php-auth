<?php

namespace WebSK\Auth;

use Psr\Container\ContainerInterface;
use WebSK\Auth\User\UserService;
use WebSK\Cache\CacheServiceProvider;
use WebSK\DB\DBConnectorMySQL;
use WebSK\DB\DBService;
use WebSK\DB\DBSettings;

/**
 * Class AuthServiceProvider
 * @package WebSK\Auth
 */
class AuthServiceProvider
{
    const string DUMP_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dumps' . DIRECTORY_SEPARATOR . 'db_auth.sql';
    const string DB_SERVICE_CONTAINER_ID = 'auth.db_service';
    const string DB_ID = 'db_auth';

    const string SETTINGS_CONTAINER_ID = 'settings';
    const string PARAM_DB = 'db';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container): void
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container->set(self::DB_SERVICE_CONTAINER_ID, function (ContainerInterface $container) {
            $db_config = $container->get(
                self::SETTINGS_CONTAINER_ID . '.' . self::PARAM_DB . '.' . self::DB_ID
            );

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
        });

        /**
         * @param ContainerInterface $container
         * @return SessionService
         */
        $container->set(SessionService::class, function (ContainerInterface $container) {
            return new SessionService(
                Session::class,
                $container->get(SessionRepository::class),
                $container->get(CacheServiceProvider::SERVICE_CONTAINER_ID),
                $container->get(UserService::class)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return SessionRepository
         */
        $container->set(SessionRepository::class, function (ContainerInterface $container) {
            return new SessionRepository(
                Session::class,
                $container->get(self::DB_SERVICE_CONTAINER_ID)
            );
        });
    }
}
