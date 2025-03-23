<?php

namespace WebSK\Auth\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\User\UserService;
use WebSK\Captcha\Captcha;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class SendConfirmCodeHandler
 * @package WebSK\Auth\RequestHandlers
 */
class SendConfirmCodeHandler extends BaseHandler
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

        $destination = $this->urlFor(AuthRoutes::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM);

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

        $user_obj = $this->user_service->getById($user_id);

        if ($user_obj->isConfirm()) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты уже зарегистрирован.');
            return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        $confirm_code = $this->user_service->generateConfirmCode();

        Auth::sendConfirmMail($user_obj->getName(), $email, $confirm_code);

        $message = 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        Messages::setMessage($message);

        return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
    }
}
