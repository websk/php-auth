<?php

namespace WebSK\Auth\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Utils\HTTP;

/**
 * Class CurrentUserHasRightToEditUser
 * @package WebSK\Auth\User\Middleware
 */
class CurrentUserHasRightToEditUser
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $user_id = $request->getAttribute('routeInfo')[2]['user_id'] ?? null;

        if (!isset($user_id)) {
            $response = $next($request, $response);

            return $response;
        }

        $user_id = (int)$user_id;

        $current_user_id = Auth::getCurrentUserId();

        if (($current_user_id != $user_id) && !Auth::currentUserIsAdmin()) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        return $next($request, $response);
    }
}

