<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoice')]
final class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $repo): Response
    {
        $user = $this->getUser();

        if (in_array('ROLE_WORKER', $user->getRoles())) {
            $invoices = $repo->findAll();
        } else {
            $invoices = $repo->findBy(['user' => $user]);
        }

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        // security: client only sees own invoice
        if (
            !in_array('ROLE_WORKER', $this->getUser()->getRoles()) &&
            $invoice->getUser() !== $this->getUser()
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }
    #[Route('/{id}/pdf', name: 'app_invoice_pdf', methods: ['GET'])]
    public function pdf(Invoice $invoice, EntityManagerInterface $em): Response
    {
        // Force full hydration of relations (orderRelation, orderLines, product)
        $invoice = $em->getRepository(Invoice::class)
            ->createQueryBuilder('i')
            ->leftJoin('i.orderRelation', 'o')           // Join orderRelation
            ->addSelect('o')
            ->leftJoin('o.orderLines', 'l')              // Join orderLines
            ->addSelect('l')
            ->leftJoin('l.product', 'p')                 // Join product
            ->addSelect('p')
            ->where('i.id = :id')
            ->setParameter('id', $invoice->getId())
            ->getQuery()
            ->getOneOrNullResult();

        // Options for Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');

        // Initialize Dompdf
        $dompdf = new Dompdf($options);

        // Render HTML for PDF
        $html = $this->renderView('invoice/pdf.html.twig', [
            'invoice' => $invoice,
        ]);

        // Load and render the HTML into a PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Return the generated PDF as a response
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="invoice-' . $invoice->getId() . '.pdf"',
            ]
        );
    }
}
