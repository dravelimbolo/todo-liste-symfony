<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Gère les permissions sur les tâches.
 *
 * VIEW   : créateur OU assigné
 * EDIT   : créateur uniquement
 * DELETE : créateur uniquement
 *
 * @extends Voter<string, Task>
 */
final class TaskVoter extends Voter
{
    public const VIEW = 'task.view';
    public const EDIT = 'task.edit';
    public const DELETE = 'task.delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::VIEW,
            self::EDIT,
            self::DELETE,
        ], true) && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($task, $user),
            self::EDIT => $this->canEdit($task, $user),
            self::DELETE => $this->canDelete($task, $user),
            default => false,
        };
    }

    private function canView(Task $task, User $user): bool
    {
        return $task->getCreatedBy() === $user || $task->getAssignedTo() === $user;
    }

    private function canEdit(Task $task, User $user): bool
    {
        return $task->getCreatedBy() === $user;
    }

    private function canDelete(Task $task, User $user): bool
    {
        return $task->getCreatedBy() === $user;
    }
}
