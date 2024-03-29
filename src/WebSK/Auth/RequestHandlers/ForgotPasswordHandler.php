<?php

namespace WebSK\Auth\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Captcha\Captcha;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class ForgotPasswordHandler
 * @package WebSK\Auth\RequestHandlers
 */
class ForgotPasswordHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $email = $request->getParam('email', '');

        $destination = $request->getParam('destination', $this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_FORGOT_PASSWORD_FORM));

        if (!$request->getParam('captcha')) {
            return $response->withRedirect($destination);
        }

        if (!Captcha::checkWithMessage()) {
            return $response->withRedirect($destination);
        }

        if (empty($email)) {
            Messages::setError('Ошибка! Не указан адрес электронной почты (Email).');
            return $response->withRedirect($destination);
        }

        $user_service = UserServiceProvider::getUserService($this->container);

        if (!$user_service->hasUserByEmail($email)) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты не зарегистрирован на сайте.');
            return $response->withRedirect($destination);
        }

        $user_id = $user_service->getUserIdByEmail($email);

        $user_service->createAndSendPasswordToUser($user_id);

        $message = 'Временный пароль отправлен на указанный вами адрес электронной почты.';

        Messages::setMessage($message);

        return $response->withRedirect($this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM));
    }
}
