<?php

namespace WebSK\Auth;

use WebSK\Config\ConfWrapper;

/**
 * Class AuthConfig
 * @package WebSK\Auth
 */
class AuthConfig
{

    const string USER_PHOTO_STORAGE = 'files';
    const string USER_PHOTO_DIR = 'images' . DIRECTORY_SEPARATOR . 'users';
    const string USER_PHOTO_FILES_DIR = DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR .  self::USER_PHOTO_DIR;

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
    public static function getAdminLayout(): string
    {
        return ConfWrapper::value('auth.layout_admin', ConfWrapper::value('layout.admin'));
    }

    /**
     * @return string
     */
    public static function getAdminMainPageUrl(): string
    {
        return ConfWrapper::value('auth.admin_main_page_url', '/admin');
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
