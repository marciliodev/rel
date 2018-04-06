<?php
    require_once("mpdf60/mpdf.php"); // chamada da biblioteca mpdf
    require_once("conexao/conexao.php"); //conexão com o banco


class PDF_c extends mpdf
{

    // Atributos da classe
    private $pdo = null;
    private $mpdf = null;
    private $css = null;
    private $titulo = null;

    /*
    * Construtor da classe
    * @param $css  - Arquivo CSS
    * @param $titulo - Título do relatório
    */
    public function __construct($css, $titulo)
    {
        $this->pdo = Conexao::getInstance();
        $this->titulo = $titulo;
        $this->setarCSS($css);
    }

    /*
        * Método para setar o conteúdo do arquivo CSS para o atributo css
        * @param $file - Caminho para arquivo CSS
        */
    public function setarCSS($file)
    {
        if (file_exists($file)):
            $this->css = file_get_contents($file);
        else:
            echo 'Arquivo CSS inexistente!';
        endif;
    }

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
    public function CorpoHTML()     {

        $color = null;
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
        foreach ($this->pdo->query($sql) as $reg):
            $html .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
            $html .= "<td>{$reg['disc_produto']}</td>";
            $html .= "<td>{$reg['qt_total']}</td>"; //quantidade total do estoque
            $html .= "<td>{$reg['qt_atual']}</td>"; //quantidade atual do estoque
            $html .= "<td>{$reg['vl_unitario']}</td>"; //valor unitário do produto
            $html .= "<td>{$reg['vl_total']}</td>"; //valor total do produto
            $color = !$color;
        endforeach;
        $html .= "</table>";
        $html .= "<div class='principal'>&nbsp;</div>";
        $html .= "</fieldset>";

        return $html;
    }

    public function GerarPDF_M_E() {
    // Geração do PDF
    $this->mpdf = new mPDF('utf-8', 'A4');
    //Trata caracteres especiais sem gerar erro
    $this->mpdf->allow_charset_conversion = true;
    //$this->mpdf->charset_in='iso-8859-1';
    $this->mpdf->charset_in = 'windows-1252';
    $this->mpdf->SetDisplayMode('fullpage');
    $css = file_get_contents("css/estilo.css");
    $this->mpdf->WriteHTML($css, 1);
    $this->mpdf->WriteHTML($this->CorpoHTML());
    $this->mpdf->Output();
    exit;
    }

/*
    * Método para exibir o arquivo PDF
    * @param $name - Nome do arquivo se necessário grava-lo
    */
    public function Exibir($name = null) {
        $this->pdf->Output($name, 'I');
    }
}