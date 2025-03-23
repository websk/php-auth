<?php

namespace WebSK\Auth\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\User\UserService;
use WebSK\Captcha\Captcha;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class ForgotPasswordHandler
 * @package WebSK\Auth\RequestHandlers
 */
class ForgotPasswordHandler extends BaseHandler
{
    /** @Inject  */
    protected UserService $user_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $email = $request->getParam('email', '');

        $destination = $request->getParam('destination', $this->urlFor(AuthRoutes::ROUTE_NAME_AUTH_FORGOT_PASSWORD_FORM));

        if (!$request->getParam('captcha')) {
            return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        if (!Captcha::checkWithMessage()) {
            return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        if (empty($email)) {
            Messages::setError('Ошибка! Не указан адрес электронной почты (Email).');
            return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        if (!$this->user_service->hasUserByEmail($email)) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты не зарегистрирован на сайте.');
            return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        $user_id = $this->user_service->getUserIdByEmail($email);

        $this->user_service->createAndSendPasswordToUser($user_id);

        $message = 'Временный пароль отправлен на указанный вами адрес электронной почты.';

        Messages::setMessage($message);

        return $response->withHeader('Location', $this->urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM))
            ->withStatus(StatusCodeInterface::STATUS_FOUND);
    }
}
