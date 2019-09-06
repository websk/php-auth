<?php

namespace WebSK\Auth;

use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Middleware\CurrentUserIsAdmin;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\DB\DBWrapper;
use WebSK\Slim\Facade;
use WebSK\Slim\Router;

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
        UserServiceProvider::register($container);
        CRUDServiceProvider::register($container);

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

            return $response->withRedirect(Router::pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST));
        });

        $this->group('/admin', function (App $app) {
            UserRoutes::registerAdmin($app);
        })->add(new CurrentUserIsAdmin());

        CaptchaRoutes::register($this);

        UserRoutes::register($this);
        AuthRoutes::register($this);

        /** Use facade */
        Facade::setFacadeApplication($this);

        /** Set DBWrapper db service */
        DBWrapper::setDbService(AuthServiceProvider::getDBService($container));
    }
}
