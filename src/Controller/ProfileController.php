<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ChangePasswordDTO;
use App\DTO\UpdateProfileDTO;
use App\Form\ChangePasswordFormType;
use App\Form\ProfileFormType;
use App\Service\AuthService;
use App\Service\DashboardService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('', name: 'app_profile', methods: ['GET'])]
    public function show(DashboardService $dashboardService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'stats' => $dashboardService->getStats($user),
        ]);
    }

    #[Route('/modifier', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserService $userService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $dto = UpdateProfileDTO::fromUser($user);
        $form = $this->createForm(ProfileFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userService->updateProfile($user, $dto);
            $this->addFlash('success', 'Profil mis à jour !');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'profileForm' => $form,
        ]);
    }

    #[Route('/mot-de-passe', name: 'app_profile_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, AuthService $authService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $dto = new ChangePasswordDTO();
        $form = $this->createForm(ChangePasswordFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $authService->changePassword($user, $dto->currentPassword, $dto->newPassword);
                $this->addFlash('success', 'Mot de passe changé avec succès !');

                return $this->redirectToRoute('app_profile');
            } catch (\DomainException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('profile/change_password.html.twig', [
            'changePasswordForm' => $form,
            'user' => $user,
        ]);
    }
}
