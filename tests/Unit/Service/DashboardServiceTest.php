<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\DashboardService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DashboardServiceTest extends TestCase
{
    private TaskRepository&MockObject $taskRepository;
    private DashboardService $dashboardService;

    protected function setUp(): void
    {
        $this->taskRepository   = $this->createMock(TaskRepository::class);
        $this->dashboardService = new DashboardService($this->taskRepository);
    }

    public function testGetStatsReturnsCorrectCompletionRate(): void
    {
        $user = $this->createMock(User::class);

        $this->taskRepository->method('countCreatedByUser')->willReturn(10);
        $this->taskRepository->method('countCompletedByUser')->willReturn(9);
        $this->taskRepository->method('countByStatusForUser')->willReturn([]);
        $this->taskRepository->method('findRecentByUser')->willReturn([]);

        $stats = $this->dashboardService->getStats($user);

        $this->assertSame(90, $stats['completionRate']);
        $this->assertSame(10, $stats['totalCreated']);
        $this->assertSame(9, $stats['totalCompleted']);
    }

    public function testGetStatsWithNoTasksReturnsZeroRate(): void
    {
        $user = $this->createMock(User::class);

        $this->taskRepository->method('countCreatedByUser')->willReturn(0);
        $this->taskRepository->method('countCompletedByUser')->willReturn(0);
        $this->taskRepository->method('countByStatusForUser')->willReturn([]);
        $this->taskRepository->method('findRecentByUser')->willReturn([]);

        $stats = $this->dashboardService->getStats($user);

        $this->assertSame(0, $stats['completionRate']);
    }

    public function testCompletionRateIsRounded(): void
    {
        $user = $this->createMock(User::class);

        $this->taskRepository->method('countCreatedByUser')->willReturn(3);
        $this->taskRepository->method('countCompletedByUser')->willReturn(1);
        $this->taskRepository->method('countByStatusForUser')->willReturn([]);
        $this->taskRepository->method('findRecentByUser')->willReturn([]);

        $stats = $this->dashboardService->getStats($user);

        $this->assertSame(33, $stats['completionRate']); // 1/3 = 33.33 → 33
    }
}