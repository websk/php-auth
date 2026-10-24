<?php

namespace WebSK\Auth;

use PHPMailer\PHPMailer\PHPMailer;
use WebSK\Auth\User\User;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\Facade;
use WebSK\Slim\Router;
use WebSK\Utils\Sanitize;

/**
 * Class Auth
 * @see SessionService
 * @method static int|null getCurrentUserId()
 * @method static User|null getCurrentUserObj()
 * @method static bool currentUserIsAdmin()
 * @method static bool currentUserHasAccessByRoleDesignation(string $role_designation)
 * @method static bool currentUserHasAccessByAnyRoleDesignations(list<string> $role_designations_arr)
 * @package WebSK\Auth\User
 */
class Auth extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return SessionService::class;
    }

    /** @deprecated Use hashPassword() for new passwords. */
    public static function getHash(string $password): string
    {
        return self::hashPassword($password);
    }

    public static function hashPassword(string $password): string
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $hash;
    }

    public static function verifyPassword(string $password, string $stored_hash): bool
    {
        if (password_get_info($stored_hash)['algoName'] !== 'unknown') {
            return password_verify($password, $stored_hash);
        }

        return hash_equals(self::legacyPasswordHash($password), $stored_hash);
    }

    public static function passwordNeedsRehash(string $stored_hash): bool
    {
        return password_get_info($stored_hash)['algoName'] === 'unknown'
            || password_needs_rehash($stored_hash, PASSWORD_DEFAULT);
    }

    private static function legacyPasswordHash(string $password): string
    {
        $salt = AuthConfig::getSalt();

        return md5($salt . $password);
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $confirm_code
     */
    public static function sendConfirmMail(string $name, string $email, string $confirm_code): void
    {
        $site_email = self::configString('site_email');
        $site_domain = self::configString('site_domain');
        $site_name = self::configString('site_name');

        $confirm_url = $site_domain . Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_CONFIRM_REGISTRATION, ['confirm_code' => $confirm_code]);

        $mail_message = 'Здравствуйте, ' . Sanitize::sanitizeTagContent($name) . '!<br>';
        $mail_message .= '<p>На сайте ' . Sanitize::sanitizeTagContent($site_domain) . ' была создана регистрационная запись, в которой был указан ваш электронный адрес (e-mail).</p>';
        $mail_message .= '<p>Если вы не регистрировались на данном сайте, просто проигнорируйте это сообщение! Аккаунт будет автоматически удален через некоторое время.</p>';
        $mail_message .= '<p>Если это были вы, то для завершения процедуры регистрации, пожалуйста перейдите по ссылке <a href="'
            . Sanitize::sanitizeAttrValue($confirm_url) . '">' . Sanitize::sanitizeTagContent($confirm_url) . '</a></p>';

        $mail_message .= '<p>С уважением, администрация сайта ' . Sanitize::sanitizeTagContent($site_name)
            . ', ' . Sanitize::sanitizeTagContent($site_domain) . '</p>';

        $subject = 'Подтверждение регистрации на сайте ' . $site_name;

        $mail = new PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($site_email, $site_name);
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $mail_message;
        $mail->AltBody = Sanitize::sanitizeTagContent($mail_message);
        $mail->send();
    }

    private static function configString(string $key): string
    {
        $value = ConfWrapper::value($key);
        if (!is_string($value)) {
            throw new \UnexpectedValueException('Configuration value "' . $key . '" must be a string.');
        }

        return $value;
    }
}
