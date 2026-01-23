<?php

namespace App\Service;

use Mpdf\Mpdf;
use Symfony\Component\Asset\Packages;

class MpdfService
{
    private Packages $packages;

    public function __construct(Packages $packages)
    {
        $this->packages = $packages;
    }

 public function generatePdf(string $html, array $options = []): string
{
    $defaultOptions = [
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'P',
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 45,     
        'margin_bottom' => 5,  
        'margin_header' => 5,
        'margin_footer' => 10,
        'default_font_size' => 12,
        'default_font' => 'Arial'
    ];

    $mpdf = new Mpdf(array_merge($defaultOptions, $options));

    // Set header and footer if provided
    if (!empty($options['header_html'])) {
        $mpdf->SetHTMLHeader($options['header_html']);
    }

    if (!empty($options['footer_html'])) {
        $mpdf->SetHTMLFooter($options['footer_html']);
    }

    $mpdf->WriteHTML($html);

    return $mpdf->Output('', 'S');
}

    public function getLogo(): string
    {
        $relativePath = $this->packages->getUrl('public/uploads/brochures/LOGOSOCAF.png');
        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . $relativePath;

        if (!file_exists($absolutePath)) {
            throw new \RuntimeException("Logo file not found at: $absolutePath");
        }

        $type = pathinfo($absolutePath, PATHINFO_EXTENSION);
        $data = file_get_contents($absolutePath);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

public function getHtmlHeader(string $logoBase64): string
{
    return <<<HTML
<div style="
    top: 0;
    left: 150;
    right: 0;
    width: 600px;
    max-width: 700px;
    margin: 0 auto;
    heigth: 110px;
    background: white;
    margin: -50px 0 30px 0;
    padding: 10px 10px 0 10px;
    font-size: 8px;
    z-index: 1000;
">
    <div style="position: relative;">
        <img src="$logoBase64" alt="Logo" style="width: 120px; height: 90px;">
    </div>

        <!-- Ligne bleue (ajustée) -->
    <div style="
        position: absolute;
        left: 0;  
        margin-right: -135px;
        bottom: 0;
        border-bottom: 2px solid #0066cc;
        margin-left: 80px;
        margin-top: -20px;
        
    "></div>

    <div style="padding: -60px 0 0 175px;">
        <table style="
            width: 100%;
            border: none;
            font-size: 8px;
            margin-top: 0;
            margin-bottom: 0;
            border-collapse: collapse;
            
        ">
            <tbody>
                <tr>
                    <td style="color: blue; padding: 0 0 0 -30px; border: none; line-height: 1.2;">PESAGE INDUSTRIEL</td>
                    <td style="color: blue; padding:  0 0 0 75px; border: none; line-height: 1.2;">PLOMBERIE</td>
                </tr>
                <tr>
                    <td style="color: blue; padding: 0 0 0 -30px; border: none; line-height: 1.2;">SECURITE ELECTRONIQUE</td>
                    <td style="color: blue; padding:  0 0 0 75px; border: none; line-height: 1.2;">FROID INDUSTRIEL</td>
                </tr>
                <tr>
                    <td style="color: blue; padding: 0 0 0 -30px; border: none; line-height: 1.2;">ELECTRONIQUE INDUSTRIEL</td>
                    <td style="color: blue; padding:  0 0 0 75px; border: none; line-height: 1.2;">VENTE DE MATERIEL INFORMATIQUE ET ELECTRONIQUE</td>
                </tr>
                <tr>
                    <td style="color: blue; padding: 0 0 0 -30px; border: none; line-height: 1.2;">INFORMATIQUE GENERAL ET INDUSTRIEL</td>
                    <td style="color: blue; padding:  0 0 0 75px; border: none; line-height: 1.2;">COMMERCE GENERAL</td>
                </tr>
                <tr>
                    <td style="color: blue; padding: 0 0 0 -30px; border: none; line-height: 1.2;">ELECTRICITE BATIMENT ET INDUSTRIEL</td>
                    <td style="color: blue; padding:  0 0 0 75px; border: none; line-height: 1.2;">DIVERSES PRESTATIONS DE SERVICES</td>
                </tr>
                <tr>
                    <td colspan="2" style="color: blue; padding: 0 0 0 -30px; border: none; line-height: 1.2;">GENIE CIVIL</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
HTML;
}

  public function getHtmlFooter(): string
{
    return <<<HTML
<div style="
    left: 0;
    right: 0;
    background: white;
    border-top: 2px solid #0066cc;
    margin: 0;
    text-align: center;
    font-size: 13px;
    line-height: 1.2;
    padding: 2px;
">
    SOCAF PESAGE, Abidjan, Yopougon - N° RCCM : CI-ABJ-03-2021-B13-07267 - N° CC : 2190890 Y<br>
    Capital : 1 000 000 F CFA - Tél : (+225) 07 68 63 43 15 / 05 56 25 08 13 - Email : socaf.pesage@gmail.com
</div>
HTML;
}
}
