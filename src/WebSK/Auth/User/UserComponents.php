<?php

namespace WebSK\Auth\User;

use WebSK\Views\PhpRender;

/**
 * Class UsersComponents
 * @package WebSK\Auth\User
 */
class UserComponents
{

    /**
     * @param User $user_obj
     * @param string $redirect_destination_url
     * @return string
     */
    public static function renderPasswordForm(User $user_obj, string $redirect_destination_url = ''): string
    {
        return PhpRender::renderTemplateForModuleNamespace(
            'WebSK' . DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR . 'User',
            'change_password_form.tpl.php',
            [
                'user_obj' => $user_obj,
                'redirect_destination_url' => $redirect_destination_url
            ]
        );
    }
}
