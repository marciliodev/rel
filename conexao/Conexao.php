<?php
/**
 * Created by PhpStorm.
 * User: marcilio
 * Date: 05/04/2018
 * Time: 17:29
 */
class Conexao extends PDO {

    private static $instancia;

    public function Conexao($dsn, $username = "", $password = "") {
        // O construtro abaixo é o do PDO
        parent::__construct($dsn, $username, $password);
    }

    public static function getInstance() {
        // Se o a instancia não existe eu faço uma
        if(!isset( self::$instancia )){
            try {
                self::$instancia = new Conexao("mysql:host=localhost; dbname=estoque", "root", "");
            } catch ( Exception $e ) {
                echo 'Erro ao conectar ao banco de dados.';
                exit ();
            }
        }
        // Se já existe instancia na memória eu retorno ela
        return self::$instancia;
    }
}
?>