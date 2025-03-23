<?php

namespace WebSK\Auth\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use WebSK\Auth\Auth;

/**
 * Class CurrentUserIsAdmin
 * @package WebSK\Auth\User\Middleware
 */
class CurrentUserIsAdmin
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!Auth::currentUserIsAdmin()) {
            $response = new Response();
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return $handler->handle($request);
    }
}
