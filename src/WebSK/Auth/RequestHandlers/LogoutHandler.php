<?php

namespace WebSK\Auth\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\SessionService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Auth;

/**
 * Class LogoutHandler
 * @package WebSK\Auth\RequestHandlers
 */
class LogoutHandler extends BaseHandler
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
        $user_id = Auth::getCurrentUserId();

        if ($user_id) {
            $this->session_service->clearUserSession($user_id);
        }

        $destination = $request->getQueryParam('destination', '/');

        return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
    }
}
