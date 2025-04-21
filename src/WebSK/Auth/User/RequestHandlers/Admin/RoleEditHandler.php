<?php

namespace WebSK\Auth\User\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\User\RoleService;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRole;
use WebSK\Auth\User\UserService;
use WebSK\CRUD\CRUD;
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
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class RoleEditHandler
 * @package WebSK\Auth\User\RequestHandlers\Admin
 */
class RoleEditHandler extends BaseHandler
{
    const string FILTER_EMAIL = 'user_email_324234';
    const string FILTER_NAME = 'user_name_2354543';

    /** @Inject */
    protected RoleService $role_service;

    /** @Inject */
    protected UserService $user_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $role_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $role_id): ResponseInterface
    {
        $role_obj = $this->role_service->getById($role_id, false);
        if (!$role_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'role_edit',
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
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $new_user_role = new UserRole();
        $new_user_role->setRoleId($role_id);

        $crud_table_obj = $this->crud_service->createTable(
            UserRole::class,
            $this->crud_service->createForm(
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
                            $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST_AJAX),
                            $this->urlFor(
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
                        function(UserRole $user_role_obj) {
                            $user_obj = $this->user_service->getById($user_role_obj->getUserId());
                            return $this->user_service->getImageHtml($user_obj);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Имя',
                    new CRUDTableWidgetTextWithLink(
                        function(UserRole $user_role_obj) {
                            $user_obj = $this->user_service->getById($user_role_obj->getUserId());
                            return $user_obj->getName();
                        },
                        function(UserRole $user_role_obj) {
                            return $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_role_obj->getUserId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Email',
                    new CRUDTableWidgetText(
                        function(UserRole $user_role_obj) {
                            $user_obj = $this->user_service->getById($user_role_obj->getUserId());
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
        if ($crud_form_table_response instanceof ResponseInterface) {
            return $crud_form_table_response;
        }

        $content_html .= '<h3>Пользователи с данной ролью</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Роль ' . $role_obj->getName());
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', AuthConfig::getAdminMainPageUrl()),
            new BreadcrumbItemDTO('Пользователи', $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST)),
            new BreadcrumbItemDTO('Роли пользователей', $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, AuthConfig::getAdminLayout(), $layout_dto);
    }
}
