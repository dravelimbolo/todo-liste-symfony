<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(DashboardService $dashboardService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'stats' => $dashboardService->getStats($user),
        ]);
    }
}
