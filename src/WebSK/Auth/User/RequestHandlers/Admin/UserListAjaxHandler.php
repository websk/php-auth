<?php

namespace WebSK\Auth\User\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\User\User;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class UserListAjaxHandler
 * @package WebSK\Auth\User\RequestHandlers\Admin
 */
class UserListAjaxHandler extends BaseHandler
{
    const FILTER_EMAIL = 'user_email_324234';
    const FILTER_NAME = 'user_name_2354543';

    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface|Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            User::class,
            null,
            [
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(User::_NAME)
                ),
                new CRUDTableColumn(
                    'Имя',
                    new CRUDTableWidgetText(User::_NAME)
                ),
                new CRUDTableColumn(
                    'Email',
                    new CRUDTableWidgetText(User::_EMAIL)
                ),

            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_NAME, 'Имя на сайте', User::_NAME),
                new CRUDTableFilterLikeInline(self::FILTER_EMAIL, 'Email', User::_EMAIL),
            ],
            '',
            'user_list_rand234324',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $response->write($crud_table_obj->html($request));
    }
}
