#!/usr/bin/env php
<?php

use GetOpt\GetOpt;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Auth\Console\CreateUserCommand;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Config\ConfWrapper;
use WebSK\Console\ConsoleApp;
use WebSK\Utils\Assert;

if (PHP_SAPI !== 'cli') {
    die('Only cli application!');
}

$autoloader  = function () {
    $files = [
        __DIR__ . '/../../../autoload.php', // composer dependency
        __DIR__ . '/../vendor/autoload.php', // stand-alone package
    ];
    foreach ($files as $file) {
        if (is_file($file)) {
            require_once $file;

            return true;
        }
    }

    return false;
};
if (!$autoloader()) {
    die('autoload.php not found');
}

$files = [
    __DIR__ . '/../../../../config/config.php', // composer dependency
    __DIR__ . '/../config/config.php', // stand-alone package
];
foreach ($files as $file) {
    if (is_file($file)) {
        $config = require_once $file;
    }
}
Assert::assert(isset($config), 'Empty config');

ConfWrapper::setConfig($config['settings']);

$app = new ConsoleApp($config);

$container = $app->getContainer();

CacheServiceProvider::register($container);
AuthServiceProvider::register($container);
UserServiceProvider::register($container);

$get_opt = new GetOpt();

$get_opt->addCommand(
    new CreateUserCommand(
            UserServiceProvider::getUserService($container),
        UserServiceProvider::getUserRoleService($container),
    )
);

$get_opt->process();

$command = $get_opt->getCommand();
if (!$command) {
    echo $get_opt->getHelpText();
    exit;
}

call_user_func($command->getHandler(), $get_opt);
