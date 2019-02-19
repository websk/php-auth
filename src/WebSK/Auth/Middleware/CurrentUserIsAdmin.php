<?php

namespace WebSK\Auth\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Auth;
use WebSK\Utils\HTTP;

/**
 * Class CurrentUserIsAdmin
 * @package WebSK\Auth\Users\Middleware
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
            return $response->withStatus(HTTP::STATUS_FORBIDDEN);
        }

        $response = $next($request, $response);

        return $response;
    }
}
