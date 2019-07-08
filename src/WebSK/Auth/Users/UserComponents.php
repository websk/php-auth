<?php

namespace WebSK\Auth\Users;

use Websk\Slim\Container;
use WebSK\Views\PhpRender;

/**
 * Class UserComponents
 * @package WebSK\Auth\Users
 */
class UserComponents
{
    /**
     * @param User $user_obj
     * @param array $user_roles_ids_arr
     * @param string $save_handler_url
     * @return string
     */
    public static function renderEditForm(User $user_obj, array $user_roles_ids_arr, string $save_handler_url)
    {
        $container = Container::self();
        $role_service = UsersServiceProvider::getRoleService($container);

        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK' . DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR . 'Users',
            'user_form_edit.tpl.php',
            [
                'user_obj' => $user_obj,
                'role_objs_arr' => $role_service->getAllRoles(),
                'user_roles_ids_arr' => $user_roles_ids_arr,
                'save_handler_url' => $save_handler_url
            ]
        );

        return $content;
    }
}
