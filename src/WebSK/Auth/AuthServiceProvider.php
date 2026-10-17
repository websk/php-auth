<?php

namespace WebSK\Auth;

use Psr\Container\ContainerInterface;
use WebSK\Auth\User\UserService;
use WebSK\Cache\CacheService;
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
    /** @param ContainerInterface&\ArrayAccess<string, mixed> $container */
    public static function register(ContainerInterface&\ArrayAccess $container): void
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container[self::DB_SERVICE_CONTAINER_ID] = function (ContainerInterface $container): DBService {
            $db_config = $container->get(
                self::SETTINGS_CONTAINER_ID . '.' . self::PARAM_DB . '.' . self::DB_ID
            );
            if (!is_array($db_config)) {
                throw new \UnexpectedValueException('Auth database configuration must be an array.');
            }
            foreach (['host', 'db_name', 'user', 'password'] as $key) {
                if (!isset($db_config[$key]) || !is_string($db_config[$key])) {
                    throw new \UnexpectedValueException('Auth database setting "' . $key . '" must be a string.');
                }
            }

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
        $container[SessionService::class] = function (ContainerInterface $container): SessionService {
            return new SessionService(
                Session::class,
                self::service($container, SessionRepository::class, SessionRepository::class),
                self::service($container, CacheServiceProvider::SERVICE_CONTAINER_ID, CacheService::class),
                self::service($container, UserService::class, UserService::class)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return SessionRepository
         */
        $container[SessionRepository::class] = function (ContainerInterface $container): SessionRepository {
            return new SessionRepository(
                Session::class,
                self::service($container, self::DB_SERVICE_CONTAINER_ID, DBService::class)
            );
        };
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    private static function service(ContainerInterface $container, string $id, string $class): object
    {
        $service = $container->get($id);
        if (!$service instanceof $class) {
            throw new \UnexpectedValueException('Container service "' . $id . '" must be an instance of ' . $class . '.');
        }

        return $service;
    }
}
