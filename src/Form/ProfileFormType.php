<?php

declare(strict_types=1);

namespace App\Form;

use App\DTO\UpdateProfileDTO;
use App\Enum\ProfileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<UpdateProfileDTO> */
class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('lastName',  TextType::class, ['label' => 'Nom'])
            ->add('profileType', EnumType::class, [
                'class'        => ProfileType::class,
                'label'        => 'Type de profil',
                'choice_label' => fn(ProfileType $p) => $p->label(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UpdateProfileDTO::class]);
    }
}