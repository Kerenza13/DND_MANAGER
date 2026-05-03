<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Simple validation (In a real app, use Symfony Validator)
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json(['error' => 'Missing fields'], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);

        // Hash the password
        $user->setPassword(
            $userPasswordHasher->hashPassword($user, $data['password'])
        );

        // Role Logic based on JSON boolean
        $roles = ['ROLE_USER'];
        if (!empty($data['isWorker'])) {
            $roles[] = 'ROLE_WORKER';
        }
        $user->setRoles($roles);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'status' => 'User created',
            'user' => $user->getEmail()
        ], 201);
    }
}
