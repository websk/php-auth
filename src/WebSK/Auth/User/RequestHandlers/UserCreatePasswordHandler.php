<?php

namespace WebSK\Auth\User\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Utils\HTTP;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\UserRoutes;

/**
 * Class UserCreatePasswordHandler
 * @package WebSK\Auth\User\RequestHandlers
 */
class UserCreatePasswordHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $user_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $user_id)
    {
        $user_service = UserServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);
        if (!$user_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $destination = $request->getQueryParam('destination', $this->pathFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]));

        $new_password = $user_service->createAndSendPasswordToUser($user_id);

        Messages::setMessage('Новый пароль: ' . $new_password);

        return $response->withRedirect($destination);
    }
}
