<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\CreateTaskDTO;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Repository\UserRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TaskServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private UserRepository&MockObject $userRepository;
    private TaskService $taskService;
    private string $uploadDir;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->uploadDir = sys_get_temp_dir().'/task_test_'.uniqid();

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $this->taskService = new TaskService(
            $this->em,
            $this->userRepository,
            new AsciiSlugger(),
            $this->uploadDir,
        );
    }

    protected function tearDown(): void
    {
        foreach (glob($this->uploadDir.'/*') ?: [] as $file) {
            unlink($file);
        }
        if (is_dir($this->uploadDir)) {
            rmdir($this->uploadDir);
        }
    }

    // create()

    public function testCreateReturnsTaskInstance(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $task = $this->taskService->create(
            $this->buildDTO('Tâche de test'),
            $this->createMock(User::class)
        );

        $this->assertInstanceOf(Task::class, $task);
    }

    public function testCreateCallsPersistOnce(): void
    {
        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $this->taskService->create(
            $this->buildDTO('Tâche de test'),
            $this->createMock(User::class)
        );
    }

    public function testCreateSetsTitle(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $task = $this->taskService->create(
            $this->buildDTO('Mon titre'),
            $this->createMock(User::class)
        );

        $this->assertSame('Mon titre', $task->getTitle());
    }

    public function testCreateSetsDescription(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $dto = $this->buildDTO('Titre');
        $dto->description = 'Description détaillée';

        $task = $this->taskService->create($dto, $this->createMock(User::class));

        $this->assertSame('Description détaillée', $task->getDescription());
    }

    public function testCreateSetsPriority(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $dto = $this->buildDTO('Titre');
        $dto->priority = TaskPriority::URGENT;

        $task = $this->taskService->create($dto, $this->createMock(User::class));

        $this->assertSame(TaskPriority::URGENT, $task->getPriority());
    }

    public function testCreateSetsStatus(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $dto = $this->buildDTO('Titre');
        $dto->status = TaskStatus::IN_PROGRESS;

        $task = $this->taskService->create($dto, $this->createMock(User::class));

        $this->assertSame(TaskStatus::IN_PROGRESS, $task->getStatus());
    }

    public function testCreateSetsDueDate(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $dto = $this->buildDTO('Titre');
        $dto->dueDate = new \DateTimeImmutable('2025-12-31');

        $task = $this->taskService->create($dto, $this->createMock(User::class));

        $this->assertEquals(new \DateTimeImmutable('2025-12-31'), $task->getDueDate());
    }

    public function testCreateSetsCreatedBy(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $creator = $this->createMock(User::class);
        $task = $this->taskService->create($this->buildDTO('Titre'), $creator);

        $this->assertSame($creator, $task->getCreatedBy());
    }

    public function testCreateDefaultPriorityIsMedium(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $task = $this->taskService->create(
            $this->buildDTO('Titre'),
            $this->createMock(User::class)
        );

        $this->assertSame(TaskPriority::MEDIUM, $task->getPriority());
    }

    public function testCreateDefaultStatusIsTodo(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $task = $this->taskService->create(
            $this->buildDTO('Titre'),
            $this->createMock(User::class)
        );

        $this->assertSame(TaskStatus::TODO, $task->getStatus());
    }

    public function testCreateCallsPersistThenFlush(): void
    {
        $callOrder = [];

        $this->em->method('persist')->willReturnCallback(
            static function () use (&$callOrder): void {
                $callOrder[] = 'persist';
            }
        );

        $this->em->method('flush')->willReturnCallback(
            static function () use (&$callOrder): void {
                $callOrder[] = 'flush';
            }
        );

        $this->taskService->create($this->buildDTO('Titre'), $this->createMock(User::class));

        $this->assertSame(['persist', 'flush'], $callOrder);
    }

    public function testCreateWithAssignedUser(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $assignedUser = $this->createMock(User::class);
        $this->userRepository->method('find')->willReturn($assignedUser);

        $dto = $this->buildDTO('Titre');
        $dto->assignedToId = 'uuid-assigné';

        $task = $this->taskService->create($dto, $this->createMock(User::class));

        $this->assertSame($assignedUser, $task->getAssignedTo());
    }

    public function testCreateWithoutAssignedIdSetsNull(): void
    {
        $this->em->method('persist');
        $this->em->method('flush');

        $dto = $this->buildDTO('Titre');
        $dto->assignedToId = null;

        $task = $this->taskService->create($dto, $this->createMock(User::class));

        $this->assertNull($task->getAssignedTo());
    }

    // update()

    public function testUpdateChangesTitle(): void
    {
        $this->em->method('flush');

        $task = $this->buildTask('Ancien titre');
        $result = $this->taskService->update($task, $this->buildDTO('Nouveau titre'));

        $this->assertSame('Nouveau titre', $result->getTitle());
    }

    public function testUpdateChangesPriority(): void
    {
        $this->em->method('flush');

        $dto = $this->buildDTO('Titre');
        $dto->priority = TaskPriority::HIGH;

        $result = $this->taskService->update($this->buildTask('Titre'), $dto);

        $this->assertSame(TaskPriority::HIGH, $result->getPriority());
    }

    public function testUpdateChangesStatus(): void
    {
        $this->em->method('flush');

        $dto = $this->buildDTO('Titre');
        $dto->status = TaskStatus::DONE;

        $result = $this->taskService->update($this->buildTask('Titre'), $dto);

        $this->assertSame(TaskStatus::DONE, $result->getStatus());
    }

    public function testUpdateNeverCallsPersist(): void
    {
        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $this->taskService->update($this->buildTask('Titre'), $this->buildDTO('Nouveau'));
    }

    public function testUpdateDoesNotChangeCreatedBy(): void
    {
        $this->em->method('flush');

        $creator = $this->createMock(User::class);
        $task = $this->buildTask('Titre');
        $task->setCreatedBy($creator);

        $this->taskService->update($task, $this->buildDTO('Nouveau titre'));

        $this->assertSame($creator, $task->getCreatedBy());
    }

    public function testUpdateClearsAssignedToWhenIdIsNull(): void
    {
        $this->em->method('flush');

        $task = $this->buildTask('Titre');
        $task->setAssignedTo($this->createMock(User::class));

        $dto = $this->buildDTO('Titre');
        $dto->assignedToId = null;

        $this->taskService->update($task, $dto);

        $this->assertNull($task->getAssignedTo());
    }

    public function testUpdateReturnsSameTaskInstance(): void
    {
        $this->em->method('flush');

        $task = $this->buildTask('Titre');
        $result = $this->taskService->update($task, $this->buildDTO('Nouveau'));

        $this->assertSame($task, $result);
    }

    // delete() — Soft Delete

    public function testDeleteSetsDeletedAt(): void
    {
        $this->em->method('flush');

        $task = $this->buildTask('Tâche');
        $this->assertNull($task->getDeletedAt());

        $this->taskService->delete($task);

        $this->assertNotNull($task->getDeletedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getDeletedAt());
    }

    public function testDeleteMarksTaskAsDeleted(): void
    {
        $this->em->method('flush');

        $task = $this->buildTask('Tâche');
        $this->assertFalse($task->isDeleted());

        $this->taskService->delete($task);

        $this->assertTrue($task->isDeleted());
    }

    public function testDeleteNeverCallsRemove(): void
    {
        $this->em->expects($this->never())->method('remove');
        $this->em->expects($this->once())->method('flush');

        $this->taskService->delete($this->buildTask('Tâche'));
    }

    public function testDeleteCallsFlushOnce(): void
    {
        $this->em->expects($this->once())->method('flush');

        $this->taskService->delete($this->buildTask('Tâche'));
    }

    public function testDeleteTimestampIsApproximatelyNow(): void
    {
        $this->em->method('flush');

        $task = $this->buildTask('Tâche');
        $before = new \DateTimeImmutable();

        $this->taskService->delete($task);

        $after = new \DateTimeImmutable();
        $deletedAt = $task->getDeletedAt();

        $this->assertNotNull($deletedAt, 'softDelete() doit définir deletedAt');
        $this->assertGreaterThanOrEqual($before->getTimestamp(), $deletedAt->getTimestamp());
        $this->assertLessThanOrEqual($after->getTimestamp(), $deletedAt->getTimestamp());
    }

    // Helpers privés

    private function buildDTO(string $title): CreateTaskDTO
    {
        $dto = new CreateTaskDTO();
        $dto->title = $title;
        $dto->priority = TaskPriority::MEDIUM;
        $dto->status = TaskStatus::TODO;
        $dto->attachments = [];

        return $dto;
    }

    private function buildTask(string $title): Task
    {
        $task = new Task();
        $task->setTitle($title);
        $task->setPriority(TaskPriority::MEDIUM);
        $task->setStatus(TaskStatus::TODO);
        $task->setCreatedBy($this->createMock(User::class));

        return $task;
    }
}
