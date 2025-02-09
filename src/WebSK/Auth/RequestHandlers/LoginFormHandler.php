<?php

namespace WebSK\Auth\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthConfig;
use WebSK\Auth\ExternalAuth;
use WebSK\Auth\Auth;
use WebSK\Auth\User\UserRoutes;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class LoginFormHandler
 * @package WebSK\Auth\RequestHandlers
 */
class LoginFormHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $current_user_id = Auth::getCurrentUserId();
        if ($current_user_id) {
            return $response->withRedirect(
                $this->pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $current_user_id])
            );
        }

        $content = '';

        if (ExternalAuth::useExternalAuth()) {
            $content .= PhpRender::renderTemplateForModuleNamespace(
                'WebSK' . DIRECTORY_SEPARATOR . 'Auth',
                'social_buttons.tpl.php'
            );
        }

        $content .= PhpRender::renderTemplateForModuleNamespace(
            'WebSK' . DIRECTORY_SEPARATOR . 'Auth',
            'login_form.tpl.php'
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Вход на сайт');
        $layout_dto->setContentHtml($content);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', AuthConfig::getMainPageUrl()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, AuthConfig::getMainLayout(), $layout_dto);
    }
}
