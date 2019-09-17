<?php

namespace WebSK\Auth;

use WebSK\Config\ConfWrapper;

/**
 * Class AuthConfig
 * @package WebSK\Auth
 */
class AuthConfig
{

    /**
     * @return string
     */
    public static function getMainLayout(): string
    {
        return ConfWrapper::value('auth.layout_main', ConfWrapper::value('layout.main'));
    }

    /**
     * @return string
     */
    public static function getMainPageUrl(): string
    {
        return ConfWrapper::value('auth.main_page_url', '/');
    }

    /**
     * @return string
     */
    public static function getSkifLayout(): string
    {
        return ConfWrapper::value('auth.layout_skif', ConfWrapper::value('skif.layout'));
    }

    /**
     * @return string
     */
    public static function getSkifMainPageUrl(): string
    {
        return ConfWrapper::value('auth.skif_main_page_url', '/admin');
    }

    /**
     * @return string
     */
    public static function getSalt(): string
    {
        return ConfWrapper::value('auth.salt', ConfWrapper::value('salt'));
    }

    /**
     * @return null|int
     */
    public static function getDefaultRoleId(): ?int
    {
        return ConfWrapper::value('auth.default_user_role_id', null);
    }
}
