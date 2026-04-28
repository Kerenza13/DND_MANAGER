<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\CharacterSheet;
use App\Repository\CharacterSheetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/characters')]
final class CharacterExportController extends AbstractController
{
    #[Route('/{id}/export/pdf', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function exportPdf(int $id, CharacterSheetRepository $repo): Response
    {
        $character = $repo->findOneWithRelations($id);

        // Security check
        if (!$character || $character->getUser() !== $this->getUser()) {
            return new Response('Forbidden', 403);
        }

        // ---------- HTML TEMPLATE ----------
        $html = '
        <h1>' . htmlspecialchars($character->getName()) . '</h1>

        <p><strong>Level:</strong> ' . $character->getLevel() . '</p>
        <p><strong>Race:</strong> ' . ($character->getRace()?->getName() ?? '-') . '</p>
        <p><strong>Class:</strong> ' . ($character->getGameClass()?->getName() ?? '-') . '</p>

        <h2>Stats</h2>
        <pre>' . json_encode($character->getStats(), JSON_PRETTY_PRINT) . '</pre>

        <h2>Inventory</h2>
        <pre>' . json_encode($character->getInventory(), JSON_PRETTY_PRINT) . '</pre>

        <h2>Items</h2>
        <pre>' . json_encode($character->getItems(), JSON_PRETTY_PRINT) . '</pre>

        <h2>Notes</h2>
        <p>' . nl2br(htmlspecialchars($character->getNotes() ?? '')) . '</p>
        ';

        // ---------- DOMPDF ----------
        $options = new Options();
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        // ---------- RESPONSE ----------
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="character_' . $character->getId() . '.pdf"',
            ]
        );
    }
}
