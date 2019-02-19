<?php

namespace WebSK\Auth\Users\RequestHandlers;

use WebSK\Image\ImageConstants;
use WebSK\Image\ImageController;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Utils\HTTP;

/**
 * Class UserAddPhotoHandler
 * @package WebSK\Auth\Users\RequestHandlers
 */
class UserAddPhotoHandler extends BaseHandler
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
        $user_service = UsersServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);

        if (!$user_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $destination = $request->getParam('destination', $this->pathFor(UserEditHandler::class, ['user_id' => $user_id]));

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
