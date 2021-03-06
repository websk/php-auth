<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\AuthConfig;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class SendConfirmCodeFormHandler
 * @package WebSK\Auth\RequestHandlers
 */
class SendConfirmCodeFormHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK' . DIRECTORY_SEPARATOR . 'Auth',
            'send_confirm_code_form.tpl.php'
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Подтверждение регистрации на сайте');
        $layout_dto->setContentHtml($content);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', AuthConfig::getMainPageUrl()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, AuthConfig::getMainLayout(), $layout_dto);
    }
}
