<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return $this->json([
                'error' => 'Missing email or password'
            ], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(
            $hasher->hashPassword($user, $data['password'])
        );

        // por defecto cliente
        $user->setRoles(['ROLE_CLIENT']);

        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'User created successfully'
        ], 201);
    }
}