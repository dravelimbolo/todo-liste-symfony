<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AttachmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
#[ORM\Table(name: 'attachments')]
class Attachment
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private string $filename;

    #[ORM\Column(length: 255)]
    private string $originalName;

    #[ORM\Column(length: 100)]
    private string $mimeType;

    #[ORM\Column]
    private int $size;

    #[ORM\Column]
    private \DateTimeImmutable $uploadedAt;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Task $task;

    public function __construct()
    {
        $this->id         = Uuid::v7();
        $this->uploadedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid { return $this->id; }
    public function getFilename(): string { return $this->filename; }
    public function setFilename(string $filename): static { $this->filename = $filename; return $this; }
    public function getOriginalName(): string { return $this->originalName; }
    public function setOriginalName(string $n): static { $this->originalName = $n; return $this; }
    public function getMimeType(): string { return $this->mimeType; }
    public function setMimeType(string $m): static { $this->mimeType = $m; return $this; }
    public function getSize(): int { return $this->size; }
    public function setSize(int $size): static { $this->size = $size; return $this; }
    public function getUploadedAt(): \DateTimeImmutable { return $this->uploadedAt; }
    public function getTask(): Task { return $this->task; }
    public function setTask(Task $task): static { $this->task = $task; return $this; }
}