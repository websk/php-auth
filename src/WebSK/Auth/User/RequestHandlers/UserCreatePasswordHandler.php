<?php

namespace WebSK\Auth\User\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\User\UserServiceProvider;
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
     * @param Request $request
     * @param Response $response
     * @param int $user_id
     * @return Response
     */
    public function __invoke(Request $request, Response $response, int $user_id)
    {
        $destination = $request->getQueryParam('destination', $this->pathFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]));

        $user_service = UserServiceProvider::getUserService($this->container);

        $new_password = $user_service->createAndSendPasswordToUser($user_id);

        Messages::setMessage('Новый пароль' . $new_password);

        return $response->withRedirect($destination);
    }
}
