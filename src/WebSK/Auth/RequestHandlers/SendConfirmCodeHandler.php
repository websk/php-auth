<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Auth;
use WebSK\Auth\AuthRoutes;
use WebSK\Captcha\Captcha;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\UserServiceProvider;

/**
 * Class SendConfirmCodeHandler
 * @package WebSK\Auth\RequestHandlers
 */
class SendConfirmCodeHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $email = $request->getParam('email', '');

        $destination = $this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM);

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

        $user_obj = $user_service->getById($user_id);

        if ($user_obj->isConfirm()) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты уже зарегистрирован.');
            return $response->withRedirect($destination);
        }

        $confirm_code = $user_service->generateConfirmCode();

        Auth::sendConfirmMail($user_obj->getName(), $email, $confirm_code);

        $message = 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        Messages::setMessage($message);

        return $response->withRedirect($destination);
    }
}
