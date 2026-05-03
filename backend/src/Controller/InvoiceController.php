<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/invoice')]
final class InvoiceController extends AbstractController
{
    #[Route('', name: 'app_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $invoices = in_array('ROLE_WORKER', $user->getRoles())
            ? $repo->findAll()
            : $repo->findBy(['user' => $user]);

        return $this->json($invoices);
    }

    #[Route('/{id}/pdf', name: 'app_invoice_pdf', methods: ['GET'])]
    public function pdf(int $id, EntityManagerInterface $em): Response
    {
        $invoice = $em->getRepository(Invoice::class)->createQueryBuilder('i')
            ->leftJoin('i.orderRelation', 'o')->addSelect('o')
            ->leftJoin('o.orderLines', 'l')->addSelect('l')
            ->leftJoin('l.product', 'p')->addSelect('p')
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$invoice) return $this->json(['error' => 'Not found'], 404);

        if (!$this->isGranted('ROLE_WORKER') && $invoice->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Note: You still need a twig file ONLY for the PDF generation
        $html = $this->renderView('invoice/pdf.html.twig', ['invoice' => $invoice]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
}
