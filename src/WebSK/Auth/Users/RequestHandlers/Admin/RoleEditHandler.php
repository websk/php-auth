<?php

namespace WebSK\Auth\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\Users\User;
use WebSK\Auth\Users\UserRole;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetHtml;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Views\LayoutDTO;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Users\Role;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class RoleEditHandler
 * @package WebSK\Auth\Users\RequestHandlers\Admin
 */
class RoleEditHandler extends BaseHandler
{
    const FILTER_EMAIL = 'user_email_324234';
    const FILTER_NAME = 'user_name_2354543';

    /**
     * @param Request $request
     * @param Response $response
     * @param int $role_id
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function __invoke(Request $request, Response $response, int $role_id)
    {
        $role_service = UsersServiceProvider::getRoleService($this->container);

        $role_obj = $role_service->getById($role_id, false);
        if (!$role_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'role_edit_rand3453245',
            $role_obj,
            [
                new CRUDFormRow(
                    'Название',
                    new CRUDFormWidgetInput(Role::_NAME, false, true)
                ),
                new CRUDFormRow(
                    'Обозначение',
                    new CRUDFormWidgetInput(Role::_DESIGNATION, false, true)
                ),
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $user_service = UsersServiceProvider::getUserService($this->container);

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            UserRole::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'user_role_create_rand324324',
                new UserRole(),
                [
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(UserRole::_ROLE_ID)),
                    new CRUDFormRow(
                        'Пользователь',
                        new CRUDFormWidgetReferenceAjax(
                            UserRole::_USER_ID,
                            User::class,
                            User::_NAME,
                            $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_LIST_AJAX),
                            $this->pathFor(
                                UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT,
                                ['user_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                            ),
                            true
                        )
                    ),
                ]
            ),
            [
                new CRUDTableColumn(
                    'ID',
                    new CRUDTableWidgetText(
                        function(UserRole $user_role_obj) {
                            return $user_role_obj->getUserId();
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Фото',
                    new CRUDTableWidgetHtml(
                        function(UserRole $user_role_obj) use ($user_service) {
                            $user_obj = $user_service->getById($user_role_obj->getUserId());
                            return $user_service->getImageHtml($user_obj);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Имя',
                    new CRUDTableWidgetTextWithLink(
                        function(UserRole $user_role_obj) use ($user_service) {
                            $user_obj = $user_service->getById($user_role_obj->getUserId());
                            return $user_obj->getName();
                        },
                        function(UserRole $user_role_obj) {
                            return $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_role_obj->getUserId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Email',
                    new CRUDTableWidgetText(
                        function(UserRole $user_role_obj) use ($user_service) {
                            $user_obj = $user_service->getById($user_role_obj->getUserId());
                            return $user_obj->getEmail();
                        }
                    )
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [],
            UserRole::_CREATED_AT_TS . ' DESC',
            'users_role_list_rand3244',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_table_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_table_response instanceof Response) {
            return $crud_form_table_response;
        }

        $content_html .= '<h3>Пользователи с данной ролью</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Роль ' . $role_obj->getName());
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/admin'),
            new BreadcrumbItemDTO('Пользователи', $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_LIST)),
            new BreadcrumbItemDTO('Роли пользователей', $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_ROLE_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.admin'), $layout_dto);
    }
}
