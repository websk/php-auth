<?php

namespace WebSK\Auth\User\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserComponents;
use WebSK\Auth\User\UserRoutes;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\Utils\HTTP;
use WebSK\Utils\Messages;
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $user_id
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $user_id = null)
    {
        $user_service = UserServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);
        if (!$user_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        if (($user_id != Auth::getCurrentUserId()) && !Auth::currentUserIsAdmin()) {
            return $response->withStatus(HTTP::STATUS_FORBIDDEN);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'user_create',
            $user_obj,
            [
                new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(User::_NAME, false, true)),
                new CRUDFormRow('Имя', new CRUDFormWidgetInput(User::_FIRST_NAME)),
                new CRUDFormRow('Фамилия', new CRUDFormWidgetInput(User::_LAST_NAME)),
                new CRUDFormRow('Email', new CRUDFormWidgetInput(User::_EMAIL, false, true)),
                new CRUDFormRow('Дата рождения', new CRUDFormWidgetInput(User::_BIRTHDAY), '(дд.мм.гггг)'),
                new CRUDFormRow('Телефон', new CRUDFormWidgetInput(User::_PHONE)),
                new CRUDFormRow('Город', new CRUDFormWidgetInput(User::_CITY)),
                new CRUDFormRow('Адрес', new CRUDFormWidgetInput(User::_ADDRESS)),
                new CRUDFormRow('Дополнительная информация', new CRUDFormWidgetTextarea(User::_COMMENT)),
            ],
            function(User $user_obj) {
                return $this->pathFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_obj->getId()]);
            }
        );

        try {
            $crud_form_response = $crud_form->processRequest($request, $response);
            if ($crud_form_response instanceof ResponseInterface) {
                return $crud_form_response;
            }
        } catch (\Exception $e) {
            Messages::setError($e->getMessage());
            return $response->withRedirect($this->pathFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]));
        }

        $content_html = $crud_form->html();

        $content_html .= UserComponents::renderUserPhotoForm($user_obj);

        $content_html .= UserComponents::renderPasswordForm($user_obj);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование профиля');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', AuthConfig::getMainPageUrl()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, AuthConfig::getMainLayout(), $layout_dto);
    }
}
