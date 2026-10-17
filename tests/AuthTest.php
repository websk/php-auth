<?php

namespace WebSK\Auth\Tests;

use PHPUnit\Framework\TestCase;
use WebSK\Auth\Auth;
use WebSK\Config\ConfWrapper;

final class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        ConfWrapper::setConfig(['auth' => ['salt' => 'legacy-test-salt']]);
    }

    public function testModernPasswordHashRoundTrip(): void
    {
        $hash = Auth::hashPassword('correct horse battery staple');

        self::assertNotSame('correct horse battery staple', $hash);
        self::assertTrue(Auth::verifyPassword('correct horse battery staple', $hash));
        self::assertFalse(Auth::verifyPassword('wrong password', $hash));
        self::assertFalse(Auth::passwordNeedsRehash($hash));
    }

    public function testLegacyHashCanBeVerifiedAndRequiresRehash(): void
    {
        $legacy_hash = md5('legacy-test-salt' . 'old password');

        self::assertTrue(Auth::verifyPassword('old password', $legacy_hash));
        self::assertFalse(Auth::verifyPassword('wrong password', $legacy_hash));
        self::assertTrue(Auth::passwordNeedsRehash($legacy_hash));
    }
}
