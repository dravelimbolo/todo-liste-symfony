<?php

declare(strict_types=1);

namespace App\Form;

use App\DTO\CreateTaskDTO;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'empty_data' => '',
                'attr'  => ['placeholder' => 'Titre de la tâche'],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => ['rows' => 4, 'placeholder' => 'Description détaillée...'],
            ])
            ->add('priority', EnumType::class, [
                'class'        => TaskPriority::class,
                'label'        => 'Priorité',
                'choice_label' => fn(TaskPriority $p) => $p->label(),
            ])
            ->add('status', EnumType::class, [
                'class'        => TaskStatus::class,
                'label'        => 'Statut',
                'choice_label' => fn(TaskStatus $s) => $s->label(),
            ])
            ->add('dueDate', DateType::class, [
                'label'    => "Date d'échéance",
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
            ])
            ->add('attachments', FileType::class, [
                'label'    => 'Pièces jointes (images uniquement)',
                'multiple' => true,
                'required' => false,
                'mapped'   => false,
                'attr'     => ['accept' => 'image/*'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CreateTaskDTO::class]);
    }
}