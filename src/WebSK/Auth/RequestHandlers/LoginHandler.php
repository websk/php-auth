<?php

namespace WebSK\Auth\RequestHandlers;

use DI\Attribute\Inject;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\SessionService;
use WebSK\CRUD\RequestParams;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

/**
 * Class LoginHandler
 * @package WebSK\Auth\RequestHandlers
 */
class LoginHandler extends BaseHandler
{
    #[Inject]
    protected SessionService $session_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $email = RequestParams::parsedBody($request, 'email');
        $password = RequestParams::parsedBody($request, 'password');
        if (!is_string($email) || !is_string($password)) {
            return $response->withHeader('Location', $this->urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM))
                ->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        $save_auth_value = RequestParams::parsedBody($request, 'save_auth', 0);
        $save_auth = (is_int($save_auth_value) || is_string($save_auth_value))
            && (int) $save_auth_value === 1;
        $is_authenticated = $this->session_service
            ->processAuthorization($email, $password, $save_auth, $message);

        if (!$is_authenticated) {
            Messages::setError($message ?? 'Ошибка авторизации.');
        }

        $destination = RequestParams::parsedString($request, 'destination', '/');

        return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_FOUND);
    }
}
