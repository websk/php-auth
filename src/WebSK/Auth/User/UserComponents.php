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
     * @param string $save_handler_url
     * @return string
     */
    public static function renderEditForm(User $user_obj, string $save_handler_url)
    {
        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK' . DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR . 'User',
            'user_form_edit.tpl.php',
            [
                'user_obj' => $user_obj,
                'save_handler_url' => $save_handler_url
            ]
        );

        return $content;
    }
}
