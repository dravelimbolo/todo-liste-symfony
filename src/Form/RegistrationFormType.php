<?php

declare(strict_types=1);

namespace App\Form;

use App\DTO\RegisterDTO;
use App\Enum\ProfileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<RegisterDTO> */
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr'  => ['placeholder' => 'Jean'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr'  => ['placeholder' => 'Dupont'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr'  => ['placeholder' => 'nom@exemple.com'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr'  => ['placeholder' => '••••••••', 'autocomplete' => 'new-password'],
            ])
            ->add('profileType', EnumType::class, [
                'class'        => ProfileType::class,
                'label'        => 'Type de profil',
                'choice_label' => fn(ProfileType $p) => $p->label(),
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'  => "J'accepte les conditions d'utilisation.",
                'mapped' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => RegisterDTO::class]);
    }
}