<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/api/dashboard', name: 'api_dashboard', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Not authenticated'], 401);
        }

        // Instead of rendering a template, return boolean flags
        // so React can decide what to show/hide.
        return $this->json([
            'isAuthenticated' => true,
            'user' => $user->getUserIdentifier(),
            'permissions' => [
                'can_view_products' => in_array('ROLE_WORKER', $user->getRoles()),
            ],
        ]);
    }
}
