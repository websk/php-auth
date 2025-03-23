<?php

namespace WebSK\Auth\User;

use Psr\Container\ContainerInterface;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Cache\CacheServiceProvider;

/**
 * Class UsersServiceProvider
 * @package WebSK\Auth\User
 */
class UserServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container): void
    {
        /**
         * @param ContainerInterface $container
         * @return RoleService
         */
        $container->set(RoleService::class, function (ContainerInterface $container) {
            return new RoleService(
                Role::class,
                $container->get(RoleRepository::class),
                $container->get(CacheServiceProvider::SERVICE_CONTAINER_ID)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return RoleRepository
         */
        $container->set(RoleRepository::class, function (ContainerInterface $container) {
            return new RoleRepository(
                Role::class,
                $container->get(AuthServiceProvider::DB_SERVICE_CONTAINER_ID)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return UserRoleService
         */
        $container->set(UserRoleService::class, function (ContainerInterface $container) {
            return new UserRoleService(
                UserRole::class,
                $container->get(UserRoleRepository::class),
                $container->get(CacheServiceProvider::SERVICE_CONTAINER_ID)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return UserRoleRepository
         */
        $container->set(UserRoleRepository::class, function (ContainerInterface $container) {
            return new UserRoleRepository(
                UserRole::class,
                $container->get(AuthServiceProvider::DB_SERVICE_CONTAINER_ID)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return UserService
         */
        $container->set(UserService::class, function (ContainerInterface $container) {
            return new UserService(
                User::class,
                $container->get(UserRepository::class),
                $container->get(CacheServiceProvider::SERVICE_CONTAINER_ID),
                $container->get(RoleService::class),
                $container->get(UserRoleService::class)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return UserRepository
         */
        $container->set(UserRepository::class, function (ContainerInterface $container) {
            return new UserRepository(
                User::class,
                $container->get(AuthServiceProvider::DB_SERVICE_CONTAINER_ID)
            );
        });
    }
}
