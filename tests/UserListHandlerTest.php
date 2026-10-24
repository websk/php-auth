<?php

namespace WebSK\Auth\Tests;

use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use WebSK\Auth\Auth;
use WebSK\Auth\User\RequestHandlers\Admin\UserListHandler;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserService;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDForm;
use WebSK\CRUD\Table\CRUDTable;

final class UserListHandlerTest extends TestCase
{
    public function testCreateErrorRedirectsAsGetAndKeepsGeneratedPasswordOffTheForm(): void
    {
        $generated_password = 'random generated password';

        $user_service = $this->createMock(UserService::class);
        $user_service->expects(self::once())
            ->method('generatePassword')
            ->with(UserService::PASSWORD_LENGTH)
            ->willReturn($generated_password);

        $form = $this->createStub(CRUDForm::class);
        $table = $this->createStub(CRUDTable::class);
        $table->method('processRequest')->willThrowException(new \RuntimeException('duplicate user'));

        $crud = $this->createMock(CRUD::class);
        $crud->expects(self::once())
            ->method('createForm')
            ->with(
                'user_create',
                self::callback(
                    static fn (User $user): bool => Auth::verifyPassword($generated_password, $user->getPassw())
                ),
                self::callback(static fn (array $rows): bool => count($rows) === 4),
                self::isCallable()
            )
            ->willReturn($form);
        $crud->method('createTable')->willReturn($table);

        $handler = new class($this->createStub(ContainerInterface::class)) extends UserListHandler {
            public function setDependencies(UserService $user_service, CRUD $crud_service): void
            {
                $this->user_service = $user_service;
                $this->crud_service = $crud_service;
            }

            public function urlFor(string $name, array $data = [], array $queryParams = []): string
            {
                return '/admin/user';
            }
        };
        $handler->setDependencies($user_service, $crud);

        $response = $handler($this->createStub(ServerRequestInterface::class), new Response());

        self::assertSame(StatusCodeInterface::STATUS_SEE_OTHER, $response->getStatusCode());
        self::assertSame('/admin/user', $response->getHeaderLine('Location'));
    }
}
