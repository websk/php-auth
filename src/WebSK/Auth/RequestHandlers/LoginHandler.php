<?php

namespace WebSK\Auth\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (is_null($request->getParam('email')) || is_null($request->getParam('password'))) {
            return $response->withRedirect($this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM));
        }

        $session_service = AuthServiceProvider::getSessionService($this->container);

        $save_auth = ((int)$request->getParam('save_auth') == 1) ? true : false;
        $is_authenticated = $session_service
            ->processAuthorization($request->getParam('email'), $request->getParam('password'), $save_auth);

        if (!$is_authenticated) {
            Messages::setError('Ошибка! Неверный адрес электронной почты или пароль.');
        }

        $destination = $request->getParam('destination', '/');

        return $response->withRedirect($destination);
    }
}
