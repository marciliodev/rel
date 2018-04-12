<?php
require_once ("mpdf60/mpdf.php"); // chamada da biblioteca mpdf
require_once ("conexao/Conexao.php"); //conexão com o banco
//require_once("../models/Pdf.php"); // chamada ao model de pdf

class PDF_c extends mpdf {

    // Atributos da classe
    private $pdo = null;
    private $pdf = null;
    private $css = null;
    private $titulo = null;
    private $itens = 0;
    private $elementos = 0;

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
    public function contItens()
    {
        $cont = 1;
        $this->itens = $this->itens + $cont;
        return $this->itens;
    }

    /*
     * Método para gerar somátorio de elementos no relatório
     * Não é necessário passar parametros, pois o valor é fixo de 1 a 1.
     */
    public function contElementos($value)
    {

        $cont = 1;
        $this->elementos = $this->elementos + $cont;
        return $this->elementos;
    }

    /*
    * Método para gerar somátorio de páginas no relatório
    * Não é necessário passar parametros, pois o valor é fixo de 1 a 1.
    */
    public function contPages()
    {
        $cont = 1;
        $this->itens = $this->itens + $cont;
        return $this->itens;
    }

    //Construção do Header para o PDF de Material de Escritórioj
    public function HeaderHTML_RME(){

    }

    //Construção do HTML para o PDF de Material de Escritório
    public function CorpoHTML_RME() {

        $data = date('d/m/Y');
        $color = null;

        $html .= "
        <header class='RME'>
        <img src=\"img\logo2.jpg\" class='logo'>
        <div class='cabecalho_RME'>
            <h1>FUNDAÇÃO DE SAÚDE DE VITÓRIA DA CONQUISTA $data<br>MATERIAL DE ESCRITÓRIO</h1>
        </div>
        </header>";

        $html .= " 
        <fieldset class='RME'>
            <table class='RME'>
                <tr class='fieldset_tr'>
                <th class='fieldset_th_itens' align='center'>ITENS</th>
                <th class='fieldset_th_discriminacao' align='left'>DISCRIMINAÇÃO DETALHADA DO PRODUTO</th>
                <th class='fieldset_th_qt_alm' align='center'>QTD. ALMOXARIFADO</th>
                <th class='fieldset_th_qt_est' align='center'>QTD. ATUAL</th>
                <th class='fieldset_th_vl_uni' align='center'>VALOR UNITÁRIO</th>
                <th class='fieldset_th_vl_total' align='center'>VALOR TOTAL</th> 
                </tr>";

                //Chamada do SQL
                $sql = "select * from produtos";
                foreach ($this->pdo->query($sql) as $reg):
                    $html .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
                    $html .= "<td class='fieldset_th_itens' align='center'>";
                    $html .= $this->contItens(); //chama a função que conta os itens
                    $html .= "</td>"; //Itens do relatório
                    $html .= "<td class='fieldset_th_discriminacao' align='left'>{$reg['disc_produto']}</td>"; //descrição do produto
                    $html .= "<td class='fieldset_th_qt_alm' align='center'>{$reg['qt_total']}</td>"; //quantidade total do estoque
                    $html .= "<td class='fieldset_th_qt_est' align='center'>{$reg['qt_atual']}</td>"; //quantidade atual do estoque
                    $html .= "<td class='fieldset_th_qt_uni' align='center'>R$ {$reg['vl_unitario']}</td>"; //valor unitário do produto
                    $html .= "<td class='fieldset_th_vl_total' align='center'>R$ {$reg['vl_total']}</td>"; //valor total do produto
                    $color = !$color;
                endforeach;

        $html .= "
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class='fieldset_td_total' align='center'><b>TOTAL</b></td>";

        //Soma do total do vl_total de cada item da lista
        $soma = "select sum(vl_total) from produtos";
        foreach ($this->pdo->query($soma) as $resultado);
        $html .= "<td class='fieldset_td_total' align='center'><b>R$ {$resultado[0]}</b></td>
            </tr>
            </table>
            </fieldset>";

        return $html;
    }

