<?php

namespace WebSK\Auth\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Users\Role;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Views\LayoutDTO;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class RoleListHandler
 * @package WebSK\Auth\Users\RequestHandlers\Admin
 */
class RoleListHandler extends BaseHandler
{
    const FILTER_NAME = 'role_name';

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Role::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'user_create_rand435345',
                new Role(),
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(Role::_NAME)),
                    new CRUDFormRow('Обозначение', new CRUDFormWidgetInput(Role::_DESIGNATION)),
                ],
                function(Role $role_obj) {
                    return $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_ROLE_EDIT, ['role_id' => $role_obj->getId()]);
                }
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Role::_ID)),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        Role::_NAME,
                        function(Role $role_obj) {
                            return $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_ROLE_EDIT, ['role_id' => $role_obj->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Обозначение',
                    new CRUDTableWidgetText(Role::_DESIGNATION)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_NAME, 'Название', Role::_NAME),
            ],
            Role::_NAME,
            'roles_list_324324',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Роли');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/admin'),
            new BreadcrumbItemDTO('Пользователи', $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.admin'), $layout_dto);
    }
}
