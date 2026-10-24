<?php

namespace WebSK\Auth\Tests;

use PHPUnit\Framework\TestCase;
use WebSK\Auth\Auth;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserService;
use WebSK\Config\ConfWrapper;

final class UserServiceSecurityTest extends TestCase
{
    public function testGeneratedCredentialsUseCryptographicRandomness(): void
    {
        $service = $this->serviceWithoutConstructor();

        $password = $service->generatePassword(24);
        $first_code = $service->generateConfirmCode();
        $second_code = $service->generateConfirmCode();

        self::assertMatchesRegularExpression('/^[A-Za-z0-9]{24}$/', $password);
        self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $first_code);
        self::assertNotSame($first_code, $second_code);
    }

    public function testLegacyPasswordIsRehashedAfterSuccessfulLogin(): void
    {
        ConfWrapper::setConfig(['auth' => ['salt' => 'legacy-test-salt']]);

        $user = new User();
        $user->setPassw(md5('legacy-test-salt' . 'old password'));

        $service = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserIdByEmail', 'getById', 'save'])
            ->getMock();
        $service->expects(self::once())->method('getUserIdByEmail')->with('user@example.test')->willReturn(42);
        $service->expects(self::once())->method('getById')->with(42)->willReturn($user);
        $service->expects(self::once())->method('save')->with($user);

        self::assertSame(42, $service->getUserIdByEmailAndPassword('user@example.test', 'old password'));
        self::assertTrue(Auth::verifyPassword('old password', $user->getPassw()));
        self::assertFalse(Auth::passwordNeedsRehash($user->getPassw()));
    }

    public function testUserImageHtmlEscapesStoredValues(): void
    {
        $user = new User();
        $user->setName('"><script>alert(1)</script>');
        $user->setPhoto('../avatar"><script>.png');

        $html = $this->serviceWithoutConstructor()->getImageHtml($user);

        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('alt="&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;"', $html);
        self::assertStringContainsString('/avatar%22%3E%3Cscript%3E.png', $html);
        self::assertStringNotContainsString('../', $html);
    }

    private function serviceWithoutConstructor(): UserService
    {
        $service = (new \ReflectionClass(UserService::class))->newInstanceWithoutConstructor();

        return $service;
    }
}
