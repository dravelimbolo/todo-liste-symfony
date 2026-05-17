<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\ProfileType;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        // ── Utilisateurs ─────────────────────────────────────────────

        $solo = new User();
        $solo->setEmail('solo@demo.fr')
             ->setFirstName('Marie')
             ->setLastName('Curie')
             ->setProfileType(ProfileType::SOLO)
             ->setPassword($this->passwordHasher->hashPassword($solo, 'password123'));
        $manager->persist($solo);

        $lead = new User();
        $lead->setEmail('lead@demo.fr')
             ->setFirstName('Julien')
             ->setLastName('Moreau')
             ->setProfileType(ProfileType::TEAM_LEAD)
             ->setPassword($this->passwordHasher->hashPassword($lead, 'password123'));
        $manager->persist($lead);

        // ── Tâches ───────────────────────────────────────────────────

        $tasks = [
            [
                'title'       => 'Refonte de l\'interface utilisateur du Dashboard',
                'description' => 'Finaliser les maquettes haute fidélité pour le nouveau tableau de bord. Respecter la charte graphique "Productive Calm".',
                'priority'    => TaskPriority::URGENT,
                'status'      => TaskStatus::IN_PROGRESS,
                'dueDate'     => new \DateTimeImmutable('+7 days'),
                'creator'     => $lead,
                'assignedTo'  => $solo,
            ],
            [
                'title'       => 'Audit de sécurité des endpoints API',
                'description' => 'Vérifier les points d\'entrée API et corriger les vulnérabilités identifiées.',
                'priority'    => TaskPriority::HIGH,
                'status'      => TaskStatus::TODO,
                'dueDate'     => new \DateTimeImmutable('+3 days'),
                'creator'     => $lead,
                'assignedTo'  => null,
            ],
            [
                'title'       => 'Documentation technique des services',
                'description' => 'Rédiger la documentation des AuthService, TaskService et DashboardService.',
                'priority'    => TaskPriority::MEDIUM,
                'status'      => TaskStatus::TODO,
                'dueDate'     => new \DateTimeImmutable('+14 days'),
                'creator'     => $solo,
                'assignedTo'  => null,
            ],
            [
                'title'       => 'Mise en place du pipeline CI/CD',
                'description' => 'Configurer GitHub Actions avec lint, tests et déploiement automatique.',
                'priority'    => TaskPriority::HIGH,
                'status'      => TaskStatus::DONE,
                'dueDate'     => new \DateTimeImmutable('-2 days'),
                'creator'     => $lead,
                'assignedTo'  => $lead,
            ],
            [
                'title'       => 'Revue du design système - Sprint 4',
                'description' => 'Revue des composants Tailwind et validation des tokens de design.',
                'priority'    => TaskPriority::MEDIUM,
                'status'      => TaskStatus::DONE,
                'dueDate'     => new \DateTimeImmutable('-5 days'),
                'creator'     => $solo,
                'assignedTo'  => null,
            ],
            [
                'title'       => 'Migration base de données v2',
                'description' => 'Appliquer les migrations Doctrine pour le nouveau schéma UUID.',
                'priority'    => TaskPriority::LOW,
                'status'      => TaskStatus::DONE,
                'dueDate'     => new \DateTimeImmutable('-10 days'),
                'creator'     => $lead,
                'assignedTo'  => $solo,
            ],
        ];

        foreach ($tasks as $data) {
            $task = new Task();
            $task->setTitle($data['title'])
                 ->setDescription($data['description'])
                 ->setPriority($data['priority'])
                 ->setStatus($data['status'])
                 ->setDueDate($data['dueDate'])
                 ->setCreatedBy($data['creator'])
                 ->setAssignedTo($data['assignedTo']);
            $manager->persist($task);
        }

        $manager->flush();
    }
}