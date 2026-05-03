<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/product')]
final class ProductController extends AbstractController
{
    #[Route('', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_WORKER');

        $products = $productRepository->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->getQuery()
            ->getResult();

        return $this->json($products);
    }

    #[Route('', name: 'app_product_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_WORKER');
        $data = $request->toArray();

        $product = new Product();
        $product->setName($data['nombre']);
        $product->setPrice($data['precio']);
        $product->setisAvalible($data['avalible'] ?? true);

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json(['message' => 'Product created', 'id' => $product->getId()], 201);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(int $id, ProductRepository $productRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_WORKER');
        $product = $productRepository->find($id);

        if (!$product || $product->getDeletedAt() !== null) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        return $this->json($product);
    }

    #[Route('/{id}', name: 'app_product_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_WORKER');

        if ($product->getDeletedAt() !== null) {
            return $this->json(['error' => 'Cannot edit deleted product'], 404);
        }

        $data = $request->toArray();
        if (isset($data['nombre'])) $product->setName($data['nombre']);
        if (isset($data['precio'])) $product->setPrice($data['precio']);
        if (isset($data['avalible'])) $product->setisAvalible($data['avalible']);

        $entityManager->flush();

        return $this->json(['message' => 'Product updated']);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_WORKER');

        $product->setDeletedAt(new \DateTimeImmutable());
        $entityManager->flush();

        return $this->json(['message' => 'Product soft-deleted']);
    }
}
