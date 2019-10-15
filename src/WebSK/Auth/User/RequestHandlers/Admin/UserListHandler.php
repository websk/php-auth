<?php

namespace WebSK\Auth\User\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;
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
use WebSK\Utils\Messages;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;

/**
 * Class UserListHandler
 * @package WebSK\Auth\User\RequestHandlers\Admin
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
        $user_service = UserServiceProvider::getUserService($this->container);

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            User::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'user_create',
                new User(),
                [
                    new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(User::_NAME)),
                    new CRUDFormRow('Имя', new CRUDFormWidgetInput(User::_FIRST_NAME)),
                    new CRUDFormRow('Фамилия', new CRUDFormWidgetInput(User::_LAST_NAME)),
                    new CRUDFormRow('Email', new CRUDFormWidgetInput(User::_EMAIL))
                ],
                function(User $user_obj) {
                    return $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_obj->getId()]);
                }
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(User::_ID)),
                new CRUDTableColumn(
                    'Фото',
                    new CRUDTableWidgetHtml(
                        function(User $user_obj) use ($user_service) {
                            return $user_service->getImageHtml($user_obj);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Имя',
                    new CRUDTableWidgetTextWithLink(
                        User::_NAME,
                        function(User $user_obj) {
                            return $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_obj->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Email',
                    new CRUDTableWidgetText(User::_EMAIL)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_NAME, 'Имя на сайте', User::_NAME),
                new CRUDTableFilterLikeInline(self::FILTER_EMAIL, 'Email', User::_EMAIL),
            ],
            User::_CREATED_AT_TS . ' DESC',
            'users_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $content_html = '';

        $content_html .= '<div style="padding: 10px 0;"><ul class="nav nav-tabs">
          <li role="presentation" class="active"><a href="#">Пользователи</a></li>
          <li role="presentation"><a href="' . $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_LIST)  . '">Роли пользователей</a></li>
        </ul></div>';

        try {
            $crud_form_response = $crud_table_obj->processRequest($request, $response);
            if ($crud_form_response instanceof Response) {
                return $crud_form_response;
            }
        } catch (\Exception $e) {
            Messages::setError($e->getMessage());
            return $response->withRedirect($this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST));
        }

        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Пользователи');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', AuthConfig::getSkifMainPageUrl()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, AuthConfig::getSkifLayout(), $layout_dto);
    }
}
