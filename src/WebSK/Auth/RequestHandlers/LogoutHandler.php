<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
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
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $user_id = Auth::getCurrentUserId();

        if ($user_id) {
            $session_service = AuthServiceProvider::getSessionService($this->container);
            $session_service->clearUserSession($user_id);
        }

        //\Hybrid_Auth::logoutAllProviders();

        $destination = $request->getQueryParam('destination', '/');

        return $response->withRedirect($destination);
    }
}
