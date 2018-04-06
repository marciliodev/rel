<?php
    require_once("mpdf60/mpdf.php"); // chamada da biblioteca mpdf
    require_once("conexao/conexao.php"); //conexão com o banco


    class PDF_c extends mpdf {

    // Atributos da classe
    private $pdo = null;
    private $pdf = null;
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

    //Construção do HTML para o PDF
    public function CorpoHTML() {

        $data = date('d/m/Y');
        $color = null;
        $html .= "
        <fieldset>
        <img src=\"img\logo1.jpg\">
        <div class='header'>
            <h2>FUNDACAO DE SAUDE DE VITORIA DA CONQUISTA $data</h2>
        </div>
        <div class='header'>
            <h2>MATERIAL DE ESCRITORIO</h2>
        </div>";
        $html .= " <table border='1' width='1000' align='center'>
        <tr class='header'>
            <th class='center'>ITEM</th>
            <th class='center'>DISCRIMINACAO DETALHADA DO PRODUTO</th>
            <th id='quebra' class='center'>ESTOQUE DO ALMOXARIFADO</th>
            <th id='quebra' class='center'>ESTOQUE ATUAL</th>
            <th class='center'>VALOR UNITARIO</th>
            <th class='center'>VALOR TOTAL</th> 
        </tr>";

        //Chamada do SQL
        $count = 1;
        $sql = "select * from produtos";
        foreach ($this->pdo->query($sql) as $reg):
            $html .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
            $html .= "<td class='center'>$count";
            $html .= "</td>";
            $html .= "<td class='left'>{$reg['disc_produto']}</td>"; //descrição do produto
            $html .= "<td class='center'>{$reg['qt_total']}</td>"; //quantidade total do estoque
            $html .= "<td class='center'>{$reg['qt_atual']}</td>"; //quantidade atual do estoque
            $html .= "<td class='left'>R$ {$reg['vl_unitario']}</td>"; //valor unitário do produto
            $html .= "<td class='left'>R$ {$reg['vl_total']}</td>"; //valor total do produto
            $color = !$color;
        endforeach;

        $html .= "
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        <td class='left'>TOTAL</td>";
        //Soma do total do vl_total de cada item da lista
        $soma = "select sum(vl_total) from produtos";
        foreach ($this->pdo->query($soma) as $resultado);
        $html .= "<td class='left'>R$ {$resultado[0]}</td>
        </tr>
        </table>
        </fieldset>";

        return $html;
    }

    public function GerarPDF_M_E() {
    // Geração do PDF
    $this->pdf = new mPDF('utf-8', 'A4');
    //Trata caracteres especiais sem gerar erro
    $this->pdf->allow_charset_conversion = true;
    $this->pdf->charset_in='iso-8859-1';
    //$this->pdf->charset_in = 'windows-1252';
    $this->pdf->SetDisplayMode('fullpage');
    $css = file_get_contents("css/estilo.css");
    $this->pdf->WriteHTML($css, 1);
    $this->pdf->WriteHTML($this->CorpoHTML());
    $this->pdf->Output();
    $this->pdf = new mPDF(['tempDir' => __DIR__ . '/tmp']);
    ob_clean(); //limpa o objeto dos dados do pdf
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