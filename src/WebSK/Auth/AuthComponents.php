<?php

namespace WebSK\Auth;

use WebSK\Views\PhpRender;

/**
 * Class AuthComponents
 * @package WebSK\Auth
 */
class AuthComponents
{
    /**
     * @param string $destination
     * @return string
     */
    public static function renderLoginForm(string $destination): string
    {
        return PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Auth',
            'login_form_block.tpl.php',
            ['destination' => $destination]
        );
    }
}
