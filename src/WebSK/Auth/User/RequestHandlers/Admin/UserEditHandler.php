<?php

namespace WebSK\Auth\User\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\User\Role;
use WebSK\Auth\User\UserComponents;
use WebSK\Auth\User\UserRole;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetRadios;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Views\LayoutDTO;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class UserEditHandler
 * @package WebSK\Auth\User\RequestHandlers\Admin
 */
class UserEditHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int|null $user_id
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, ?int $user_id = null)
    {
        $user_service = UserServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);

        if (!$user_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'user_create',
            $user_obj,
            [
                new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(User::_NAME, false, true)),
                new CRUDFormRow('Имя', new CRUDFormWidgetInput(User::_FIRST_NAME)),
                new CRUDFormRow('Фамилия', new CRUDFormWidgetInput(User::_LAST_NAME)),
                new CRUDFormRow('Email', new CRUDFormWidgetInput(User::_EMAIL, false, true)),
                new CRUDFormRow('Регистрация подтверждена', new CRUDFormWidgetRadios(User::_IS_CONFIRM, [0 => 'Нет', 1 =>  'Да'])),
                new CRUDFormRow('Дата рождения', new CRUDFormWidgetInput(User::_BIRTHDAY), '(дд.мм.гггг)'),
                new CRUDFormRow('Телефон', new CRUDFormWidgetInput(User::_PHONE)),
                new CRUDFormRow('Город', new CRUDFormWidgetInput(User::_CITY)),
                new CRUDFormRow('Адрес', new CRUDFormWidgetInput(User::_ADDRESS)),
                new CRUDFormRow('Дополнительная информация', new CRUDFormWidgetTextarea(User::_COMMENT)),
            ],
            function(User $user_obj) {
                return $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_obj->getId()]);
            }
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();


        $role_service = UserServiceProvider::getRoleService($this->container);

        $new_user_role = new UserRole();
        $new_user_role->setUserId($user_id);

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            UserRole::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
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
                            $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_LIST_AJAX),
                            $this->pathFor(
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
                        function(UserRole $user_role_obj) use ($role_service) {
                            $role_obj = $role_service->getById($user_role_obj->getRoleId());
                            return $role_obj->getName();
                        },
                        function(UserRole $user_role_obj) {
                            return $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_EDIT, ['role_id' => $user_role_obj->getRoleId()]);
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
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html .= UserComponents::renderUserPhotoForm($user_obj);

        $content_html .= UserComponents::renderPasswordForm($user_obj);

        $content_html .= '<h3>Роли пользователя</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование профиля');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/admin'),
            new BreadcrumbItemDTO('Пользователи', $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.admin'), $layout_dto);
    }
}
