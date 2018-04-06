<?php
/**
 * Created by PhpStorm.
 * User: marcilio
 * Date: 06/04/2018
 * Time: 09:14
 */

    require_once "PDF_c.php";


    $report = new reportCliente();
    $report->GerarPDF_M_E(); // chama a construção do pdf.
    $report->Exibir("Relatório de Material de Escritório"); //nome do arquivo relatório que será salvo.