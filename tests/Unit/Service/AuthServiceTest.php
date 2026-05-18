<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\RegisterDTO;
use App\Entity\User;
use App\Enum\ProfileType;
use App\Repository\UserRepository;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject      $em;
    private UserPasswordHasherInterface&MockObject $hasher;
    private UserRepository&MockObject              $userRepository;
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->em             = $this->createMock(EntityManagerInterface::class);
        $this->hasher         = $this->createMock(UserPasswordHasherInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->authService    = new AuthService($this->em, $this->hasher, $this->userRepository);
    }

    public function testRegisterCreatesUserSuccessfully(): void
    {
        $dto              = new RegisterDTO();
        $dto->firstName   = 'Marie';
        $dto->lastName    = 'Curie';
        $dto->email       = 'marie@example.com';
        $dto->plainPassword = 'SecurePass1!';
        $dto->profileType = ProfileType::SOLO;
        $dto->agreeTerms  = true;

        $this->userRepository->method('emailExists')->willReturn(false);
        $this->hasher->method('hashPassword')->willReturn('hashed_password');
        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $user = $this->authService->register($dto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('marie@example.com', $user->getEmail());
        $this->assertSame('Marie', $user->getFirstName());
    }

    public function testRegisterThrowsExceptionWhenEmailExists(): void
    {
        $dto        = new RegisterDTO();
        $dto->email = 'existing@example.com';

        $this->userRepository->method('emailExists')->willReturn(true);

        $this->expectException(\DomainException::class);
        $this->authService->register($dto);
    }

    public function testChangePasswordThrowsWhenCurrentPasswordInvalid(): void
    {
        $user = $this->createMock(User::class);
        $this->hasher->method('isPasswordValid')->willReturn(false);

        $this->expectException(\DomainException::class);
        $this->authService->changePassword($user, 'wrong', 'newpass');
    }
}