    //Construção do HTML para o PDF de Material do Almoxarifado
    public function CorpoHTML_RMA() {

        $data = date('d/m/Y');
        $color = null;
        $html .= "
        <fieldset class='R_M_A'>
        <img src=\"img\logo2.jpg\">
        <div class='header'>
            <h1 id='h1_RMA'>Relação de Materiais do Almoxarifado $data</h1>
        </div>";
        $html .= " <table border='1' width='1000' align='center'>
        <tr class='header'>
            <th class='center'>Itens</th>
            <th id='th_desc_RSV' class='center'>Descrição do item</th>
            <th class='center'>Quantitativo</th>
            <th class='center'>U.F</th>
            <th class='center'>Valor Unitário. R$</th>
            <th class='center'>Valor Total. R$</th>
        </tr>";

        //Chamada do SQL
        $sql = "select * from produtos";
        foreach ($this->pdo->query($sql) as $reg):
            $html .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
            $html .= "<td class='center'>";
            $html .= $this->contItens(); //chama a função que conta os itens
            $html .= "</td>"; //Itens do relatório
            $html .= "<td class='left'>{$reg['disc_produto']}</td>"; //descrição do produto
            $html .= "<td class='center'>{$reg['qt_atual']}</td>"; //quantidade total do estoque
            $html .= "<td class='center'>{$reg['UF']}</td>"; //quantidade atual do estoque
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
        <td class='center'><b>TOTAL</b></td>";
        //Soma do total do vl_total de cada item da lista
        $soma = "select sum(vl_total) from produtos";
        foreach ($this->pdo->query($soma) as $resultado);
        $html .= "<td class='center'><b>R$ {$resultado[0]}</b></td>
        </tr>
        </table>
        </fieldset>";

        return $html;
    }

    //Construção do HTML para o PDF de Material de Serviço Vascular
    public function CorpoHTML_RMSV() {

        $data = date('d/m/Y');
        $color = null;
        $html .= "
        <fieldset class='RMSV'>
        <img src=\"img\logo2.jpg\">
        <div class='header'>
            <h1 id='h1_RSV'>Relação de Materiais do Almoxarifado para Serviço Vascular $data</h1>
        </div>";
        $html .= " <table border='1' width='1000' align='center'>
        <tr class='header'>
            <th class='center'>Itens</th>
            <th id='th_desc_SV' class='center'>Descrição do item</th>
            <th class='center'>Quantitativo</th>
            <th class='center'>U.F</th>
            <th class='center'>Valor Unitário. R$</th>
            <th class='center'>Valor Total. R$</th>
        </tr>";

        //Chamada do SQL
        $sql = "select * from produtos";
        foreach ($this->pdo->query($sql) as $reg):
            $html .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
            $html .= "<td class='center'>";
            $html .= $this->contItens(); //chama a função que conta os itens
            $html .= "</td>"; //Itens do relatório
            $html .= "<td class='left'>{$reg['disc_produto']}</td>"; //descrição do produto
            $html .= "<td class='center'>{$reg['qt_atual']}</td>"; //quantidade total do estoque
            $html .= "<td class='center'>{$reg['UF']}</td>"; //quantidade atual do estoque
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
        <td class='center'><b>TOTAL</b></td>";
        //Soma do total do vl_total de cada item da lista
        $soma = "select sum(vl_total) from produtos";
        foreach ($this->pdo->query($soma) as $resultado);
        $html .= "<td class='center'><b>R$ {$resultado[0]}</b></td>
        </tr>
        </table>
        </fieldset>";

        return $html;
    }

    //Construção do HTML para o PDF de Entrada
    public function CorpoHTML_RE() {

        $data = date('d/m/Y');
        $color = null;

        $html .= "
        <fieldset class='RME'>
        <img src=\"img\logo1.jpg\">
        <div class='entrada'>
            <h1 class='entrada'>Relatório de Entrada</h1>
            <h2 class='entrada'><b>&nbsp;Fornecedor:</b> NOME DO FORNECEDOR - <br>
            &nbsp;<b>Nº Documento:</b> 000001/2018<br>
            <b>&nbsp;Data:</b> $data</h2>
        </div>";
        $html .= " <table border='1' width='1000' align='center'>
        <tr class='header_desc'>
            <th id='th_cod_RE' class='center'>Código</th>
            <th id='th_desc_RE' class='center'>Descrição</th>
            <th id='th_qtd_RE' class='center'>Qtd</th>
            <th id='th_und_RE' class='center'>Und.</th>
            <th id='th_vl_custo_RE' class='center'>Valor de Custo R$</th>
            <th id='th_vl_total_RE' class='center'>Valor Total R$</th> 
            <th id='th_lote_RE' class='center'>Lote</th> 
            <th id='th_validade_RE' class='center'>Validade</th> 
        </tr>";

        //Chamada do SQL
        $sql = "select * from produtos";
        foreach ($this->pdo->query($sql) as $reg):
            $html .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
            $html .= "<td class='center'>";
            $html .= $this->contItens(); //chama a função que conta os itens
            $html .= "</td>"; //Itens do relatório
            $html .= "<td class='left'>{$reg['disc_produto']}</td>"; //descrição do produto
            $html .= "<td class='center'>{$reg['qt_total']}</td>"; //quantidade total do estoque
            $html .= "<td class='center'>{$reg['qt_atual']}</td>"; //quantidade atual do estoque
            $html .= "<td class='left'>{$reg['vl_unitario']}</td>"; //valor unitário do produto
            $html .= "<td class='left'>{$reg['vl_total']}</td>"; //valor total do produto
            $html .= "<td class='center'></td>"; //valor do lote
            $html .= "<td class='left'>-</td>"; //valor da validade
            $color = !$color;
        endforeach;

        $html .= ";
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        <td class='left'><b>TOTAL</b></td>";
        //Soma do total do vl_total de cada item da lista
        $soma = "select sum(vl_total) from produtos";
        foreach ($this->pdo->query($soma) as $resultado);
        $html .= "<td class='left'><b>{$resultado[0]}</b></td>
            <td></td>
            <td></td>
        </tr>
        </table>
        </fieldset>";

        return $html;
    }

