<?php

namespace WebSK\Auth\User\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\Auth;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

/**
 * Class UserChangePasswordHandler
 * @package WebSK\Auth\User\RequestHandlers
 */
class UserChangePasswordHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $user_id
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, int $user_id)
    {
        $user_service = UserServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);
        if (!$user_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        if (($user_id != Auth::getCurrentUserId()) && !Auth::currentUserIsAdmin()) {
            return $response->withStatus(StatusCode::HTTP_FORBIDDEN);
        }

        $destination = $request->getParam('destination', $this->pathFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]));

        $new_password_first = $request->getParam('new_password_first', '');
        $new_password_second = $request->getParam('new_password_second', '');

        if (!$new_password_first || !$new_password_second) {
            Messages::setError('Ошибка! Не заполнен пароль или подтверждение пароля');
            return $response->withRedirect($destination);
        }

        if ($new_password_first != $new_password_second) {
            Messages::setError('Ошибка! Пароль не подтвержден, либо подтвержден неверно.');
            return $response->withRedirect($destination);
        }

        $user_obj->setPassw(Auth::getHash($new_password_first));
        $user_service->save($user_obj);

        Messages::setMessage('Информация о пользователе была успешно сохранена');

        return $response->withRedirect($destination);
    }
}
