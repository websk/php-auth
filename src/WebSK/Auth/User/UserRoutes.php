<?php

namespace WebSK\Auth\User;

use Fig\Http\Message\RequestMethodInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\Auth\Middleware\CurrentUserHasRightToEditUser;
use WebSK\Auth\Middleware\CurrentUserIsAdmin;
use WebSK\Auth\User\RequestHandlers\Admin\RoleEditHandler;
use WebSK\Auth\User\RequestHandlers\Admin\RoleListAjaxHandler;
use WebSK\Auth\User\RequestHandlers\Admin\UserEditHandler as AdminUserEditHandler;
use WebSK\Auth\User\RequestHandlers\Admin\UserListAjaxHandler;
use WebSK\Auth\User\RequestHandlers\Admin\UserListHandler;
use WebSK\Auth\User\RequestHandlers\Admin\RoleListHandler;
use WebSK\Auth\User\RequestHandlers\UserChangePasswordHandler;
use WebSK\Auth\User\RequestHandlers\UserCreatePasswordHandler;
use WebSK\Auth\User\RequestHandlers\UserEditHandler;

/**
 * Class UserRoutes
 * @package WebSK\Auth\User
 */
class UserRoutes
{
    const string ROUTE_NAME_ADMIN_USER_EDIT = 'admin:user:edit';
    const string ROUTE_NAME_ADMIN_USER_LIST = 'admin:user:list';
    const string ROUTE_NAME_ADMIN_USER_LIST_AJAX = 'admin:user:list:ajax';

    const string ROUTE_NAME_USER_EDIT = 'user:edit';

    const string ROUTE_NAME_USER_CHANGE_PASSWORD = 'user:change_password';
    const string ROUTE_NAME_USER_CREATE_PASSWORD = 'user:create_password';

    const string ROUTE_NAME_ADMIN_ROLE_LIST = 'admin:user:role:list';
    const string ROUTE_NAME_ADMIN_ROLE_EDIT = 'admin:user:role:edit';
    const string ROUTE_NAME_ADMIN_ROLE_LIST_AJAX = 'admin:user:role:list:ajax';

    /**
     * @param RouteCollectorProxyInterface $route_collector_proxy
     */
    public static function registerAdmin(RouteCollectorProxyInterface $route_collector_proxy): void
    {
        $route_collector_proxy->group('/user', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', UserListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_LIST);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],'/ajax', UserListAjaxHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_LIST_AJAX);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],'/{user_id:\d+}', AdminUserEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_EDIT);

            $route_collector_proxy->group('/roles', function (RouteCollectorProxyInterface $route_collector_proxy) {
                $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],'', RoleListHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_LIST);

                $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],'ajax', RoleListAjaxHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_LIST_AJAX);

                $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],'/{role_id:\d+}', RoleEditHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_EDIT);
            });
        })->add(new CurrentUserIsAdmin());
    }

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->group('/user', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],'/{user_id:\d+}', UserEditHandler::class)
                ->setName(self::ROUTE_NAME_USER_EDIT);

            $route_collector_proxy->get('/create_password/{user_id:\d+}', UserCreatePasswordHandler::class)
                ->setName(self::ROUTE_NAME_USER_CREATE_PASSWORD);

            $route_collector_proxy->post('/change_password/{user_id:\d+}', UserChangePasswordHandler::class)
                ->setName(self::ROUTE_NAME_USER_CHANGE_PASSWORD);
        })->add(new CurrentUserHasRightToEditUser());
    }
}
