<?php

namespace WebSK\Auth\User\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRole;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetHtml;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Views\LayoutDTO;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\User\Role;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class RoleEditHandler
 * @package WebSK\Auth\User\RequestHandlers\Admin
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
        $role_service = UserServiceProvider::getRoleService($this->container);

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

        $user_service = UserServiceProvider::getUserService($this->container);

        $new_user_role = new UserRole();
        $new_user_role->setRoleId($role_id);

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            UserRole::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'user_role_create_rand324324',
                $new_user_role,
                [
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(UserRole::_ROLE_ID)),
                    new CRUDFormRow(
                        'Пользователь',
                        new CRUDFormWidgetReferenceAjax(
                            UserRole::_USER_ID,
                            User::class,
                            User::_NAME,
                            $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST_AJAX),
                            $this->pathFor(
                                UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT,
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
                            return $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_role_obj->getUserId()]);
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
            [
                new CRUDTableFilterEqualInvisible(UserRole::_ROLE_ID, $role_id)
            ],
            UserRole::_CREATED_AT_TS . ' DESC',
            'user_role_list',
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
            new BreadcrumbItemDTO('Главная', AuthConfig::getSkifMainPageUrl()),
            new BreadcrumbItemDTO('Пользователи', $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST)),
            new BreadcrumbItemDTO('Роли пользователей', $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, AuthConfig::getSkifLayout(), $layout_dto);
    }
}
