<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Race;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/races')]
final class RaceController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(RaceRepository $repo): JsonResponse
    {
        return $this->json($repo->findAllOrdered());
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $race = new Race();
        $race->setName($data['name']);
        $race->setDescription($data['description'] ?? null);
        $race->setStatBonuses($data['stat_bonuses'] ?? []);
        $race->setTraits($data['traits'] ?? []);

        $em->persist($race);
        $em->flush();

        return $this->json(['message' => 'Race created'], 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(
        int $id,
        Request $request,
        RaceRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $race = $repo->find($id);

        if (!$race) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $race->setName($data['name'] ?? $race->getName());
        $race->setDescription($data['description'] ?? $race->getDescription());
        $race->setStatBonuses($data['stat_bonuses'] ?? $race->getStatBonuses());
        $race->setTraits($data['traits'] ?? $race->getTraits());

        $em->flush();

        return $this->json(['message' => 'Race updated']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        int $id,
        RaceRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $race = $repo->find($id);

        if (!$race) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $em->remove($race);
        $em->flush();

        return $this->json(['message' => 'Race deleted']);
    }
}
