<?php

namespace WebSK\Auth\User\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Utils\HTTP;
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $user_id
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $user_id)
    {
        $user_service = UserServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);

        if (!$user_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        if (($user_id != Auth::getCurrentUserId()) && !Auth::currentUserIsAdmin()) {
            return $response->withStatus(HTTP::STATUS_FORBIDDEN);
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
