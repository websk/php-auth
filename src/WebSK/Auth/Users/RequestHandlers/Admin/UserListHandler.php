<?php

namespace WebSK\Auth\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Auth\Users\UsersUtils;
use WebSK\Config\ConfWrapper;
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
    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
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

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Пользователи');
        $layout_dto->setContentHtml($content);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/admin'),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.admin'), $layout_dto);
    }
}
