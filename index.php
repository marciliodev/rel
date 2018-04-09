<?php
/**
 * Created by PhpStorm.
 * User: marcilio
 * Date: 06/04/2018
 * Time: 09:14
 */

    require_once ('controllers/PDF_c.php'); //requisita o controlador

    //Geração do PDF com base no tipo de relatório repassado por parâmetro
    $report = new PDF_c("css/estilo.css", "Relatório"); //Parâmetros do pdf
    $report->GerarPDF(4); // chama a construção do pdf.
    $report->Exibir("Relatório"); //nome do arquivo relatório que será salvo.