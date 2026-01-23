<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Asset\Packages;

class PdfService
{
    private $dompdf;
    private $packages;

    public function __construct(Packages $packages)
    {
        $this->packages = $packages;
        $this->dompdf = new Dompdf();
        $pdfOptions = new Options();
        
        // Configuration des options DomPDF
        $pdfOptions->set('defaultFont', 'Garamond');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('isPhpEnabled', true); // Essentiel pour les callbacks
        $pdfOptions->set('isJavascriptEnabled', true);
        $pdfOptions->set('isFontSubsettingEnabled', true);
        $pdfOptions->set('PdfLoadSettings', true);
        
        $this->dompdf->setOptions($pdfOptions);
    }

    public function generatePdf($html, array $options = [])
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');

        // Configuration des callbacks pour header/footer
        $this->dompdf->setCallbacks([
            'event' => function($event, $canvas) use ($options) {
                if ($event === 'begin_page' && !empty($options['header_html'])) {
                    $canvas->page_script('
                        $pdf->set_opacity(1);
                        $pdf->text(30, 30, "' . addslashes($options['header_html']) . '", null, 10);
                    ');
                }
                if ($event === 'end_page' && !empty($options['footer_html'])) {
                    $canvas->page_script('
                        $footerContent = "' . addslashes($options['footer_html']) . '";
                        $font = $fontMetrics->getFont("Helvetica");
                        $size = 10;
                        $width = $fontMetrics->getTextWidth($footerContent, $font, $size);
                        $x = ($pdf->get_width() - $width) / 2;
                        $y = $pdf->get_height() - 20;
                        $pdf->text($x, $y, $footerContent, $font, $size);
                        
                        // Numérotation des pages (optionnelle)
                        if (' . (!empty($options['show_page_numbers']) ? 'true' : 'false') . ') {
                            $pageText = "Page $PAGE_NUM/$PAGE_COUNT";
                            $pdf->text(30, $y, $pageText, $font, $size);
                        }
                    ');
                }
            }
        ]);

        $this->dompdf->render();
        
        // Stream le PDF directement au navigateur
        $this->dompdf->stream($options['filename'] ?? 'document.pdf', [
            'Attachment' => $options['attachment'] ?? 0
        ]);
        exit;
    }

    public function generateBinaryPdf($html, array $options = []): string
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($options['paper_size'] ?? 'A4', $options['orientation'] ?? 'portrait');

        // Configuration des callbacks (identique à generatePdf)
        $this->dompdf->setCallbacks([
            'event' => function($event, $canvas) use ($options) {
                if ($event === 'begin_page' && !empty($options['header_html'])) {
                    $canvas->page_script('
                        $pdf->set_opacity(1);
                        $pdf->text(30, 30, "' . addslashes($options['header_html']) . '", null, 10);
                    ');
                }
                if ($event === 'end_page' && !empty($options['footer_html'])) {
                    $canvas->page_script('
                        $footerContent = "' . addslashes($options['footer_html']) . '";
                        $font = $fontMetrics->getFont("Helvetica");
                        $size = 10;
                        $width = $fontMetrics->getTextWidth($footerContent, $font, $size);
                        $x = ($pdf->get_width() - $width) / 0;
                        $y = $pdf->get_height() - 0;
                        $pdf->text($x, $y, $footerContent, $font, $size);
                    ');
                }
            }
        ]);

        $this->dompdf->render();
        return $this->dompdf->output();
    }

    public function getLogo(): string
    {
        $relativePath = $this->packages->getUrl('uploads/brochures/LOGOSOCAF.png');
        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . $relativePath;

        $type = pathinfo($absolutePath, PATHINFO_EXTENSION);
        $data = file_get_contents($absolutePath);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    // Méthode pour générer un PDF en paysage (conservée pour compatibilité)
    public function generateBynaryPdf($html)
    {
        return $this->generateBinaryPdf($html, ['orientation' => 'landscape']);
    }
}