<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\UpdateProfileDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /** Met à jour prénom, nom et type de profil de l'utilisateur. */
    public function updateProfile(User $user, UpdateProfileDTO $dto): User
    {
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setProfileType($dto->profileType);
        $this->em->flush();
        return $user;
    }
}