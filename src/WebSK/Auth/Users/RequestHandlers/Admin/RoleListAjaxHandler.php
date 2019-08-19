<?php

namespace WebSK\Auth\Users\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Users\Role;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class RoleListAjaxHandler
 * @package WebSK\Auth\Users\RequestHandlers\Admin
 */
class RoleListAjaxHandler extends BaseHandler
{
    const FILTER_NAME = 'role_name_234234';

    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface|Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Role::class,
            null,
            [
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(Role::_NAME)
                ),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetText(Role::_NAME)
                ),
                new CRUDTableColumn(
                    'Обозначение',
                    new CRUDTableWidgetText(Role::_DESIGNATION)
                ),

            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_NAME, 'Название', Role::_NAME),
            ],
            '',
            'role_list_rand5343253',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $response->write($crud_table_obj->html($request));
    }

}
