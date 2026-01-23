<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\PdfService;

class PdfController extends AbstractController
{
    public function print(PdfService $pdfService): Response
    {
        $contentHtml = $this->renderView('pdf/content.html.twig');
        $headerHtml = $this->renderView('pdf/header.html.twig');
        $footerHtml = $this->renderView('pdf/footer.html.twig');

        $pdfContent = $pdfService->generatePdf($contentHtml, $headerHtml, $footerHtml);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }
}
