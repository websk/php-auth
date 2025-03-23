<?php

namespace WebSK\Auth\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\User\UserRoleService;
use WebSK\Auth\User\UserService;
use WebSK\Captcha\Captcha;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Router;
use WebSK\Auth\Auth;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRole;

/**
 * Class RegistrationHandler
 * @package WebSK\Auth\RequestHandlers
 */
class RegistrationHandler extends BaseHandler
{
    /** @Inject */
    protected UserRoleService $user_role_service;

    /** @Inject */
    protected UserService $user_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $destination = $request->getParam('destination', Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM));

        $name = trim($request->getParam('name', ''));
        $first_name = trim($request->getParam('first_name', ''));
        $last_name = trim($request->getParam('last_name', ''));
        $email = trim($request->getParam('email', ''));
        $new_password_first = $request->getParam('new_password_first', '');
        $new_password_second = $request->getParam('new_password_second', '');

        $error_destination = Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM);

        if (!$name) {
            Messages::setError("Не указано Имя на сайте");
            return $response->withHeader('Location', $error_destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        if (!$email) {
            Messages::setError("Не указан E-mail");
            return $response->withHeader('Location', $error_destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        if (!$request->getParam('captcha')) {
            return $response->withHeader('Location', $error_destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        if (!Captcha::checkWithMessage()) {
            return $response->withHeader('Location', $error_destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        if ($this->user_service->getUserIdByEmail($email)) {
            Messages::setError("Пользователь с таким адресом электронной почты " . $email . ' уже зарегистрирован');
            return $response->withHeader('Location', $error_destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        if (!$new_password_first && !$new_password_second) {
            Messages::setError('Ошибка! Не введен пароль.');
            return $response->withHeader('Location', $error_destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        if ($new_password_first || $new_password_second) {
            if ($new_password_first != $new_password_second) {
                Messages::setError('Ошибка! Пароль не подтвержден, либо подтвержден неверно.');
                return $response->withHeader('Location', $error_destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
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

        $confirm_code = $this->user_service->generateConfirmCode();
        $user_obj->setConfirmCode($confirm_code);

        try {
            $this->user_service->save($user_obj);
        } catch (\Exception $e) {
            Messages::setError($e->getMessage());
            return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        // Roles
        $role_id = AuthConfig::getDefaultRoleId();

        if ($role_id) {
            $user_role_obj = new UserRole();
            $user_role_obj->setUserId($user_obj->getId());
            $user_role_obj->setRoleId($role_id);
            $this->user_role_service->save($user_role_obj);
        }

        Auth::sendConfirmMail($name, $email, $confirm_code);

        $message = 'Вы успешно зарегистрированы на сайте. ';
        $message .= 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        Messages::setMessage($message);

        return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
    }
}
