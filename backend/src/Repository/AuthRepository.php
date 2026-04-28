<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthRepository
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {}

    public function register(array $data): array
    {
        if (!isset($data['email'], $data['password'], $data['username'])) {
            return ['error' => 'Missing fields', 'status' => 400];
        }

        if (strlen($data['password']) < 6) {
            return ['error' => 'Password must be at least 6 characters', 'status' => 400];
        }

        if ($this->userRepository->findOneBy(['email' => $data['email']])) {
            return ['error' => 'Email already exists', 'status' => 400];
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setPassword($this->hasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return ['message' => 'User created', 'status' => 201];
    }

    public function login(array $data, SessionInterface $session): array
    {
        if (!isset($data['email'], $data['password'])) {
            return ['error' => 'Missing credentials', 'status' => 400];
        }

        $user = $this->userRepository->findOneBy(['email' => $data['email']]);

        if (!$user || !$this->hasher->isPasswordValid($user, $data['password'])) {
            return ['error' => 'Invalid credentials', 'status' => 401];
        }

        $session->set('user_id', $user->getId());

        return [
            'message' => 'Login successful',
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
            'status' => 200
        ];
    }

    public function logout(SessionInterface $session): void
    {
        $session->invalidate();
    }
}
