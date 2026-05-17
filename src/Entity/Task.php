<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'tasks')]
#[ORM\HasLifecycleCallbacks]
class Task
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', enumType: TaskPriority::class, length: 10)]
    private TaskPriority $priority;

    #[ORM\Column(type: 'string', enumType: TaskStatus::class, length: 15)]
    private TaskStatus $status;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dueDate = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /** Soft delete — ne jamais supprimer physiquement */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'createdTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private User $createdBy;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'assignedTasks')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $assignedTo = null;

    /** @var Collection<int, Attachment> */
    #[ORM\OneToMany(
        mappedBy: 'task',
        targetEntity: Attachment::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $attachments;

    public function __construct()
    {
        $this->id          = Uuid::v7();
        $this->createdAt   = new \DateTimeImmutable();
        $this->status      = TaskStatus::TODO;
        $this->priority    = TaskPriority::MEDIUM;
        $this->attachments = new ArrayCollection();
    }

    public function getId(): Uuid { return $this->id; }

    public function getTitle(): string { return $this->title; }

    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPriority(): TaskPriority { return $this->priority; }

    public function setPriority(TaskPriority $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

    public function getStatus(): TaskStatus { return $this->status; }

    public function setStatus(TaskStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable { return $this->dueDate; }

    public function setDueDate(?\DateTimeImmutable $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void { $this->updatedAt = new \DateTimeImmutable(); }

    public function getDeletedAt(): ?\DateTimeImmutable { return $this->deletedAt; }

    /** Soft delete — appelé uniquement par TaskService */
    public function softDelete(): void { $this->deletedAt = new \DateTimeImmutable(); }

    public function isDeleted(): bool { return $this->deletedAt !== null; }

    public function getCreatedBy(): User { return $this->createdBy; }

    public function setCreatedBy(User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getAssignedTo(): ?User { return $this->assignedTo; }

    public function setAssignedTo(?User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    /** @return Collection<int, Attachment> */
    public function getAttachments(): Collection { return $this->attachments; }

    public function addAttachment(Attachment $attachment): static
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setTask($this);
        }
        return $this;
    }

    public function removeAttachment(Attachment $attachment): static
    {
        $this->attachments->removeElement($attachment);
        return $this;
    }
}