<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once("./application/libraries/dompdf/autoload.inc.php");
// require_once(base_url());

use Dompdf\Dompdf;
use Dompdf\Options; //updated
class Pdfgenerator
{
    public function generate($html, $filename = '', $paper = '', $orientation = '', $stream = TRUE)
    {
        $options = new Options(); //updated
        $options->set('isHtml5ParserEnabled', TRUE); //updated
        $options->set('isRemoteEnabled', TRUE); //updated
        $dompdf = new Dompdf($options); //updated
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();
        if ($stream) {
            $output = $dompdf->stream();
            // $output = $dompdf->stream('document/' . $filename . ".pdf");
            file_put_contents('document/' . $filename . ".pdf", $output);
        } else {
            $output = $dompdf->output();
            // $output = $dompdf->output('document/' . $filename . ".pdf");
            file_put_contents('document/' . $filename . ".pdf", $output);
        }
    }

    public function save($html, $filename = '', $paper = 'A4', $orientation = 'portrait')
    {
        // define(‘DOMPDF_ENABLE_AUTOLOAD’, false);
        $options = new Options(); //updated
        $options->set('isRemoteEnabled', TRUE); //updated
        $options->set('isHtml5ParserEnabled', TRUE); //updated
        $dompdf = new DOMPDF($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents('document/' . $filename, $output);
    }
}
