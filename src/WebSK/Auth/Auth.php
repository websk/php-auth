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
 * @method static bool currentUserHasAccessByAnyRoleDesignations(array $role_designations_arr)
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

    /**
     * Хеш пароля
     * @param $password
     * @return string
     */
    public static function getHash(string $password): string
    {
        $salt = AuthConfig::getSalt();

        $hash = md5($salt . $password);

        return $hash;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $confirm_code
     */
    public static function sendConfirmMail(string $name, string $email, string $confirm_code): void
    {
        $site_email = ConfWrapper::value('site_email');
        $site_domain = ConfWrapper::value('site_domain');
        $site_name = ConfWrapper::value('site_name');

        $confirm_url = $site_domain . Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_CONFIRM_REGISTRATION, ['confirm_code' => $confirm_code]);

        $mail_message = 'Здравствуйте, ' . $name . '!<br />';
        $mail_message .= '<p>На сайте ' .  $site_domain . ' была создана регистрационная запись, в которой был указал ваш электронный адрес (e-mail).</p>';
        $mail_message .= '<p>Если вы не регистрировались на данном сайте, просто проигнорируйте это сообщение! Аккаунт будет автоматически удален через некоторое время.</p>';
        $mail_message .= '<p>Если это были вы, то для завершения процедуры регистрации, пожалуйста перейдите по ссылке <a href="' . $confirm_url .  '">' . $confirm_url .  '</a></p>';

        $mail_message .= '<p>С уважением, администрация сайта ' . $site_name . ', ' . $site_domain . '</p>';

        $subject = 'Подтверждение регистрации на сайте ' . $site_name;

        $mail = new PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($site_email, $site_name);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $mail_message;
        $mail->AltBody = Sanitize::sanitizeTagContent($mail_message);
        $mail->send();
    }
}
