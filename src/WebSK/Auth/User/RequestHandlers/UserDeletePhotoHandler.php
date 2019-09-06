<?php

namespace WebSK\Auth\User\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\Auth;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;

/**
 * Class UserDeletePhotoHandler
 * @package WebSK\Auth\User\RequestHandlers
 */
class UserDeletePhotoHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $user_id
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, int $user_id)
    {
        $user_service = UserServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);

        if (!$user_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        if (($user_id != Auth::getCurrentUserId()) && !Auth::currentUserIsAdmin()) {
            return $response->withStatus(StatusCode::HTTP_FORBIDDEN);
        }

        $destination = $request->getQueryParam('destination', $this->pathFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]));

        if (!$user_service->deletePhoto($user_obj)) {
            Messages::setError('Не удалось удалить фотографию.');
            return $response->withRedirect($destination);
        }

        Messages::setMessage('Фотография была успешно удалена');

        return $response->withRedirect($destination);
    }
}
