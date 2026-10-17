<?php

namespace WebSK\Auth\Tests;

use PHPUnit\Framework\TestCase;
use WebSK\Auth\SessionService;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserService;

final class SessionServiceSecurityTest extends TestCase
{
    public function testAuthorizationCreatesRandomSessionToken(): void
    {
        $user = new User();
        $user->setConfirm(true);

        $user_service = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserIdByEmailAndPassword', 'getById'])
            ->getMock();
        $user_service->method('getUserIdByEmailAndPassword')->willReturn(7);
        $user_service->method('getById')->with(7)->willReturn($user);

        $session_service = $this->getMockBuilder(SessionService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['storeUserSession'])
            ->getMock();
        $session_service->expects(self::once())
            ->method('storeUserSession')
            ->with(
                7,
                self::callback(static fn (string $token): bool => preg_match('/^[a-f0-9]{64}$/', $token) === 1),
                0
            );

        $property = new \ReflectionProperty(SessionService::class, 'user_service');
        $property->setValue($session_service, $user_service);

        self::assertTrue($session_service->processAuthorization('user@example.test', 'password'));
    }
}
