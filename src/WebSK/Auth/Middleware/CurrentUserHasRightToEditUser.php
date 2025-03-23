<?php

namespace WebSK\Auth\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use WebSK\Auth\Auth;

/**
 * Class CurrentUserHasRightToEditUser
 * @package WebSK\Auth\User\Middleware
 */
class CurrentUserHasRightToEditUser
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $user_id = (int)$route->getArgument('user_id') ?? null;

        if (!isset($user_id)) {
            return $handler->handle($request);
        }

        $current_user_id = Auth::getCurrentUserId();

        if (($current_user_id != $user_id) && !Auth::currentUserIsAdmin()) {
            $response = new Response();
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return $handler->handle($request);
    }
}

