<?php

namespace WebSK\Auth\Demo;

use DI\Container;
use Fig\Http\Message\StatusCodeInterface;
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
use WebSK\CRUD\MutableContainerAdapter;
use WebSK\Slim\Router;

/**
 * Class AuthDemoApp
 * @package WebSK\Auth\Demo
 */
/** @extends App<Container> */
class AuthDemoApp extends App
{

    /**
     * AuthDemoApp constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct(new ResponseFactory(), $container);

        $this->registerRouterSettings($container);

        $mutable_container = new MutableContainerAdapter($container);
        CacheServiceProvider::register($mutable_container);
        CRUDServiceProvider::register($mutable_container);
        AuthServiceProvider::register($mutable_container);
        UserServiceProvider::register($mutable_container);

        $this->registerRoutes();

        $display_error_details = $container->get('settings.displayErrorDetails');
        if (!is_bool($display_error_details)) {
            throw new \UnexpectedValueException('displayErrorDetails must be a boolean.');
        }
        $error_middleware = $this->addErrorMiddleware($display_error_details, true, true);
        $error_middleware->setDefaultErrorHandler(ErrorHandler::class);
    }

    /**
     * @param Container $container
     */
    protected function registerRouterSettings(Container $container): void
    {
        $route_collector = $this->getRouteCollector();
        $invocation_strategy = $container->get(InvocationStrategyInterface::class);
        if (!$invocation_strategy instanceof InvocationStrategyInterface) {
            throw new \UnexpectedValueException('Container invocation strategy has an invalid type.');
        }
        $route_collector->setDefaultInvocationStrategy($invocation_strategy);
        $route_parser = $route_collector->getRouteParser();

        $container->set(RouteParserInterface::class, $route_parser);
    }

    protected function registerRoutes(): void
    {
        // Demo routing. Redirects
        $this->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
            if (!Auth::getCurrentUserId()) {
                return $response->withHeader('Location', Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM))
                    ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
            }
            if (Auth::currentUserIsAdmin()) {
                return $response->withHeader('Location', Router::urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_LIST))
                    ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
            }

            return $response->withHeader('Location', Router::urlFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => (string) Auth::getCurrentUserId()]))
                ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
        });

        $this->group('/admin', function (RouteCollectorProxyInterface $route_collector_proxy) {
            UserRoutes::registerAdmin($route_collector_proxy);
        })->add(new CurrentUserIsAdmin());

        CaptchaRoutes::register($this);

        UserRoutes::register($this);
        AuthRoutes::register($this);
    }
}
