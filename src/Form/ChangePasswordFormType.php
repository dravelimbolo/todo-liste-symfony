<?php

declare(strict_types=1);

namespace App\Form;

use App\DTO\ChangePasswordDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr'  => ['autocomplete' => 'current-password'],
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'attr'  => ['autocomplete' => 'new-password'],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'attr'  => ['autocomplete' => 'new-password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ChangePasswordDTO::class]);
    }
}