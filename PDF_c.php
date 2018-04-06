<?php
    require_once("mpdf60/mpdf.php"); // chamada da biblioteca mpdf
    require_once("conexao/conexao.php"); //conexão com o banco
    $data = date('d/m/Y');//Data da solicitação
    $pdo = Conexao::getInstance();

    //Estrutura antiga
/*
 // Construção do PDF em HTML
 $html = "";
 $html .= "<fieldset>";
 $html .= "<img src=\"img\logo1.jpg\">";
 $html .= "<div class='header'>";
 $html .= "<h2>FUNDAÇÃO DE SAÚDE DE VITÓRIA DA CONQUISTA $data</h2>";
 $html .= "</div>";
 $html .= "<div class='header'>";
 $html .= "<h2>MATERIAL DE ESCRITÓRIO</h2>";
 $html .= "</div>";
 $html .= "<table border='1' width='1000' align='center'>";
 $html .= "<tr class='header'>";
 $html .= "<th>Discriminação Detalhada do Produto</th>";
 $html .= "<th>Estoque do Almoxarifado</th>";
 $html .= "<th>Estoque Total</th>";
 $html .= "<th>Valor Unitário</th>";
 $html .= "<th>Valor Total</th>";
 $html .= "</tr>";

 $sql = "select * from produtos";
 foreach ($pdo->query($sql) as $reg):
    $html .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
    $html .= "<td class='destaque'>{$reg['disc_produto']}</td>";
    $html .= "<td>{$reg['qt_total']}</td>"; //quantidade total do estoque
    $html .= "<td>{$reg['qt_atual']}</td>"; //quantidade atual do estoque
    $html .= "<td>{$reg['vl_unitario']}</td>"; //valor unitário do produto
    $html .= "<td>{$reg['vl_total']}</td>"; //valor total do produto
    $html = !$color;
 endforeach;

 $html .= "</table>";
 return $html;
 $html .= "</table>";
 $html .= "<div class='principal'>&nbsp;</div>";
 $html .= "</fieldset>";
*/

    //Construção do HTML
    $html .= "
    <fieldset>
        <img src=\"img\logo1.jpg\">
        <div class='header'>
            <h2>FUNDAÇÃO DE SAÚDE DE VITÓRIA DA CONQUISTA $data</h2>
        </div>
        <div class='header'>
            <h2>MATERIAL DE ESCRITÓRIO</h2>
        </div>
        <table border='1' width='1000' align='center'>
        <tr class='header'>
            <th>Discriminação Detalhada do Produto</th>
            <th>Estoque do Almoxarifado</th>
            <th>Estoque Total</th>
            <th>Valor Unitário</th>
            <th>Valor Total</th>
        </tr>";

    //Chamada do SQL
    $sql = "select * from produtos";
    foreach ($pdo->query($sql) as $reg):
    $html .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
    $html .= "<td class='destaque'>{$reg['disc_produto']}</td>";
    $html .= "<td>{$reg['qt_total']}</td>"; //quantidade total do estoque
    $html .= "<td>{$reg['qt_atual']}</td>"; //quantidade atual do estoque
    $html .= "<td>{$reg['vl_unitario']}</td>"; //valor unitário do produto
    $html .= "<td>{$reg['vl_total']}</td>"; //valor total do produto
    endforeach;
    $html .= "</table>";
    $html .= "<div class='principal'>&nbsp;</div>";
    $html .= "</fieldset>";

    // Geração do PDF
    $mpdf = new mPDF('utf-8', 'A4');
    //Trata caracteres especiais sem gerar erro
    $mpdf->allow_charset_conversion = true;
    //$mpdf->charset_in='iso-8859-1';
    $mpdf->charset_in='windows-1252';
    $mpdf->SetDisplayMode('fullpage');
    $css = file_get_contents("css/estilo.css");
    $mpdf->WriteHTML($css, 1);
    $mpdf->WriteHTML($html);
    $mpdf->Output();
    exit;