    // Gerar Relatório de Material de Escritório
    public function GerarPDF($tipo) {

        $this->$tipo = 0; //tipo que será passado no parâmetro da construção do pdf no index.php

        //Verificar qual relatório será gerado
        switch ($tipo){
            case 1: {
                // Geração do PDF Material de Escritório
                $this->pdf = new mPDF('utf-8', 'A4');
                $this->pdf->SetDisplayMode('fullpage');
                $css = file_get_contents("css/estilo.css");
                //Parâmetros do Corpo do PDF
                $this->pdf->WriteHTML($css, 1);
                $this->pdf->WriteHTML($this->CorpoHTML_RME());
                //Saída do PDF
                ob_end_clean(); //limpar objeto antes da geração do PDF
                $this->pdf->Output();
                //Trata caracteres especiais sem gerar erro
                $this->pdf->allow_charset_conversion = true;
                //$this->pdf->charset_in='iso-8859-1';
                $this->pdf->charset_in = 'windows-1252';
                //Geração do arquivo temporário do PDF para não gerar atrasos
                $this->pdf = new mPDF(['tempDir' => __DIR__ . '/tmp']);
                exit;
            }
            case 2: {
                // Geração do PDF Material do Almoxarifado
                $this->pdf = new mPDF('utf-8', 'A4-L'); //A4-L Vertical
                $css = file_get_contents("css/estilo.css");
                //Parâmetros do Corpo do PDF
                $this->pdf->SetDisplayMode('fullpage');
                $this->pdf->WriteHTML($css, 1);
                $this->pdf->WriteHTML($this->CorpoHTML_RMA());
                //Saída do PDF
                ob_end_clean(); //limpar objeto antes da geração do PDF
                $this->pdf->Output();
                //Trata caracteres especiais sem gerar erro
                $this->pdf->allow_charset_conversion = true;
                //$this->pdf->charset_in='iso-8859-1';
                $this->pdf->charset_in = 'windows-1252';
                //Geração do arquivo temporário do PDF para não gerar atrasos
                $this->pdf = new mPDF(['tempDir' => __DIR__ . '/tmp']);
                exit;
            }
            case 3: {
                // Geração do PDF Materila de Serviço Vascular
                $this->pdf = new mPDF('utf-8', 'A4-L'); //A4-L Vertical
                $css = file_get_contents("css/estilo.css");
                //Parâmetros do Corpo do PDF
                $this->pdf->SetDisplayMode('fullpage');
                $this->pdf->WriteHTML($css, 1);
                $this->pdf->WriteHTML($this->CorpoHTML_RMSV());
                //Saída do PDF
                ob_end_clean(); //limpar objeto antes da geração do PDF
                $this->pdf->Output();
                //Trata caracteres especiais sem gerar erro
                $this->pdf->allow_charset_conversion = true;
                //$this->pdf->charset_in='iso-8859-1';
                $this->pdf->charset_in = 'windows-1252';
                //Geração do arquivo temporário do PDF para não gerar atrasos
                $this->pdf = new mPDF(['tempDir' => __DIR__ . '/tmp']);
                exit;
            }
            case 4: {
                // Geração do PDF Entrada
                $this->pdf = new mPDF('utf-8', 'A4');
                $css = file_get_contents("css/estilo.css");
                //Parâmetros do Corpo do PDF
                $this->pdf->SetDisplayMode('fullpage');
                $this->pdf->WriteHTML($css, 1);
                $this->pdf->WriteHTML($this->CorpoHTML_RE());
                //Saída do PDF
                ob_end_clean(); //limpar objeto antes da geração do PDF
                $this->pdf->Output();
                //Trata caracteres especiais sem gerar erro
                //$this->pdf->charset_in='iso-8859-1';
                $this->pdf->allow_charset_conversion = true;
                $this->pdf->charset_in = 'windows-1252';
                //Geração do arquivo temporário do PDF para não gerar atrasos
                $this->pdf = new mPDF(['tempDir' => __DIR__ . '/tmp']);
                exit;
            }
            default : {
                exit;
            }

        }

    }

    /*
    * Método para exibir o arquivo PDF
    * @param $name - Nome do arquivo se necessário grava-lo
    */
    public function Exibir($name = null) {
        $this->pdf->Output($name, 'I');
    }
}