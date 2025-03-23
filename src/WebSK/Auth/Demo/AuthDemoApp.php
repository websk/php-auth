<?php

namespace WebSK\Auth\Demo;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Factory\ResponseFactory;
use WebSK\Auth\Auth;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Auth\Middleware\CurrentUserIsAdmin;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\Slim\Router;

/**
 * Class AuthDemoApp
 * @package WebSK\Auth\Demo
 */
class AuthDemoApp extends App
{

    /**
     * AuthDemoApp constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct(new ResponseFactory(), $container);

        $this->registerRouterSettings($container);

        CacheServiceProvider::register($container);
        CRUDServiceProvider::register($container);
        AuthServiceProvider::register($container);
        UserServiceProvider::register($container);

        $this->registerRoutes($container);

        $error_middleware = $this->addErrorMiddleware(true, true, true);
        $error_middleware->setDefaultErrorHandler(ErrorHandler::class);
    }

    /**
     * @param ContainerInterface $container
     */
    protected function registerRouterSettings(ContainerInterface $container): void
    {
        $route_collector = $this->getRouteCollector();
        $route_collector->setDefaultInvocationStrategy($container->get(InvocationStrategyInterface::class));

        //$route_parser = $this->getRouteCollector()->getRouteParser();
        //$this->getContainer()->set(RouteParserInterface::class, $route_parser);

        $route_parser = $route_collector->getRouteParser();

        $container->set(RouteParserInterface::class, $route_parser);
    }

    protected function registerRoutes(ContainerInterface $container): void
    {
        // Demo routing. Redirects
        $this->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
            if (!Auth::getCurrentUserId()) {
                return $response->withHeader('Location', Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM))
                    ->withStatus(StatusCodeInterface::STATUS_FOUND);
            }
            if (Auth::currentUserIsAdmin()) {
                return $response->withHeader('Location', Router::urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST))
                    ->withStatus(StatusCodeInterface::STATUS_FOUND);
            }

            return $response->withHeader('Location', Router::urlFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => Auth::getCurrentUserId()]))
                ->withStatus(StatusCodeInterface::STATUS_FOUND);
        });

        $this->group('/admin', function (RouteCollectorProxyInterface $route_collector_proxy) {
            UserRoutes::registerAdmin($route_collector_proxy);
        })->add(new CurrentUserIsAdmin());

        CaptchaRoutes::register($this);

        UserRoutes::register($this);
        AuthRoutes::register($this);
    }
}
