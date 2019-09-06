<?php

namespace WebSK\Auth\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\Auth;

/**
 * Class CurrentUserHasRightToEditUser
 * @package WebSK\Auth\User\Middleware
 */
class CurrentUserHasRightToEditUser
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $user_id = $request->getAttribute('routeInfo')[2]['user_id'] ?? null;

        if (!isset($user_id)) {
            $response = $next($request, $response);

            return $response;
        }

        $user_id = (int)$user_id;

        $current_user_id = Auth::getCurrentUserId();

        if (($current_user_id != $user_id) && !Auth::currentUserIsAdmin()) {
            return $response->withStatus(StatusCode::HTTP_FORBIDDEN);
        }

        $response = $next($request, $response);

        return $response;
    }
}

