<?php

namespace WebSK\Auth\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\User\UserService;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class ConfirmRegistrationHandler
 * @package WebSK\Auth\RequestHandlers
 */
class ConfirmRegistrationHandler extends BaseHandler
{
    /** @Inject  */
    protected UserService $user_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $confirm_code
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $confirm_code): ResponseInterface
    {
        $user_id = $this->user_service->getUserIdByConfirmCode($confirm_code);

        $destination = $this->urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM);

        if (!$user_id) {
            Messages::setError(
                'Ошибка! Неверный код подтверждения. <a href="' . $this->urlFor(AuthRoutes::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM) . '">Выслать код подтверждения повторно.</a>'
            );
            return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        $user_obj = $user_service->getById($user_id);
        $user_obj->setConfirm(true);
        $user_obj->setConfirmCode('');
        $user_service->save($user_obj);

        $message = 'Поздравляем! Процесс регистрации успешно завершен. Теперь вы можете войти на сайт.';

        Messages::setMessage($message);

        return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
    }
}
