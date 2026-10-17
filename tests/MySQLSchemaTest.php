<?php

namespace WebSK\Auth\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use WebSK\Auth\Auth;

#[Group('mysql')]
#[RequiresPhpExtension('pdo_mysql')]
final class MySQLSchemaTest extends TestCase
{
    public function testSchemaSupportsSecureCredentialsAndSessions(): void
    {
        $host = getenv('MYSQL_TEST_HOST');
        if ($host === false || $host === '') {
            self::markTestSkipped('Set MYSQL_TEST_HOST to run the MySQL integration test.');
        }

        $port = getenv('MYSQL_TEST_PORT') ?: '3306';
        $user = getenv('MYSQL_TEST_USER') ?: 'root';
        $password = getenv('MYSQL_TEST_PASSWORD') ?: '';
        $pdo = new \PDO(
            'mysql:host=' . $host . ';port=' . $port . ';charset=utf8mb4',
            $user,
            $password,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        $database = 'php_auth_test_' . bin2hex(random_bytes(6));
        $pdo->exec('CREATE DATABASE `' . $database . '` CHARACTER SET utf8mb4');

        try {
            $pdo->exec('USE `' . $database . '`');
            $dump = file_get_contents(__DIR__ . '/../dumps/db_auth.sql');
            if ($dump === false) {
                self::fail('Could not read the auth schema dump.');
            }
            foreach (preg_split('/;\s*(?:\R|$)/', $dump) ?: [] as $statement) {
                if (trim($statement) !== '') {
                    $pdo->exec($statement);
                }
            }

            $password_hash = Auth::hashPassword('integration password');
            $confirmation_code = bin2hex(random_bytes(32));
            $insert_user = $pdo->prepare(
                'INSERT INTO users (email, passw, name, confirm_code, confirm) VALUES (?, ?, ?, ?, 1)'
            );
            $insert_user->execute(['integration@example.test', $password_hash, 'Integration', $confirmation_code]);
            $user_id = (int) $pdo->lastInsertId();

            $session_token = bin2hex(random_bytes(32));
            $insert_session = $pdo->prepare(
                'INSERT INTO sessions (user_id, session, hostname, timestamp) VALUES (?, ?, ?, ?)'
            );
            $insert_session->execute([$user_id, $session_token, '127.0.0.1', time()]);

            self::assertSame($password_hash, $this->fetchColumn($pdo, 'SELECT passw FROM users'));
            self::assertSame($confirmation_code, $this->fetchColumn($pdo, 'SELECT confirm_code FROM users'));
            self::assertSame($session_token, $this->fetchColumn($pdo, 'SELECT session FROM sessions'));
            self::assertSame('Администраторы', $this->fetchColumn($pdo, 'SELECT name FROM roles WHERE id = 1'));
        } finally {
            $pdo->exec('DROP DATABASE `' . $database . '`');
        }
    }

    private function fetchColumn(\PDO $pdo, string $query): mixed
    {
        $statement = $pdo->query($query);
        if ($statement === false) {
            throw new \RuntimeException('MySQL query failed: ' . $query);
        }

        return $statement->fetchColumn();
    }
}
