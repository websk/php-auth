<?php

namespace WebSK\Auth;

use WebSK\Config\ConfWrapper;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserService;
use WebSK\DB\DBWrapper;
use WebSK\Slim\Router;
use WebSK\Utils\Filters;

/**
 * Class AuthService
 * @package WebSK\Auth\User
 */
class AuthService
{
    /** @var UserService */
    protected $user_service;

    /** @var SessionService */
    protected $session_service;

    /**
     * AuthService constructor.
     * @param UserService $user_service
     * @param SessionService $session_service
     */
    public function __construct(
        UserService $user_service,
        SessionService $session_service
    )
    {
        $this->user_service = $user_service;
        $this->session_service = $session_service;
    }

    /**
     * Авторизация на сайте
     * @param string $email
     * @param string $password
     * @param bool $save_auth
     * @return bool
     */
    public function processAuthorization(string $email, string $password, bool $save_auth = false)
    {
        $salt_password = Auth::getHash($password);

        $query = "SELECT id FROM " . User::DB_TABLE_NAME . " WHERE email=? AND passw=? LIMIT 1";
        $user_id = DBWrapper::readField($query, [$email, $salt_password]);

        if (!$user_id) {
            return false;
        }

        $user_obj = $this->user_service->getById($user_id);

        // Регистрация не подтверждена
        if (!$user_obj->isConfirm()) {
            return false;
        }

        $delta = null;
        if ($save_auth) {
            $delta = time() + Session::SESSION_LIFE_TIME;
        }

        $session = sha1(time() . $user_id);

        $this->session_service->storeUserSession($user_id, $session, $delta);

        return true;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $confirm_code
     * @throws \phpmailerException
     */
    public function sendConfirmMail(string $name, string $email, string $confirm_code)
    {
        $site_email = ConfWrapper::value('site_email');
        $site_domain = ConfWrapper::value('site_domain');
        $site_name = ConfWrapper::value('site_name');

        $confirm_url = $site_domain . Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_CONFIRM_REGISTRATION, ['confirm_code' => $confirm_code]);

        $mail_message = 'Здравствуйте, ' . $name . '!<br />';
        $mail_message .= '<p>На сайте ' .  $site_domain . ' была создана регистрационная запись, в которой был указал ваш электронный адрес (e-mail).</p>';
        $mail_message .= '<p>Если вы не регистрировались на данном сайте, просто проигнорируйте это сообщение! Аккаунт будет автоматически удален через некоторое время.</p>';
        $mail_message .= '<p>Если это были вы, то для завершения процедуры регистрации, пожалуйста перейдите по ссылке <a href="' . $confirm_url .  '">' . $confirm_url .  '</a></p>';

        $mail_message .= '<p>С уважением, администрация сайта ' . $site_name . ', ' . $site_domain . '</p>';

        $subject = 'Подтверждение регистрации на сайте ' . $site_name;

        $mail = new \PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($site_email, $site_name);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $mail_message;
        $mail->AltBody = Filters::checkPlain($mail_message);
        $mail->send();
    }

    /**
     * @return User
     * @throws \Exception
     */
    public function getCurrentUserObj()
    {
        $user_id = Auth::getCurrentUserId();

        return $this->user_service->getById($user_id, false);
    }
}
