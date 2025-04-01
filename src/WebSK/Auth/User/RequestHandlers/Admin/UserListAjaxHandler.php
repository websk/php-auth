<?php

namespace WebSK\Auth\User\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\User\User;
use WebSK\CRUD\CRUD;
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
    const string FILTER_EMAIL = 'user_email_324234';
    const string FILTER_NAME = 'user_name_2354543';

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

        $response->getBody()->write($crud_table_obj->html($request));

        return $response;
    }
}
