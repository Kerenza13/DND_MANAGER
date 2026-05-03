<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Invoice;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/order')]
final class OrderController extends AbstractController
{
    #[Route('', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): JsonResponse
    {
        $user = $this->getUser();
        $qb = $orderRepository->createQueryBuilder('o');

        if (in_array('ROLE_WORKER', $user->getRoles())) {
            $qb->where('o.deletedAt IS NULL');
        } else {
            $qb->where('o.deletedAt IS NULL')
                ->andWhere('o.user = :user')
                ->setParameter('user', $user);
        }

        return $this->json($qb->getQuery()->getResult());
    }

    #[Route('', name: 'app_order_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) return $this->json(['error' => 'Auth required'], 401);

        $data = $request->toArray();
        $order = new Order();
        $order->setStatus('serving');
        $order->setType($data['type'] ?? 'dine_in');
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setUser($user);

        $entityManager->persist($order);

        foreach ($data['items'] ?? [] as $item) {
            $product = $productRepository->find($item['product_id']);

            if (!$product || !$product->isAvalible()) continue;

            $line = new OrderLine();
            $line->setProduct($product);
            $line->setQuantity((int)$item['quantity']);
            $line->setPrice($product->getPrice());
            $line->setOrderRelation($order);

            $order->addOrderLine($line);
            $entityManager->persist($line);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Order created', 'id' => $order->getId()], 201);
    }

    #[Route('/{id}/complete', name: 'app_order_complete', methods: ['POST'])]
    public function complete(Order $order, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isGranted('ROLE_WORKER')) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $order->setStatus('completed');

        $existingInvoice = $entityManager->getRepository(Invoice::class)
            ->findOneBy(['orderRelation' => $order]);

        if (!$existingInvoice) {
            $total = 0;
            foreach ($order->getOrderLines() as $line) {
                $total += $line->getPrice() * $line->getQuantity();
            }

            $invoice = new Invoice();
            $invoice->setOrderRelation($order);
            $invoice->setUser($order->getUser());
            $invoice->setTotal($total);
            $invoice->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($invoice);
        }

        $entityManager->flush();
        return $this->json(['message' => 'Order completed and invoiced']);
    }
}
