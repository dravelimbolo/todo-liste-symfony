<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class ChangePasswordDTO
{
    #[Assert\NotBlank(message: 'Le mot de passe actuel est requis.')]
    public string $currentPassword = '';

    #[Assert\NotBlank(message: 'Le nouveau mot de passe est requis.')]
    #[Assert\Length(min: 8, minMessage: 'Au moins {{ limit }} caractères.')]
    #[Assert\PasswordStrength(minScore: 2)]
    public string $newPassword = '';

    #[Assert\NotBlank]
    #[Assert\EqualTo(
        propertyPath: 'newPassword',
        message: 'Les mots de passe ne correspondent pas.'
    )]
    public string $confirmPassword = '';
}
