<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CharacterSheet;
use App\Repository\CharacterSheetRepository;
use App\Repository\RaceRepository;
use App\Repository\ClassesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/characters')]
#[IsGranted('ROLE_USER')]
final class CharacterController extends AbstractController
{
    // ---------- LIST USER CHARACTERS ----------
    #[Route('', methods: ['GET'])]
    public function list(CharacterSheetRepository $repo): JsonResponse
    {
        return $this->json($repo->findByUser($this->getUser()));
    }

    // ---------- GET SINGLE CHARACTER ----------
    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id, CharacterSheetRepository $repo): JsonResponse
    {
        $character = $repo->findOneWithRelations($id);

        if (!$character || $character->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Not found or forbidden'], 403);
        }

        return $this->json([
            'id' => $character->getId(),
            'name' => $character->getName(),
            'level' => $character->getLevel(),
            'stats' => $character->getStats(),
            'inventory' => $character->getInventory(),
            'items' => $character->getItems(),
            'notes' => $character->getNotes(),
            'avatar_url' => $character->getAvatarUrl(),

            'race' => $character->getRace(),
            'class' => $character->getGameClass(),
        ]);
    }

    // ---------- CREATE CHARACTER ----------
    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        RaceRepository $raceRepo,
        ClassesRepository $classRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $character = new CharacterSheet();
        $character->setUser($this->getUser());
        $character->setName($data['name']);
        $character->setLevel($data['level'] ?? 1);
        $character->setStats($data['stats'] ?? []);
        $character->setInventory($data['inventory'] ?? []);
        $character->setItems($data['items'] ?? []);
        $character->setNotes($data['notes'] ?? null);
        $character->setAvatarUrl($data['avatar_url'] ?? null);

        if (isset($data['race_id'])) {
            $character->setRace($raceRepo->find($data['race_id']));
        }

        if (isset($data['class_id'])) {
            $character->setGameClass($classRepo->find($data['class_id']));
        }

        $em->persist($character);
        $em->flush();

        return $this->json(['message' => 'Character created'], 201);
    }

    // ---------- UPDATE CHARACTER ----------
    #[Route('/{id}', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        CharacterSheetRepository $repo,
        EntityManagerInterface $em,
        RaceRepository $raceRepo,
        ClassesRepository $classRepo
    ): JsonResponse {
        $character = $repo->find($id);

        if (!$character || $character->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $character->setName($data['name'] ?? $character->getName());
        $character->setLevel($data['level'] ?? $character->getLevel());
        $character->setStats($data['stats'] ?? $character->getStats());
        $character->setInventory($data['inventory'] ?? $character->getInventory());
        $character->setItems($data['items'] ?? $character->getItems());
        $character->setNotes($data['notes'] ?? $character->getNotes());
        $character->setAvatarUrl($data['avatar_url'] ?? $character->getAvatarUrl());

        if (isset($data['race_id'])) {
            $character->setRace($raceRepo->find($data['race_id']));
        }

        if (isset($data['class_id'])) {
            $character->setGameClass($classRepo->find($data['class_id']));
        }

        $em->flush();

        return $this->json(['message' => 'Character updated']);
    }

    // ---------- DELETE CHARACTER ----------
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(
        int $id,
        CharacterSheetRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $character = $repo->find($id);

        if (!$character || $character->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $em->remove($character);
        $em->flush();

        return $this->json(['message' => 'Character deleted']);
    }
}
