<?php

namespace WebSK\Auth\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Config\ConfWrapper;
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
    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $role_service = UsersServiceProvider::getRoleService($this->container);

        $role_objs_arr = $role_service->getAllRoles();

        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK' . DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR .  'Users',
            'roles_list.tpl.php',
            ['role_objs_arr' => $role_objs_arr]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Роли пользователей');
        $layout_dto->setContentHtml($content);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Пользователи', $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.admin'), $layout_dto);
    }
}
