<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

/** @extends ServiceEntityRepository<Task> */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /** @return Task[] */
    public function findActiveByUser(User $user): array
    {
        return $this->createActiveQB()
            ->andWhere('t.createdBy = :user OR t.assignedTo = :user')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<Task> */
    public function findRecentByUser(User $user, int $limit = 5): array
    {
        return $this->createActiveQB()
            ->andWhere('t.createdBy = :user')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countCreatedByUser(User $user): int
    {
        return (int) $this->createActiveQB()
            ->select('COUNT(t.id)')
            ->andWhere('t.createdBy = :user')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countCompletedByUser(User $user): int
    {
        return (int) $this->createActiveQB()
            ->select('COUNT(t.id)')
            ->andWhere('t.createdBy = :user')
            ->andWhere('t.status = :status')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->setParameter('status', TaskStatus::DONE->value)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /** @return array<string, int> */
    public function countByStatusForUser(User $user): array
    {
        $result = [];
        foreach (TaskStatus::cases() as $status) {
            $result[$status->value] = (int) $this->createActiveQB()
                ->select('COUNT(t.id)')
                ->andWhere('t.createdBy = :user')
                ->andWhere('t.status = :status')
                ->setParameter('user', $user->getId(), UuidType::NAME)
                ->setParameter('status', $status->value)
                ->getQuery()
                ->getSingleScalarResult();
        }
        return $result;
    }

    /** Filtre automatique sur les non-supprimés (soft delete) */
    private function createActiveQB(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.deletedAt IS NULL');
    }
}