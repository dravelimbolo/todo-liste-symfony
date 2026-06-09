<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreateTaskDTO;
use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/taches')]
#[IsGranted('ROLE_USER')]
class TaskController extends AbstractController
{
    #[Route('', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $repo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('task/index.html.twig', [
            'tasks' => $repo->findActiveByUser($user),
        ]);
    }

    #[Route('/nouvelle', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TaskService $taskService): Response
    {
        $dto = new CreateTaskDTO();
        $form = $this->createForm(TaskFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Les fichiers ne sont pas mappés automatiquement (mapped: false)
            $uploadedFiles = $form->get('attachments')->getData();
            $dto->attachments = is_array($uploadedFiles) ? $uploadedFiles : [];

            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $task = $taskService->create($dto, $user);

            $this->addFlash('success', 'Tâche créée avec succès !');

            return $this->redirectToRoute('app_task_show', ['id' => $task->getId()]);
        }

        return $this->render('task/new.html.twig', [
            'taskForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::VIEW, $task);

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, TaskService $taskService): Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::EDIT, $task);

        $dto = CreateTaskDTO::fromTask($task);
        $form = $this->createForm(TaskFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFiles = $form->get('attachments')->getData();
            $dto->attachments = is_array($uploadedFiles) ? $uploadedFiles : [];

            $taskService->update($task, $dto);

            $this->addFlash('success', 'Tâche mise à jour !');

            return $this->redirectToRoute('app_task_show', ['id' => $task->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'taskForm' => $form,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, TaskService $taskService): Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::DELETE, $task);

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $taskService->delete($task);
            $this->addFlash('success', 'Tâche supprimée.');
        }

        return $this->redirectToRoute('app_task_index');
    }
}
