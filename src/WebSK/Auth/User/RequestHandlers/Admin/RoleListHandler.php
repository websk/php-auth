<?php

namespace WebSK\Auth\User\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\User\Role;
use WebSK\CRUD\CRUD;
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
use WebSK\Auth\User\UserRoutes;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class RoleListHandler
 * @package WebSK\Auth\User\RequestHandlers\Admin
 */
class RoleListHandler extends BaseHandler
{
    const string FILTER_NAME = 'role_name';

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $crud_table_obj = $this->crud_service->createTable(
            Role::class,
            $this->crud_service->createForm(
                'user_create',
                new Role(),
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(Role::_NAME, false, true)),
                    new CRUDFormRow('Обозначение', new CRUDFormWidgetInput(Role::_DESIGNATION, false, true)),
                ],
                function(Role $role_obj) {
                    return $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_EDIT, ['role_id' => $role_obj->getId()]);
                }
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Role::_ID)),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        Role::_NAME,
                        function(Role $role_obj) {
                            return $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_EDIT, ['role_id' => $role_obj->getId()]);
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
            'roles_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = '<div style="padding: 10px 0;"><ul class="nav nav-tabs">
          <li role="presentation"><a href="' . $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST)  . '">Пользователи</a></li>
          <li role="presentation" class="active"><a href="#">Роли пользователей</a></li>
        </ul></div>';

        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Роли');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', AuthConfig::getAdminMainPageUrl()),
            new BreadcrumbItemDTO('Пользователи', $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, AuthConfig::getAdminLayout(), $layout_dto);
    }
}
