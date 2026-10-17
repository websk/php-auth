<?php

namespace WebSK\Auth\User;

use Psr\Container\ContainerInterface;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Cache\CacheService;
use WebSK\DB\DBService;

/**
 * Class UsersServiceProvider
 * @package WebSK\Auth\User
 */
class UserServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    /** @param ContainerInterface&\ArrayAccess<string, mixed> $container */
    public static function register(ContainerInterface&\ArrayAccess $container): void
    {
        /**
         * @param ContainerInterface $container
         * @return RoleService
         */
        $container[RoleService::class] = function (ContainerInterface $container): RoleService {
            return new RoleService(
                Role::class,
                self::service($container, RoleRepository::class, RoleRepository::class),
                self::service($container, CacheServiceProvider::SERVICE_CONTAINER_ID, CacheService::class)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return RoleRepository
         */
        $container[RoleRepository::class] = function (ContainerInterface $container): RoleRepository {
            return new RoleRepository(
                Role::class,
                self::service($container, AuthServiceProvider::DB_SERVICE_CONTAINER_ID, DBService::class)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return UserRoleService
         */
        $container[UserRoleService::class] = function (ContainerInterface $container): UserRoleService {
            return new UserRoleService(
                UserRole::class,
                self::service($container, UserRoleRepository::class, UserRoleRepository::class),
                self::service($container, CacheServiceProvider::SERVICE_CONTAINER_ID, CacheService::class)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return UserRoleRepository
         */
        $container[UserRoleRepository::class] = function (ContainerInterface $container): UserRoleRepository {
            return new UserRoleRepository(
                UserRole::class,
                self::service($container, AuthServiceProvider::DB_SERVICE_CONTAINER_ID, DBService::class)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return UserService
         */
        $container[UserService::class] = function (ContainerInterface $container): UserService {
            return new UserService(
                User::class,
                self::service($container, UserRepository::class, UserRepository::class),
                self::service($container, CacheServiceProvider::SERVICE_CONTAINER_ID, CacheService::class),
                self::service($container, RoleService::class, RoleService::class),
                self::service($container, UserRoleService::class, UserRoleService::class)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return UserRepository
         */
        $container[UserRepository::class] = function (ContainerInterface $container): UserRepository {
            return new UserRepository(
                User::class,
                self::service($container, AuthServiceProvider::DB_SERVICE_CONTAINER_ID, DBService::class)
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
