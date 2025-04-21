<?php

namespace WebSK\Auth\Console;

use GetOpt\Command;
use GetOpt\GetOpt;
use GetOpt\Option;
use WebSK\Auth\Auth;
use WebSK\Auth\User\Role;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRole;
use WebSK\Auth\User\UserRoleService;
use WebSK\Auth\User\UserService;


/**
 * Class CreateUser
 * @package VitrinaTV\Auth\Console
 */
class CreateUserCommand extends Command
{
    const string NAME = 'auth:create_user';

    const string OPTION_EMAIL = 'email';
    const string OPTION_PASSWORD = 'password';

    protected UserService $user_service;

    protected UserRoleService $user_role_service;

    /**
     * CreateUser constructor.
     * @param UserService $user_service
     * @param UserRoleService $user_role_service
     */
    public function __construct(UserService $user_service, UserRoleService $user_role_service)
    {
        $this->user_service = $user_service;
        $this->user_role_service = $user_role_service;

        parent::__construct(self::NAME, [$this, 'execute']);

        $this->addOption(Option::create('e',self::OPTION_EMAIL, GetOpt::OPTIONAL_ARGUMENT)->setDefaultValue('demo' . rand(1, 100) . '@websk.devbox'));
        $this->addOption(Option::create('p', self::OPTION_PASSWORD, GetOpt::OPTIONAL_ARGUMENT)->setDefaultValue('demo' . rand(1, 100)));
    }

    /**
     * @param GetOpt $get_opt
     */
    public function execute(GetOpt $get_opt): void
    {
        $email = $get_opt->getOption(self::OPTION_EMAIL);
        $password = $get_opt->getOption(self::OPTION_PASSWORD);

        $user_obj = new User();
        $user_obj->setEmail($email);
        $user_obj->setName($email);
        $user_obj->setConfirm(true);
        $user_obj->setPassw(Auth::getHash($password));
        $this->user_service->save($user_obj);

        $user_role_obj = new UserRole();
        $user_role_obj->setUserId($user_obj->getId());
        $user_role_obj->setRoleId(Role::ADMIN_ROLE_ID);
        $this->user_role_service->save($user_role_obj);

        echo 'User created' . PHP_EOL;
        echo 'Email: ' . $email . PHP_EOL;
        echo 'Password: ' . $password . PHP_EOL;
    }
}
