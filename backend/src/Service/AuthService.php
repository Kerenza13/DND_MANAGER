<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Handles user business logic to keep the Controller clean.
 */
final class AuthService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * Creates and saves a new user.
     */
    public function registerUser(array $data): User
    {
        // 1. Validation Logic
        if (!isset($data['email'], $data['password'], $data['username'])) {
            throw new BadRequestHttpException('Missing email, username, or password.');
        }

        // 2. Hydrate Entity (Using your provided Entity structure)
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);

        // 3. Secure Password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // 4. Persistence
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
