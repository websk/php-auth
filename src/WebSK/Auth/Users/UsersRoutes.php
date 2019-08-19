<?php

namespace WebSK\Auth\Users;

use Slim\App;
use WebSK\Auth\Middleware\CurrentUserHasRightToEditUser;
use WebSK\Auth\Middleware\CurrentUserIsAdmin;
use WebSK\Auth\Users\RequestHandlers\Admin\RoleEditHandler;
use WebSK\Auth\Users\RequestHandlers\Admin\RoleListAjaxHandler;
use WebSK\Auth\Users\RequestHandlers\Admin\UserEditHandler as AdminUserEditHandler;
use WebSK\Auth\Users\RequestHandlers\Admin\UserListAjaxHandler;
use WebSK\Auth\Users\RequestHandlers\Admin\UserListHandler;
use WebSK\Auth\Users\RequestHandlers\Admin\RoleListHandler;
use WebSK\Auth\Users\RequestHandlers\UserAddPhotoHandler;
use WebSK\Auth\Users\RequestHandlers\UserCreatePasswordHandler;
use WebSK\Auth\Users\RequestHandlers\UserDeleteHandler;
use WebSK\Auth\Users\RequestHandlers\UserDeletePhotoHandler;
use WebSK\Auth\Users\RequestHandlers\UserEditHandler;
use WebSK\Auth\Users\RequestHandlers\UserSaveHandler;
use WebSK\Utils\HTTP;

/**
 * Class UsersRoutes
 * @package WebSK\Auth\Users
 */
class UsersRoutes
{
    const ROUTE_NAME_ADMIN_USER_CREATE = 'admin:users:create';
    const ROUTE_NAME_ADMIN_USER_EDIT = 'admin:users:edit';
    const ROUTE_NAME_ADMIN_USER_LIST = 'admin:users:list';
    const ROUTE_NAME_ADMIN_USER_LIST_AJAX = 'admin:users:list:ajax';

    const ROUTE_NAME_USER_CREATE = 'user:create';
    const ROUTE_NAME_USER_EDIT = 'user:edit';
    const ROUTE_NAME_USER_ADD = 'user:add';
    const ROUTE_NAME_USER_UPDATE = 'user:update';
    const ROUTE_NAME_USER_DELETE = 'user:delete';

    const ROUTE_NAME_USER_CREATE_PASSWORD = 'user:create_password';

    const ROUTE_NAME_USER_ADD_PHOTO = 'user:add_photo';
    const ROUTE_NAME_USER_DELETE_PHOTO = 'user:delete_photo';

    const ROUTE_NAME_ADMIN_ROLE_LIST = 'admin:users:role:list';
    const ROUTE_NAME_ADMIN_ROLE_EDIT = 'admin:users:role:edit';
    const ROUTE_NAME_ADMIN_ROLE_LIST_AJAX = 'admin:users:role:list:ajax';

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/users', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', UserListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_LIST);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST],'/ajax', UserListAjaxHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_LIST_AJAX);

            $app->get('/create', AdminUserEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_CREATE);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST],'/{user_id:\d+}', AdminUserEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_EDIT);

            $app->group('/roles', function (App $app) {
                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST],'', RoleListHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_LIST);

                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST],'ajax', RoleListAjaxHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_LIST_AJAX);

                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST],'/{role_id:\d+}', RoleEditHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_EDIT);
            });
        })->add(new CurrentUserIsAdmin());
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/user', function (App $app) {
            $app->get('/create', UserEditHandler::class)
                ->setName(self::ROUTE_NAME_USER_CREATE);

            $app->get('/{user_id:\d+}', UserEditHandler::class)
                ->setName(self::ROUTE_NAME_USER_EDIT);

            $app->post('/add', UserSaveHandler::class)
                ->setName(self::ROUTE_NAME_USER_ADD);

            $app->post('/update/{user_id:\d+}', UserSaveHandler::class)
                ->setName(self::ROUTE_NAME_USER_UPDATE);

            $app->get('/create_password/{user_id:\d+}', UserCreatePasswordHandler::class)
                ->setName(self::ROUTE_NAME_USER_CREATE_PASSWORD);

            $app->get('/add_photo/{user_id:\d+}', UserAddPhotoHandler::class)
                ->setName(self::ROUTE_NAME_USER_ADD_PHOTO);

            $app->get('/delete_photo/{user_id:\d+}', UserDeletePhotoHandler::class)
                ->setName(self::ROUTE_NAME_USER_DELETE_PHOTO);
        })->add(new CurrentUserHasRightToEditUser());
    }
}
