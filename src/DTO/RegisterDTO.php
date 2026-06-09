<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\ProfileType;
use Symfony\Component\Validator\Constraints as Assert;

final class RegisterDTO
{
    #[Assert\NotBlank(message: 'Le prénom est requis.')]
    #[Assert\Length(max: 100, maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.')]
    public string $firstName = '';

    #[Assert\NotBlank(message: 'Le nom est requis.')]
    #[Assert\Length(max: 100)]
    public string $lastName = '';

    #[Assert\NotBlank(message: "L'email est requis.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    #[Assert\Length(max: 180)]
    public string $email = '';

    #[Assert\NotBlank(message: 'Le mot de passe est requis.')]
    #[Assert\Length(min: 8, minMessage: 'Au moins {{ limit }} caractères.')]
    #[Assert\PasswordStrength(minScore: 2)]
    public string $plainPassword = '';

    #[Assert\NotNull(message: 'Le type de profil est requis.')]
    public ProfileType $profileType = ProfileType::SOLO;

    #[Assert\IsTrue(message: "Vous devez accepter les conditions d'utilisation.")]
    public bool $agreeTerms = false;
}
