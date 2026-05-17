<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RegisterDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AuthService
{
    public function __construct(
        private readonly EntityManagerInterface      $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository              $userRepository,
    ) {}

    /**
     * Inscription d'un nouvel utilisateur.
     *
     * @throws \DomainException si l'email est déjà utilisé
     */
    public function register(RegisterDTO $dto): User
    {
        if ($this->userRepository->emailExists($dto->email)) {
            throw new \DomainException('Un compte existe déjà avec cet email.');
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setProfileType($dto->profileType);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $dto->plainPassword)
        );

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Changement de mot de passe sécurisé.
     *
     * @throws \DomainException si le mot de passe actuel est incorrect
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            throw new \DomainException('Le mot de passe actuel est incorrect.');
        }

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $newPassword)
        );

        $this->em->flush();
    }
}