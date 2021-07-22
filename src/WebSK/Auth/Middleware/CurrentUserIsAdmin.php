<?php

namespace WebSK\Auth\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Utils\HTTP;

/**
 * Class CurrentUserIsAdmin
 * @package WebSK\Auth\User\Middleware
 */
class CurrentUserIsAdmin
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        if (!Auth::currentUserIsAdmin()) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $response = $next($request, $response);

        return $response;
    }
}
