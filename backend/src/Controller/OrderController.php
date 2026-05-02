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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
final class OrderController extends AbstractController
{
    #[Route(name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
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

        $orders = $qb->getQuery()->getResult();;

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    ): Response {

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $order = new Order();
            $order->setStatus('serving');
            $order->setType($data['type'] ?? 'dine_in');
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setUser($user);

            $entityManager->persist($order);

            foreach ($data['items'] ?? [] as $item) {

                if (!isset($item['product_id'], $item['quantity'])) {
                    continue;
                }

                $quantity = (int) $item['quantity'];

                if ($quantity <= 0) {
                    continue;
                }

                $product = $productRepository->find($item['product_id']);

                if (!$product || !$product->isAvalible()) {
                    continue;
                }

                $line = new OrderLine();
                $line->setProduct($product);
                $line->setQuantity($quantity);
                $line->setPrice($product->getPrice());

                // ✅ IMPORTANT: owning side
                $line->setOrderRelation($order);

                // ✅ IMPORTANT: inverse side (FIX FOR PDF EMPTY ISSUE)
                $order->addOrderLine($line);

                $entityManager->persist($line);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/new.html.twig', [
            'products' => $productRepository->createQueryBuilder('p')
                ->where('p.deletedAt IS NULL')
                ->getQuery()
                ->getResult(),
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/complete', name: 'app_order_complete', methods: ['POST'])]
    public function complete(Order $order, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_WORKER')) {
            throw $this->createAccessDeniedException();
        }

        $order->setStatus('completed');

        $existingInvoice = $entityManager
            ->getRepository(Invoice::class)
            ->findOneBy(['orderRelation' => $order]);

        if (!$existingInvoice) {

            $total = 0;

            // ✅ FIXED: use correct collection
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

        return $this->redirectToRoute('app_order_index');
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_WORKER')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(\App\Form\OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Order $order, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_WORKER')) {
            throw $this->createAccessDeniedException();
        }

        // SOFT DELETE instead of hard delete
        $order->setDeletedAt(new \DateTimeImmutable());

        $em->flush();

        return $this->redirectToRoute('app_order_index');
    }
}
