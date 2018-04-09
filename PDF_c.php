<?php
require_once("mpdf60/mpdf.php"); // chamada da biblioteca mpdf
require_once("conexao/conexao.php"); //conexão com o banco


class PDF_c extends mpdf {

    // Atributos da classe
    private $pdo = null;
    private $pdf = null;
    private $css = null;
    private $titulo = null;
    public $itens = 0;

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

    /*
     * Método para gerar somátorio de itens no relatório
     * Não é necessário passar parametros, pois o valor é fixo de 1 a 1.
     */
    public function somatorio()
    {
        $cont = 1;
        $this->itens = $this->itens + $cont;
        return $this->itens;
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
            <th class='center'>ITENS</th>
            <th class='center'>DISCRIMINACAO DETALHADA DO PRODUTO</th>
            <th id='quebra' class='center'>ESTOQUE DO ALMOXARIFADO</th>
            <th id='quebra' class='center'>ESTOQUE ATUAL</th>
            <th class='center'>VALOR UNITARIO</th>
            <th class='center'>VALOR TOTAL</th> 
        </tr>";

        //Chamada do SQL
        $sql = "select * from produtos";
        foreach ($this->pdo->query($sql) as $reg):
            $html .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
            $html .= "<td class='center'>";
            $html .= $this->somatorio();
            $html .= "</td>"; //Itens do relatório
            $html .= "<td class='left'>{$reg['disc_produto']}</td>"; //descrição do produto
            $html .= "<td class='center'>{$reg['qt_total']}</td>"; //quantidade total do estoque
            $html .= "<td class='center'>{$reg['qt_atual']}</td>"; //quantidade atual do estoque
            $html .= "<td class='center'>R$ {$reg['vl_unitario']}</td>"; //valor unitário do produto
            $html .= "<td class='center'>R$ {$reg['vl_total']}</td>"; //valor total do produto
            $color = !$color;
        endforeach;

        $html .= ";
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        <td class='center'>TOTAL</td>";
        //Soma do total do vl_total de cada item da lista
        $soma = "select sum(vl_total) from produtos";
        foreach ($this->pdo->query($soma) as $resultado);
        $html .= "<td class='center'>R$ {$resultado[0]}</td>
        </tr>
        </table>
        </fieldset>";

        return $html;
    }

    // Gerar Relatório de Material de Escritório
    public function GerarPDF_M_E() {
        // Geração do PDF
        $this->pdf = new mPDF('utf-8', 'A4');
        $css = file_get_contents("css/estilo.css");
        //Trata caracteres especiais sem gerar erro
        $this->pdf->allow_charset_conversion = true;
        $this->pdf->charset_in='iso-8859-1';
        $this->pdf->charset_in = 'windows-1252';
        //Parâmetros do Corpo do PDF
        $this->pdf->SetDisplayMode('fullpage');
        $this->pdf->WriteHTML($css, 1);
        $this->pdf->WriteHTML($this->CorpoHTML());
        //Saída do PDF
        ob_end_clean(); //limpar objeto antes da geração do PDF
        $this->pdf->Output();
        //Geração do arquivo temporário do PDF para não gerar atrasos
        $this->pdf = new mPDF(['tempDir' => __DIR__ . '/tmp']);
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