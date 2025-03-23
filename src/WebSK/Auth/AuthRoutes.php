<?php

namespace WebSK\Auth;

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\Auth\RequestHandlers\ConfirmRegistrationHandler;
use WebSK\Auth\RequestHandlers\ForgotPasswordFormHandler;
use WebSK\Auth\RequestHandlers\ForgotPasswordHandler;
use WebSK\Auth\RequestHandlers\LoginFormHandler;
use WebSK\Auth\RequestHandlers\LoginHandler;
use WebSK\Auth\RequestHandlers\LogoutHandler;
use WebSK\Auth\RequestHandlers\RegistrationFormHandler;
use WebSK\Auth\RequestHandlers\RegistrationHandler;
use WebSK\Auth\RequestHandlers\SendConfirmCodeFormHandler;
use WebSK\Auth\RequestHandlers\SendConfirmCodeHandler;

/**
 * Class AuthRoutes
 * @package WebSK\Auth
 */
class AuthRoutes
{
    const string ROUTE_NAME_AUTH_LOGIN_FORM = 'auth:login_form';
    const string ROUTE_NAME_AUTH_LOGIN = 'auth:login';
    const string ROUTE_NAME_AUTH_LOGOUT = 'auth:logout';
    const string ROUTE_NAME_AUTH_FORGOT_PASSWORD_FORM = 'auth:forgot_password_form';
    const string ROUTE_NAME_AUTH_FORGOT_PASSWORD = 'auth:forgot_password';
    const string ROUTE_NAME_AUTH_REGISTRATION_FORM = 'auth:registration_form';
    const string ROUTE_NAME_AUTH_REGISTRATION = 'auth:registration';
    const string ROUTE_NAME_AUTH_CONFIRM_REGISTRATION = 'auth:confirm_registration';
    const string ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM = 'auth:send_confirm_code_form';
    const string ROUTE_NAME_AUTH_SEND_CONFIRM_CODE = 'auth:send_confirm_code';

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->group('/auth', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->get('/login_form', LoginFormHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_LOGIN_FORM);

            $route_collector_proxy->post('/login', LoginHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_LOGIN);

            $route_collector_proxy->get('/logout', LogoutHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_LOGOUT);

            $route_collector_proxy->get('/forgot_password_form', ForgotPasswordFormHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_FORGOT_PASSWORD_FORM);

            $route_collector_proxy->post('/forgot_password', ForgotPasswordHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_FORGOT_PASSWORD);

            $route_collector_proxy->get('/registration_form', RegistrationFormHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_REGISTRATION_FORM);

            $route_collector_proxy->post('/registration', RegistrationHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_REGISTRATION);

            $route_collector_proxy->get('/confirm_registration/{confirm_code:\w+}', ConfirmRegistrationHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_CONFIRM_REGISTRATION);

            $route_collector_proxy->get('/send_confirm_code_form', SendConfirmCodeFormHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM);

            $route_collector_proxy->post('/send_confirm_code', SendConfirmCodeHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE);
        });
    }
}
