<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\Service\UserService;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(ValidatorInterface::class);
        $this->userService = new UserService(
            $this->mockObject,
            $this->createMock(UserPasswordHasher::class),
            $this->createMock(EntityManagerInterface::class),
        );
    }

    public function test_create_validationFailed_exceptionThrown(): void
    {
        $this->mockObject->method('validate')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('test_error', null, [], null, null, null)
        ]));

        $this->expectException(ValidationException::class);
        $this->userService->create('test_pswd', 'test_username');
    }

    public function test_create_validationSuccess_returnUser(): void
    {
        $this->mockObject->method('validate')->willReturn(new ConstraintViolationList([]));
        $user = $this->userService->create('test_pswd', 'test_username');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test_username', $user->getUserIdentifier());
    }
}
