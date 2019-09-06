<?php

namespace WebSK\Auth\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\Auth;

/**
 * Class CurrentUserIsAdmin
 * @package WebSK\Auth\User\Middleware
 */
class CurrentUserIsAdmin
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        if (!Auth::currentUserIsAdmin()) {
            return $response->withStatus(StatusCode::HTTP_FORBIDDEN);
        }

        $response = $next($request, $response);

        return $response;
    }
}
