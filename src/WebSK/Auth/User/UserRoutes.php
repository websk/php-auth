<?php

namespace WebSK\Auth\User;

use Slim\App;
use WebSK\Auth\Middleware\CurrentUserHasRightToEditUser;
use WebSK\Auth\Middleware\CurrentUserIsAdmin;
use WebSK\Auth\User\RequestHandlers\Admin\RoleEditHandler;
use WebSK\Auth\User\RequestHandlers\Admin\RoleListAjaxHandler;
use WebSK\Auth\User\RequestHandlers\Admin\UserEditHandler as AdminUserEditHandler;
use WebSK\Auth\User\RequestHandlers\Admin\UserListAjaxHandler;
use WebSK\Auth\User\RequestHandlers\Admin\UserListHandler;
use WebSK\Auth\User\RequestHandlers\Admin\RoleListHandler;
use WebSK\Auth\User\RequestHandlers\UserAddPhotoHandler;
use WebSK\Auth\User\RequestHandlers\UserChangePasswordHandler;
use WebSK\Auth\User\RequestHandlers\UserCreatePasswordHandler;
use WebSK\Auth\User\RequestHandlers\UserDeletePhotoHandler;
use WebSK\Auth\User\RequestHandlers\UserEditHandler;
use WebSK\Auth\User\RequestHandlers\UserSaveHandler;
use WebSK\Utils\HTTP;

/**
 * Class UserRoutes
 * @package WebSK\Auth\User
 */
class UserRoutes
{
    const ROUTE_NAME_ADMIN_USER_EDIT = 'admin:user:edit';
    const ROUTE_NAME_ADMIN_USER_LIST = 'admin:user:list';
    const ROUTE_NAME_ADMIN_USER_LIST_AJAX = 'admin:user:list:ajax';

    const ROUTE_NAME_USER_CREATE = 'user:create';
    const ROUTE_NAME_USER_EDIT = 'user:edit';
    const ROUTE_NAME_USER_ADD = 'user:add';
    const ROUTE_NAME_USER_UPDATE = 'user:update';
    const ROUTE_NAME_USER_DELETE = 'user:delete';

    const ROUTE_NAME_USER_CHANGE_PASSWORD = 'user:change_password';
    const ROUTE_NAME_USER_CREATE_PASSWORD = 'user:create_password';

    const ROUTE_NAME_USER_ADD_PHOTO = 'user:add_photo';
    const ROUTE_NAME_USER_DELETE_PHOTO = 'user:delete_photo';

    const ROUTE_NAME_ADMIN_ROLE_LIST = 'admin:user:role:list';
    const ROUTE_NAME_ADMIN_ROLE_EDIT = 'admin:user:role:edit';
    const ROUTE_NAME_ADMIN_ROLE_LIST_AJAX = 'admin:user:role:list:ajax';

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/user', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', UserListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_LIST);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST],'/ajax', UserListAjaxHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_LIST_AJAX);

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
            $app->get('/{user_id:\d+}', UserEditHandler::class)
                ->setName(self::ROUTE_NAME_USER_EDIT);

            $app->post('/update/{user_id:\d+}', UserSaveHandler::class)
                ->setName(self::ROUTE_NAME_USER_UPDATE);

            $app->get('/create_password/{user_id:\d+}', UserCreatePasswordHandler::class)
                ->setName(self::ROUTE_NAME_USER_CREATE_PASSWORD);

            $app->post('/change_password/{user_id:\d+}', UserChangePasswordHandler::class)
                ->setName(self::ROUTE_NAME_USER_CHANGE_PASSWORD);

            $app->post('/add_photo/{user_id:\d+}', UserAddPhotoHandler::class)
                ->setName(self::ROUTE_NAME_USER_ADD_PHOTO);

            $app->get('/delete_photo/{user_id:\d+}', UserDeletePhotoHandler::class)
                ->setName(self::ROUTE_NAME_USER_DELETE_PHOTO);
        })->add(new CurrentUserHasRightToEditUser());
    }
}
