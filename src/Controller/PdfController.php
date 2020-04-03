<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Patient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    /**
     * @Route("/pdf/{id}", name="pdf_print")
     */
    public function generate_pdf($id){

        $patient = $this->getDoctrine()->getRepository(Patient::class)->find($id);

        $options = new Options();

        $options->set('defaultFont', 'DejaVu Sans');
        $options->setIsRemoteEnabled(true);
        $options->setDpi(100);
        $options->setIsHtml5ParserEnabled(true);
        $options->setIsJavascriptEnabled(true);
        $options->setIsPhpEnabled(true);
               
        $dompdf = new Dompdf($options);
        
        $data = array(
          'headline' => 'my headline'
        );
        $html = $this->renderView('pdf/pdf.html.twig', [
            'headline' => "Patient's Information",
            'patient' => $patient
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("patientinfo.pdf", [
            "Attachment" => true
        ]);
    }
}
