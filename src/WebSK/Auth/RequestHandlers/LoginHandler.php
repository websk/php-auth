<?php

namespace WebSK\Auth\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\SessionService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

/**
 * Class LoginHandler
 * @package WebSK\Auth\RequestHandlers
 */
class LoginHandler extends BaseHandler
{
    /** @Inject */
    protected SessionService $session_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (is_null($request->getParam('email')) || is_null($request->getParam('password'))) {
            return $response->withHeader('Location', $this->urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM))
                ->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        $save_auth = ((int)$request->getParam('save_auth') == 1) ? true : false;
        $is_authenticated = $this->session_service
            ->processAuthorization($request->getParam('email'), $request->getParam('password'), $save_auth, $message);

        if (!$is_authenticated) {
            Messages::setError($message);
        }

        $destination = $request->getParam('destination', '/');

        return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
    }
}
