<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

/**
 * Class LoginHandler
 * @package WebSK\Auth\RequestHandlers
 */
class LoginHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        if (is_null($request->getParam('email')) || is_null($request->getParam('password'))) {
            return $response->withRedirect($this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM));
        }

        $auth_service = AuthServiceProvider::getAuthService($this->container);

        $save_auth = ((int)$request->getParam('save_auth') == 1) ? true : false;
        $is_authenticated = $auth_service
            ->processAuthorization($request->getParam('email'), $request->getParam('password'), $save_auth);

        if (!$is_authenticated) {
            Messages::setError('Ошибка! Неверный адрес электронной почты или пароль.');
        }

        $destination = $request->getParam('destination', '/');

        return $response->withRedirect($destination);
    }
}
