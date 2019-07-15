<?php

namespace WebSK\Auth\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Users\User;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Auth\Users\UsersUtils;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetHtml;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;

/**
 * Class UserListHandler
 * @package WebSK\Auth\Users\RequestHandlers\Admin
 */
class UserListHandler extends BaseHandler
{
    const FILTER_EMAIL = 'user_email_324234';
    const FILTER_NAME = 'user_name_2354543';

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        /*
        $role_service = UsersServiceProvider::getRoleService($this->container);

        $requested_role_id = $request->getQueryParam('role_id', 0);

        $user_service = UsersServiceProvider::getUserService($this->container);

        $user_objs_arr = [];
        $users_ids_arr = UsersUtils::getUsersIdsArr($requested_role_id);
        foreach ($users_ids_arr as $user_id) {
            $user_objs_arr[] = $user_service->getById($user_id);
        }

        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK' . DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR . 'Users',
            'users_list.tpl.php',
            [
                'requested_role_id' => $requested_role_id,
                'role_objs_arr' => $role_service->getAllRoles(),
                'user_objs_arr' => $user_objs_arr
            ]
        );
        */

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            User::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'user_create_rand435345',
                new User(),
                [
                    new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(User::_NAME)),
                    new CRUDFormRow('Имя', new CRUDFormWidgetInput(User::_FIRST_NAME)),
                    new CRUDFormRow('Фамилия', new CRUDFormWidgetInput(User::_LAST_NAME)),
                    new CRUDFormRow('Email', new CRUDFormWidgetInput(User::_EMAIL))
                ],
                $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => '{this->' . User::_ID . '}'])
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText('{this->' . User::_ID . '}')),
                new CRUDTableColumn(
                    'Логотип',
                    new CRUDTableWidgetHtml(
                        '{this->getImageHTML()}'
                    )
                ),
                new CRUDTableColumn(
                    'Имя',
                    new CRUDTableWidgetTextWithLink(
                        '{this->' . User::_NAME . '}',
                        $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => '{this->' . User::_ID . '}'])
                    )
                ),
                new CRUDTableColumn(
                    'Email',
                    new CRUDTableWidgetText('{this->' . User::_EMAIL . '}')
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_NAME, 'Имя на сайте', User::_NAME),
                new CRUDTableFilterLikeInline(self::FILTER_EMAIL, 'Email', User::_EMAIL),
            ],
            User::_CREATED_AT_TS . ' DESC',
            'users_list_rand234324',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Пользователи');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/admin'),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.admin'), $layout_dto);
    }
}
