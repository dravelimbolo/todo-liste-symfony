<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreateTaskDTO;
use App\Entity\Attachment;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class TaskService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepository,
        private readonly SluggerInterface $slugger,
        private readonly string $uploadDir,
    ) {
    }

    /** Crée une nouvelle tâche pour le créateur donné. */
    public function create(CreateTaskDTO $dto, User $creator): Task
    {
        $task = new Task();
        $task->setCreatedBy($creator);
        $this->hydrateFromDTO($task, $dto);

        $this->em->persist($task);
        $this->em->flush();

        return $task;
    }

    /** Met à jour une tâche existante. */
    public function update(Task $task, CreateTaskDTO $dto): Task
    {
        $this->hydrateFromDTO($task, $dto);
        $this->em->flush();

        return $task;
    }

    /**
     * Soft delete — ne supprime jamais physiquement.
     * Le filtre TaskRepository::createActiveQB() exclut les tâches supprimées.
     */
    public function delete(Task $task): void
    {
        $task->softDelete();
        $this->em->flush();
    }

    /** Hydrate l'entité depuis le DTO — méthode privée, DRY. */
    private function hydrateFromDTO(Task $task, CreateTaskDTO $dto): void
    {
        $task->setTitle($dto->title);
        $task->setDescription($dto->description);
        $task->setPriority($dto->priority);
        $task->setStatus($dto->status);
        $task->setDueDate($dto->dueDate);

        if ($dto->assignedToId) {
            $assignedUser = $this->userRepository->find($dto->assignedToId);
            $task->setAssignedTo($assignedUser);
        } else {
            $task->setAssignedTo(null);
        }

        foreach ($dto->attachments as $uploadedFile) {
            if ($uploadedFile instanceof UploadedFile) {
                $task->addAttachment($this->processUpload($uploadedFile));
            }
        }
    }

    /** Déplace le fichier et retourne une entité Attachment prête à être persistée. */
    private function processUpload(UploadedFile $file): Attachment
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalName)->lower()->toString();
        $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$file->guessExtension();

        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $size = $file->getSize();
        $clientName = $file->getClientOriginalName();

        $file->move($this->uploadDir, $newFilename);

        $attachment = new Attachment();
        $attachment->setFilename($newFilename);
        $attachment->setOriginalName($clientName);
        $attachment->setMimeType($mimeType);
        $attachment->setSize($size);

        return $attachment;
    }
}
