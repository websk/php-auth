<?php

namespace WebSK\Auth;

use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Middleware\CurrentUserIsAdmin;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Config\ConfWrapper;
use WebSK\DB\DBWrapper;
use WebSK\Logger\LoggerRoutes;
use WebSK\Logger\LoggerServiceProvider;
use WebSK\Slim\Facade;
use WebSK\Slim\Router;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class PhpAuthApp
 * @package WebSK\Auth
 */
class AuthApp extends App
{
    /**
     * SkifApp constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $container = $this->getContainer();

        CacheServiceProvider::register($container);
        AuthServiceProvider::register($container);
        UsersServiceProvider::register($container);
        LoggerServiceProvider::register($container);

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        $container = $this->getContainer();
        $container['foundHandler'] = function () {
            return new RequestResponseArgs();
        };

        $this->get('/', function (Request $request, Response $response) {
            if (!Auth::getCurrentUserId()) {
                return $response->withRedirect(Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM));
            }

            return $response->withRedirect(Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_LIST));
        });

        $this->group('/admin', function (App $app) {
            UsersRoutes::registerAdmin($app);
            LoggerRoutes::registerAdmin($app);
        })->add(new CurrentUserIsAdmin());

        CaptchaRoutes::register($this);

        UsersRoutes::register($this);
        AuthRoutes::register($this);

        /** Use facade */
        Facade::setFacadeApplication($this);

        /** Set DBWrapper db service */
        DBWrapper::setDbService(AuthServiceProvider::getDBService($container));
    }
}
