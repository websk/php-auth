<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\AuthRoutes;
use WebSK\Captcha\Captcha;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Router;
use WebSK\Auth\Auth;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRole;
use WebSK\Auth\User\UserServiceProvider;

/**
 * Class RegistrationHandler
 * @package WebSK\Auth\RequestHandlers
 */
class RegistrationHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response)
    {
        $destination = $request->getParam('destination', Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM));

        $name = trim($request->getParam('name', ''));
        $first_name = trim($request->getParam('first_name', ''));
        $last_name = trim($request->getParam('last_name', ''));
        $email = trim($request->getParam('email', ''));
        $new_password_first = $request->getParam('new_password_first', '');
        $new_password_second = $request->getParam('new_password_second', '');

        $error_destination = Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM);

        if ($name) {
            Messages::setError("Не указано Имя на сайте");
            return $response->withRedirect($destination);
        }

        if ($email) {
            Messages::setError("Не указан E-mail");
            return $response->withRedirect($destination);
        }

        if (!$request->getParam('captcha')) {
            return $response->withRedirect($error_destination);
        }

        if (!Captcha::checkWithMessage()) {
            return $response->withRedirect($error_destination);
        }

        $user_service = UserServiceProvider::getUserService($this->container);

        if ($user_service->getUserIdByEmail($email)) {
            Messages::setError("Пользователь с таким адресом электронной почты " . $email . ' уже зарегистрирован');
            return $response->withRedirect($destination);
        }

        if (!$new_password_first && !$new_password_second) {
            Messages::setError('Ошибка! Не введен пароль.');
            return $response->withRedirect($error_destination);
        }

        if ($new_password_first || $new_password_second) {
            if ($new_password_first != $new_password_second) {
                Messages::setError('Ошибка! Пароль не подтвержден, либо подтвержден неверно.');
                return $response->withRedirect($error_destination);
            }
        }

        $user_obj = new User();

        $user_obj->setName($name);
        if ($first_name) {
            $user_obj->setFirstName($first_name);
        }
        if ($last_name) {
            $user_obj->setLastName($last_name);
        }
        $user_obj->setEmail($email);
        $user_obj->setPassw(Auth::getHash($new_password_first));

        $confirm_code = $user_service->generateConfirmCode();
        $user_obj->setConfirmCode($confirm_code);

        try {
            $user_service->save($user_obj);
        } catch (\Exception $e) {
            Messages::setError($e->getMessage());
            return $response->withRedirect($destination);
        }

        // Roles
        $role_id = AuthConfig::getDefaultRoleId();

        if ($role_id) {
            $user_role_service = UserServiceProvider::getUserRoleService($this->container);

            $user_role_obj = new UserRole();
            $user_role_obj->setUserId($user_obj->getId());
            $user_role_obj->setRoleId($role_id);
            $user_role_service->save($user_role_obj);
        }

        Auth::sendConfirmMail($name, $email, $confirm_code);

        $message = 'Вы успешно зарегистрированы на сайте. ';
        $message .= 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        Messages::setMessage($message);

        return $response->withRedirect($destination);
    }
}
