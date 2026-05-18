<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\User;
use App\Enum\ProfileType;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateProfileDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public string $firstName = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public string $lastName = '';

    #[Assert\NotNull]
    public ProfileType $profileType = ProfileType::SOLO;

    public static function fromUser(User $user): self
    {
        $dto              = new self();
        $dto->firstName   = $user->getFirstName();
        $dto->lastName    = $user->getLastName();
        $dto->profileType = $user->getProfileType();
        return $dto;
    }
}