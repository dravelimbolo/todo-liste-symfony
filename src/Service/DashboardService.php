<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\TaskRepository;

final class DashboardService
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
    ) {}

    /**
     * Calcule toutes les statistiques du dashboard pour un utilisateur.
     *
     * @return array{
     *   totalCreated: int,
     *   totalCompleted: int,
     *   completionRate: int,
     *   byStatus: array<string, int>,
     *   recentTasks: list<\App\Entity\Task>
     * }
     */
    public function getStats(User $user): array
    {
        $totalCreated   = $this->taskRepository->countCreatedByUser($user);
        $totalCompleted = $this->taskRepository->countCompletedByUser($user);
        $byStatus       = $this->taskRepository->countByStatusForUser($user);
        $recentTasks    = $this->taskRepository->findRecentByUser($user, 5);

        $completionRate = $totalCreated > 0
            ? (int) round(($totalCompleted / $totalCreated) * 100)
            : 0;

        return [
            'totalCreated'   => $totalCreated,
            'totalCompleted' => $totalCompleted,
            'completionRate' => $completionRate,
            'byStatus'       => $byStatus,
            'recentTasks'    => $recentTasks,
        ];
    }
}