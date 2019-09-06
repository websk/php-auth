<?php

namespace WebSK\Auth\User\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\Auth;
use WebSK\Auth\User\UserComponents;
use WebSK\Config\ConfWrapper;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Views\PhpRender;

/**
 * Class UserEditHandler
 * @package WebSK\Auth\User\RequestHandlers
 */
class UserEditHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $user_id
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, int $user_id = null)
    {
        $user_service = UserServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);
        if (!$user_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        if (($user_id != Auth::getCurrentUserId()) && !Auth::currentUserIsAdmin()) {
            return $response->withStatus(StatusCode::HTTP_FORBIDDEN);
        }

        $content_html = UserComponents::renderEditForm($user_obj);

        $content_html .= UserComponents::renderUserPhotoForm($user_obj);

        $content_html .= UserComponents::renderPasswordForm($user_obj);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование профиля');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/'),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
