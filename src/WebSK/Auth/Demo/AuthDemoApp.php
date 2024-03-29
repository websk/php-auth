<?php

namespace WebSK\Auth\Demo;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
use WebSK\Auth\Auth;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Auth\Middleware\CurrentUserIsAdmin;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\DB\DBWrapper;
use WebSK\Image\ImageRoutes;
use WebSK\Slim\Facade;
use WebSK\Slim\Router;

/**
 * Class AuthDemoApp
 * @package WebSK\Auth\Demo
 */
class AuthDemoApp extends App
{

    /**
     * AuthDemoApp constructor.
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

        // Demo routing. Redirects
        $this->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
            if (!Auth::getCurrentUserId()) {
                return $response->withRedirect(Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM));
            }
            if (Auth::currentUserIsAdmin()) {
                return $response->withRedirect(Router::pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST));
            }

            return $response->withRedirect(Router::pathFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => Auth::getCurrentUserId()]));
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

        ImageRoutes::routes();
    }
}
