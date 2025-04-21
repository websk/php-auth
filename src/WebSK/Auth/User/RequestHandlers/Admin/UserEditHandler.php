<?php

namespace WebSK\Auth\User\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\User\Role;
use WebSK\Auth\User\RoleService;
use WebSK\Auth\User\UserComponents;
use WebSK\Auth\User\UserRole;
use WebSK\Auth\User\UserService;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetRadios;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetUpload;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Utils\Messages;
use WebSK\Views\LayoutDTO;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRoutes;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class UserEditHandler
 * @package WebSK\Auth\User\RequestHandlers\Admin
 */
class UserEditHandler extends BaseHandler
{
    /** @Inject */
    protected UserService $user_service;

    /** @Inject */
    protected RoleService $role_service;

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
        $user_obj = $this->user_service->getById($user_id, false);

        if (!$user_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'user_edit',
            $user_obj,
            [
                new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(User::_NAME, false, true)),
                new CRUDFormRow('Имя', new CRUDFormWidgetInput(User::_FIRST_NAME)),
                new CRUDFormRow('Фамилия', new CRUDFormWidgetInput(User::_LAST_NAME)),
                new CRUDFormRow('Email', new CRUDFormWidgetInput(User::_EMAIL, false, true)),
                new CRUDFormRow('Регистрация подтверждена', new CRUDFormWidgetRadios(User::_CONFIRM, [0 => 'Нет', 1 =>  'Да'])),
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
                return $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_obj->getId()]);
            }
        );

        try {
            $crud_form_response = $crud_form->processRequest($request, $response);
            if ($crud_form_response instanceof ResponseInterface) {
                return $crud_form_response;
            }
        } catch (\Exception $e) {
            Messages::setError($e->getMessage());
            return $response->withHeader('Location', $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_id]))->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        $content_html = $crud_form->html();

        $new_user_role = new UserRole();
        $new_user_role->setUserId($user_id);

        $crud_table_obj = $this->crud_service->createTable(
            UserRole::class,
            $this->crud_service->createForm(
                'user_role_create',
                $new_user_role,
                [
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(UserRole::_USER_ID)),
                    new CRUDFormRow(
                        'Роль',
                        new CRUDFormWidgetReferenceAjax(
                            UserRole::_ROLE_ID,
                            Role::class,
                            Role::_NAME,
                            $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_LIST_AJAX),
                            $this->urlFor(
                                UserRoutes::ROUTE_NAME_ADMIN_ROLE_EDIT,
                                ['role_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                            ),
                            true
                        )
                    ),
                ]
            ),
            [
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        function(UserRole $user_role_obj) {
                            $role_obj = $this->role_service->getById($user_role_obj->getRoleId());
                            return $role_obj->getName();
                        },
                        function(UserRole $user_role_obj) {
                            return $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_EDIT, ['role_id' => $user_role_obj->getRoleId()]);
                        }
                    )
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible(UserRole::_USER_ID, $user_id)
            ],
            UserRole::_ID,
            'user_roles_list_324324',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html .= UserComponents::renderPasswordForm($user_obj);

        $content_html .= '<h3>Роли пользователя</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование профиля');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', AuthConfig::getAdminMainPageUrl()),
            new BreadcrumbItemDTO('Пользователи', $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, AuthConfig::getAdminLayout(), $layout_dto);
    }
}
