<?php

namespace WebSK\Auth\User\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserComponents;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserService;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetUpload;
use WebSK\Utils\Messages;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\PhpRender;

/**
 * Class UserEditHandler
 * @package WebSK\Auth\User\RequestHandlers
 */
class UserEditHandler extends BaseHandler
{
    /** @Inject */
    protected UserService $user_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $user_id
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $user_id): ResponseInterface
    {
        if (!$user_id) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $user_obj = $this->user_service->getById($user_id, false);
        if (!$user_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if (($user_id != Auth::getCurrentUserId()) && !Auth::currentUserIsAdmin()) {
            return $response->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $crud_form = $this->crud_service->createForm(
            'user_edit',
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
                new CRUDFormRow(
                    'Фото',
                    new CRUDFormWidgetUpload(
                        User::_PHOTO,
                        UserService::PHOTO_STORAGE,
                        UserService::PHOTO_DIR_INSIDE_STORAGE,
                        function(User $user_obj) {
                            return $user_obj->getPhoto() ? UserService::PHOTO_URL_RELATIVE_TO_SITE_ROOT . DIRECTORY_SEPARATOR . $user_obj->getPhoto() : '';
                        },
                        $user_obj->getId() . '-' . time(),
                        CRUDFormWidgetUpload::FILE_TYPE_IMAGE
                    )
                )
            ],
            function(User $user_obj) {
                return $this->urlFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_obj->getId()]);
            }
        );

        try {
            $crud_form_response = $crud_form->processRequest($request, $response);
            if ($crud_form_response instanceof ResponseInterface) {
                return $crud_form_response;
            }
        } catch (\Exception $e) {
            Messages::setError($e->getMessage());
            return $response->withHeader('Location', $this->urlFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]))->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        $content_html = $crud_form->html();

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
