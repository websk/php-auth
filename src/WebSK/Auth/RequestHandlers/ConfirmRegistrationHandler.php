<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\AuthRoutes;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\UserServiceProvider;

/**
 * Class ConfirmRegistrationHandler
 * @package WebSK\Auth\RequestHandlers
 */
class ConfirmRegistrationHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param string $confirm_code
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, string $confirm_code)
    {
        $user_service = UserServiceProvider::getUserService($this->container);

        $user_id = $user_service->getUserIdByConfirmCode($confirm_code);

        $destination = $this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM);

        if (!$user_id) {
            Messages::setError(
                'Ошибка! Неверный код подтверждения. <a href="' . $this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM) . '">Выслать код подтверждения повторно.</a>'
            );
            return $response->withRedirect($destination);
        }

        $user_obj = $user_service->getById($user_id);
        $user_obj->setConfirm(true);
        $user_obj->setConfirmCode('');
        $user_service->save($user_obj);

        $message = 'Поздравляем! Процесс регистрации успешно завершен. Теперь вы можете войти на сайт.';

        Messages::setMessage($message);

        return $response->withRedirect($destination);
    }
}
