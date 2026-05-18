<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Task;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateTaskDTO
{
    #[Assert\NotBlank(message: 'Le titre est requis.')]
    #[Assert\Length(max: 255)]
    public string $title = '';

    #[Assert\Length(max: 5000)]
    public ?string $description = null;

    #[Assert\NotNull]
    public TaskPriority $priority = TaskPriority::MEDIUM;

    #[Assert\NotNull]
    public TaskStatus $status = TaskStatus::TODO;

    public ?\DateTimeImmutable $dueDate = null;

    public ?string $assignedToId = null;

    /**
     * @var UploadedFile[]
     */
    #[Assert\All([
        new Assert\File(
            maxSize: '5M',
            maxSizeMessage: 'La pièce jointe est trop volumineuse (max {{ limit }}).',
            mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
            mimeTypesMessage: 'Seules les images sont acceptées (JPG, PNG, WebP, GIF).'
        )
    ])]
    public array $attachments = [];

    /** Hydrate depuis une entité existante pour la page d'édition */
    public static function fromTask(Task $task): self
    {
        $dto              = new self();
        $dto->title       = $task->getTitle();
        $dto->description = $task->getDescription();
        $dto->priority    = $task->getPriority();
        $dto->status      = $task->getStatus();
        $dto->dueDate     = $task->getDueDate();
        $dto->assignedToId = $task->getAssignedTo()?->getId()->toString();
        return $dto;
    }
}