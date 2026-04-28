<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
final class AuthController extends AbstractController
{
    /**
     * Endpoint for user registration.
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, AuthService $authService): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        try {
            $user = $authService->registerUser($data);
            return $this->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user' => $user->getUserIdentifier()
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * This route is intercepted by the 'json_login' firewall.
     * The code inside only runs if authentication is successful.
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }

    /**
     * Intercepted by the 'logout' firewall key.
     */
    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): void
    {
        // Symfony handles session invalidation automatically.
        throw new \LogicException('This should never be reached.');
    }

    /**
     * Useful for React to check if the session is still valid on page load.
     */
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return $this->json(['status' => 'ok', 'timestamp' => time()]);
    }
}
