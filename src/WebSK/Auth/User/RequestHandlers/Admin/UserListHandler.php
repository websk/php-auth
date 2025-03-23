<?php

namespace WebSK\Auth\User\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserService;
use WebSK\CRUD\CRUD;
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
    const string FILTER_EMAIL = 'user_email_324234';
    const string FILTER_NAME = 'user_name_2354543';

    /** @Inject  */
    protected UserService $user_service;

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
            User::class,
            $this->crud_service->createForm(
                'user_create',
                new User(),
                [
                    new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(User::_NAME, false, true)),
                    new CRUDFormRow('Имя', new CRUDFormWidgetInput(User::_FIRST_NAME)),
                    new CRUDFormRow('Фамилия', new CRUDFormWidgetInput(User::_LAST_NAME)),
                    new CRUDFormRow('Email', new CRUDFormWidgetInput(User::_EMAIL, false, true))
                ],
                function(User $user_obj) {
                    return $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_obj->getId()]);
                }
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(User::_ID)),
                new CRUDTableColumn(
                    'Фото',
                    new CRUDTableWidgetHtml(
                        function(User $user_obj){
                            return $this->user_service->getImageHtml($user_obj);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Имя',
                    new CRUDTableWidgetTextWithLink(
                        User::_NAME,
                        function(User $user_obj) {
                            return $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_obj->getId()]);
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

        $content_html = '<div style="padding: 10px 0;"><ul class="nav nav-tabs">
          <li role="presentation" class="active"><a href="#">Пользователи</a></li>
          <li role="presentation"><a href="' . $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_ROLE_LIST)  . '">Роли пользователей</a></li>
        </ul></div>';

        try {
            $crud_form_response = $crud_table_obj->processRequest($request, $response);
            if ($crud_form_response instanceof ResponseInterface) {
                return $crud_form_response;
            }
        } catch (\Exception $e) {
            Messages::setError($e->getMessage());
            return $response->withHeader('Location', $this->urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST))->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Пользователи');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', AuthConfig::getAdminMainPageUrl()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, AuthConfig::getAdminLayout(), $layout_dto);
    }
}
