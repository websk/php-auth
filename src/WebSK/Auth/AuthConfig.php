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
        $value = ConfWrapper::value('auth.layout_main', null);

        return $value === null ? self::stringValue('layout.main') : self::ensureString('auth.layout_main', $value);
    }

    /**
     * @return string
     */
    public static function getMainPageUrl(): string
    {
        return self::stringValue('auth.main_page_url', '/');
    }

    /**
     * @return string
     */
    public static function getAdminLayout(): string
    {
        $value = ConfWrapper::value('auth.layout_admin', null);

        return $value === null ? self::stringValue('layout.admin') : self::ensureString('auth.layout_admin', $value);
    }

    /**
     * @return string
     */
    public static function getAdminMainPageUrl(): string
    {
        return self::stringValue('auth.admin_main_page_url', '/admin');
    }

    /**
     * @return string
     */
    public static function getSalt(): string
    {
        $value = ConfWrapper::value('auth.salt', null);

        return $value === null ? self::stringValue('salt') : self::ensureString('auth.salt', $value);
    }

    /**
     * @return null|int
     */
    public static function getDefaultRoleId(): ?int
    {
        $value = ConfWrapper::value('auth.default_user_role_id', null);
        if ($value !== null && !is_int($value)) {
            throw new \UnexpectedValueException('Default auth role ID must be an integer or null.');
        }

        return $value;
    }

    private static function stringValue(string $key, ?string $default = null): string
    {
        $value = ConfWrapper::value($key, $default);

        return self::ensureString($key, $value);
    }

    private static function ensureString(string $key, mixed $value): string
    {
        if (!is_string($value)) {
            throw new \UnexpectedValueException('Configuration value "' . $key . '" must be a string.');
        }

        return $value;
    }
}
