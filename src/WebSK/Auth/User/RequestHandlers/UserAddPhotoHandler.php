<?php

namespace WebSK\Auth\User\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Auth\User\UserRoutes;
use WebSK\Image\ImageConstants;
use WebSK\Image\ImageController;
use WebSK\Utils\HTTP;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\UserServiceProvider;

/**
 * Class UserAddPhotoHandler
 * @package WebSK\Auth\User\RequestHandlers
 */
class UserAddPhotoHandler extends BaseHandler
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
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $destination = $request->getParam('destination', $this->pathFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]));

        $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;
        $file = $_FILES['image_file'];
        $file_name = ImageController::processUpload($file, 'user', $root_images_folder);
        if (!$file_name) {
            Messages::setError('Не удалось загрузить фотографию.');
            return $response->withRedirect($destination);
        }

        $user_obj = $user_service->getById($user_id);
        $user_obj->setPhoto($file_name);

        $user_service->save($user_obj);

        Messages::setMessage('Фотография была успешно добавлена');

        return $response->withRedirect($destination);
    }
}
