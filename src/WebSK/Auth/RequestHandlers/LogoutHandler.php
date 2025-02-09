<?php

namespace WebSK\Auth\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Auth;

/**
 * Class LogoutHandler
 * @package WebSK\Auth\RequestHandlers
 */
class LogoutHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $user_id = Auth::getCurrentUserId();

        if ($user_id) {
            $session_service = AuthServiceProvider::getSessionService($this->container);
            $session_service->clearUserSession($user_id);
        }

        //TODO: External auth logout

        $destination = $request->getQueryParam('destination', '/');

        return $response->withRedirect($destination);
    }
}
