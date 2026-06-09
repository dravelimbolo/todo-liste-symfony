<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\ProfileType;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Form as DomForm;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser          $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em     = static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        $this->em->createQuery(
            'DELETE FROM App\Entity\Task t WHERE t.createdBy IN (
                SELECT u FROM App\Entity\User u WHERE u.email LIKE :prefix
            )'
        )->setParameter('prefix', 'test_%')->execute();

        $this->em->createQuery(
            'DELETE FROM App\Entity\User u WHERE u.email LIKE :prefix'
        )->setParameter('prefix', 'test_%')->execute();

        parent::tearDown();
    }

    // ══════════════════════════════════════════════════════════
    // Accès non authentifié → redirection
    // ══════════════════════════════════════════════════════════

    public function testTaskIndexRequiresAuthentication(): void
    {
        $this->client->request('GET', '/taches');

        $this->assertResponseRedirects('/connexion');
    }

    public function testTaskNewRequiresAuthentication(): void
    {
        $this->client->request('GET', '/taches/nouvelle');

        $this->assertResponseRedirects('/connexion');
    }

    public function testTaskShowRequiresAuthentication(): void
    {
        $user = $this->createTestUser('test_auth_show@example.com');
        $task = $this->createTestTask('Tâche protégée', $user);

        $this->client->request('GET', '/taches/' . $task->getId());

        $this->assertResponseRedirects('/connexion');
    }

    // ══════════════════════════════════════════════════════════
    // Liste des tâches
    // ══════════════════════════════════════════════════════════

    public function testTaskIndexLoadsForAuthenticatedUser(): void
    {
        $user = $this->createTestUser('test_index@example.com');
        $this->client->loginUser($user);

        $this->client->request('GET', '/taches');

        $this->assertResponseIsSuccessful();
    }

    public function testTaskIndexShowsUserOwnTasks(): void
    {
        $user = $this->createTestUser('test_list@example.com');
        $this->createTestTask('Tâche visible', $user);
        $this->client->loginUser($user);

        $this->client->request('GET', '/taches');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Tâche visible');
    }

    public function testTaskIndexDoesNotShowOtherUsersTasks(): void
    {
        $owner = $this->createTestUser('test_owner_list@example.com');
        $other = $this->createTestUser('test_other_list@example.com');
        $this->createTestTask('Tâche du propriétaire', $owner);
        $this->client->loginUser($other);

        $this->client->request('GET', '/taches');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextNotContains('body', 'Tâche du propriétaire');
    }

    public function testTaskIndexShowsEmptyState(): void
    {
        $user = $this->createTestUser('test_empty@example.com');
        $this->client->loginUser($user);

        $this->client->request('GET', '/taches');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Aucune tâche');
    }

    // ══════════════════════════════════════════════════════════
    // Formulaire de création (GET)
    // ══════════════════════════════════════════════════════════

    public function testNewTaskFormLoads(): void
    {
        $user = $this->createTestUser('test_newform@example.com');
        $this->client->loginUser($user);

        $this->client->request('GET', '/taches/nouvelle');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[name*="[title]"]');
        $this->assertSelectorExists('button[type="submit"]');
    }

    // ══════════════════════════════════════════════════════════
    // Création (POST)
    // ══════════════════════════════════════════════════════════

    public function testNewTaskSubmitRedirectsOnSuccess(): void
    {
        $user = $this->createTestUser('test_create@example.com');
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/taches/nouvelle');
        $form    = $crawler->selectButton('Créer la tâche')->form();

        $form[$this->fieldName($form, 'title')]    = 'Nouvelle tâche fonctionnelle';
        $form[$this->fieldName($form, 'priority')] = TaskPriority::MEDIUM->value;
        $form[$this->fieldName($form, 'status')]   = TaskStatus::TODO->value;

        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', 'Nouvelle tâche fonctionnelle');
    }

    public function testNewTaskWithEmptyTitleStaysOnForm(): void
    {
        $user = $this->createTestUser('test_invalid@example.com');
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/taches/nouvelle');
        $form    = $crawler->selectButton('Créer la tâche')->form();

        $form[$this->fieldName($form, 'title')] = '';

        $this->client->submit($form);

        // Symfony 7 retourne 422 quand la validation du formulaire échoue
        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorExists('input[name*="[title]"]');
    }

    // ══════════════════════════════════════════════════════════
    // Détail d'une tâche (GET)
    // ══════════════════════════════════════════════════════════

    public function testTaskShowLoadsForOwner(): void
    {
        $user = $this->createTestUser('test_show@example.com');
        $task = $this->createTestTask('Ma tâche détail', $user);
        $this->client->loginUser($user);

        $this->client->request('GET', '/taches/' . $task->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Ma tâche détail');
    }

    public function testTaskShowReturnsForbiddenForNonOwner(): void
    {
        $owner = $this->createTestUser('test_show_owner@example.com');
        $other = $this->createTestUser('test_show_other@example.com');
        $task  = $this->createTestTask('Tâche privée', $owner);
        $this->client->loginUser($other);

        $this->client->request('GET', '/taches/' . $task->getId());

        $this->assertResponseStatusCodeSame(403);
    }

    public function testTaskShowDisplaysPriorityBadge(): void
    {
        $user = $this->createTestUser('test_priority@example.com');
        $task = $this->createTestTask('Tâche urgente', $user, TaskPriority::URGENT);
        $this->client->loginUser($user);

        $this->client->request('GET', '/taches/' . $task->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'URGENTE');
    }

    public function testTaskShowDisplaysStatusBadge(): void
    {
        $user = $this->createTestUser('test_status@example.com');
        $task = $this->createTestTask('Tâche en cours', $user, TaskPriority::MEDIUM, TaskStatus::IN_PROGRESS);
        $this->client->loginUser($user);

        $this->client->request('GET', '/taches/' . $task->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'En cours');
    }

    // ══════════════════════════════════════════════════════════
    // Formulaire d'édition (GET)
    // ══════════════════════════════════════════════════════════

    public function testEditFormLoadsForOwner(): void
    {
        $user = $this->createTestUser('test_editform@example.com');
        $task = $this->createTestTask('Tâche à modifier', $user);
        $this->client->loginUser($user);

        $this->client->request('GET', '/taches/' . $task->getId() . '/modifier');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[name*="[title]"]');
    }

    public function testEditFormForbiddenForNonOwner(): void
    {
        $owner = $this->createTestUser('test_edit_owner@example.com');
        $other = $this->createTestUser('test_edit_other@example.com');
        $task  = $this->createTestTask('Tâche protégée', $owner);
        $this->client->loginUser($other);

        $this->client->request('GET', '/taches/' . $task->getId() . '/modifier');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditFormPreFillsTitleFromEntity(): void
    {
        $user = $this->createTestUser('test_prefill@example.com');
        $task = $this->createTestTask('Titre pré-rempli', $user);
        $this->client->loginUser($user);

        $crawler    = $this->client->request('GET', '/taches/' . $task->getId() . '/modifier');
        $titleInput = $crawler->filter('input[name*="[title]"]');

        $this->assertSame('Titre pré-rempli', $titleInput->attr('value'));
    }

    // ══════════════════════════════════════════════════════════
    // Modification (POST)
    // ══════════════════════════════════════════════════════════

    public function testEditTaskUpdatesTitle(): void
    {
        $user = $this->createTestUser('test_update@example.com');
        $task = $this->createTestTask('Titre original', $user);
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/taches/' . $task->getId() . '/modifier');
        $form    = $crawler->selectButton('Enregistrer les modifications')->form();

        $form[$this->fieldName($form, 'title')] = 'Titre modifié';

        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', 'Titre modifié');
    }

    // ══════════════════════════════════════════════════════════
    // Suppression (Soft Delete)
    // ══════════════════════════════════════════════════════════

    public function testDeleteForbiddenForNonOwner(): void
    {
        $owner = $this->createTestUser('test_del_owner@example.com');
        $other = $this->createTestUser('test_del_other@example.com');
        $task  = $this->createTestTask('Tâche protégée', $owner);
        $this->client->loginUser($other);

        $this->client->request('POST', '/taches/' . $task->getId() . '/supprimer', [
            '_token' => 'token_invalide',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteWithInvalidCsrfDoesNotSoftDelete(): void
    {
        $user = $this->createTestUser('test_csrf@example.com');
        $task = $this->createTestTask('Tâche CSRF', $user);
        $this->client->loginUser($user);

        $this->client->request('POST', '/taches/' . $task->getId() . '/supprimer', [
            '_token' => 'token_completement_invalide',
        ]);

        $this->assertResponseRedirects('/taches');

        $this->em->clear();
        $taskInDb = $this->em->getRepository(Task::class)->find($task->getId());
        $this->assertNull($taskInDb?->getDeletedAt(), 'Token invalide → pas de soft delete');
    }

    public function testDeleteWithValidCsrfSoftDeletesTask(): void
    {
        $user   = $this->createTestUser('test_softdel@example.com');
        $task   = $this->createTestTask('Tâche à supprimer', $user);
        $taskId = $task->getId()->toString();
        $this->client->loginUser($user);

        $crawler   = $this->client->request('GET', '/taches/' . $taskId);
        $csrfToken = $crawler->filter('input[name="_token"]')->attr('value');

        $this->client->request('POST', '/taches/' . $taskId . '/supprimer', [
            '_token' => $csrfToken,
        ]);

        $this->assertResponseRedirects('/taches');

        $this->em->clear();
        $taskInDb = $this->em->find(Task::class, $taskId);
        $this->assertInstanceOf(Task::class, $taskInDb);
        $this->assertNotNull($taskInDb->getDeletedAt(), 'Token valide → deletedAt doit être défini');
    }

    public function testSoftDeletedTaskNotVisibleInList(): void
    {
        $user = $this->createTestUser('test_invisible@example.com');
        $task = $this->createTestTask('Tâche soft-deletée', $user);
        $this->client->loginUser($user);

        $task->softDelete();
        $this->em->flush();

        $this->client->request('GET', '/taches');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextNotContains('body', 'Tâche soft-deletée');
    }

    // ══════════════════════════════════════════════════════════
    // Helpers privés
    // ══════════════════════════════════════════════════════════

    private function createTestUser(string $email): User
    {
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail($email)
             ->setFirstName('Test')
             ->setLastName('User')
             ->setProfileType(ProfileType::SOLO)
             ->setPassword($hasher->hashPassword($user, 'password123'));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function createTestTask(
        string       $title    = 'Tâche de test',
        ?User        $creator  = null,
        TaskPriority $priority = TaskPriority::MEDIUM,
        TaskStatus   $status   = TaskStatus::TODO,
    ): Task {
        if ($creator === null) {
            $creator = $this->createTestUser('test_auto_' . uniqid() . '@example.com');
        }

        $task = new Task();
        $task->setTitle($title)
             ->setPriority($priority)
             ->setStatus($status)
             ->setCreatedBy($creator);

        $this->em->persist($task);
        $this->em->flush();

        return $task;
    }

    /**
     * Retrouve le nom complet d'un champ de formulaire Symfony depuis sa clé partielle.
     * Ex: 'title' → 'task_form[title]'
     */
    private function fieldName(DomForm $form, string $key): string
    {
        foreach (array_keys($form->getValues()) as $name) {
            if (str_contains((string) $name, "[{$key}]")) {
                return (string) $name;
            }
        }

        throw new \RuntimeException(sprintf(
            "Champ '%s' introuvable. Champs disponibles : %s",
            $key,
            implode(', ', array_keys($form->getValues()))
        ));
    }
}