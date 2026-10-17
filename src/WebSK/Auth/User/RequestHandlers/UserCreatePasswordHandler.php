<?php

namespace WebSK\Auth\User\RequestHandlers;

use DI\Attribute\Inject;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\User\UserService;
use WebSK\CRUD\RequestParams;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\UserRoutes;

/**
 * Class UserCreatePasswordHandler
 * @package WebSK\Auth\User\RequestHandlers
 */
class UserCreatePasswordHandler extends BaseHandler
{
    #[Inject]
    protected UserService $user_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $user_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $user_id): ResponseInterface
    {
        $user_obj = $this->user_service->getById($user_id, false);
        if (!$user_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $destination = RequestParams::queryString(
            $request,
            'destination',
            $this->urlFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => (string) $user_id])
        );

        $new_password = $this->user_service->createAndSendPasswordToUser($user_id);

        Messages::setMessage('Новый пароль: ' . $new_password);

        return $response->withHeader('Location', $destination)->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
    }
}
