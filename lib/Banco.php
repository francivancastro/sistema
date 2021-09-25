<?php

class Banco
{
    private static $instancia;
    private $conexao;
    
    private $driver = 'mysql';
    private $host   = 'localhost';
    private $dbname = 'sistema';
    private $user   = 'root';
    private $pass   = '';

    public static function instanciar()
    {
        if(!self::$instancia) {
            self::$instancia = new Banco();
            self::$instancia->conectar();
        }
        
        return self::$instancia;
    }
    
    private function __construct() 
    {
        // Esta classe nao deve ser instanciada externamente
    }
    
    private function conectar(){
        $this->conexao = new PDO("{$this->driver}:host={$this->host};dbname={$this->dbname}", $this->user, $this->pass);
        $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function transacaoStart(){
        return $this->conexao->beginTransaction();
    }
    
    public function transacaoRefazer(){
        return $this->conexao->rollBack();
    }
    
    public function transacaoSalvar(){
        return $this->conexao->commit();
    }

    public function inserir($tabela, $dados){
        foreach ($dados as $inds => $vals ){
            ($vals ? $d = "'". $vals ."'" : $d = "null");
            $campos[] = $inds;
            $valores[] = $d;
        }    
        $campos = implode(", ", $campos);
        $valores = implode(",", $valores);
        return $this->conexao->query("INSERT INTO `{$tabela}` ({$campos}) VALUES ({$valores})");
        
    }
    
    public function atualizar($tabela, $dados, $where, $and = null){
        foreach ($dados as $ind => $val){
            if($val){
                $campos[] = "{$ind} = '{$val}'"; 
            } else {
                $campos[] = "{$ind} = null"; 
            }
        }
        $and = ($and != null ? "AND {$and}" : "");
        
        $campos = implode(", ", $campos);
        $q = "UPDATE `{$tabela}` SET {$campos} WHERE {$where} {$and}";
        
        return $this->conexao->query($q);
        
    }
    
    public function excluir($tabela, $where){
        return $this->conexao->query("DELETE FROM `{$tabela}` WHERE {$where}");
    }
    
    public function ler($tabela, $where = null, $limit = null, $offset = null, $orderby = null, $and = null){
        
        $where = ($where != null ? "WHERE ({$where})" : "");
        $and = ($and != null ? "AND {$and} " : $and = null);
        $limit = ($limit != null ? "LIMIT {$limit}" : "");
        $offset = ($offset != null ? "OFFSET ({$offset})" : "");
        $orderby = ($orderby != null ? "ORDER BY {$orderby}" : "");
        $q = $this->conexao->query("SELECT * FROM `{$tabela}` {$where} {$and} {$limit} {$orderby}");
        //print_r($q);die();
        $q->setFetchMode(PDO::FETCH_ASSOC);
        return $q->fetchAll();
        
    }
    
    public function consultar($sql) {
        $query = $this->conexao->prepare($sql);
        $query->execute();
        if ($query->rowCount()) {
                $array = array();
                while ($rg = $query->fetch(PDO::FETCH_OBJ)) {
                        $array[] = $rg;
                }
                return $array;
        }
        return false;
    }
    
    public function busca($sql) {
        $q = $this->conexao->query($sql);
        //print_r($q);die();
        $q->setFetchMode(PDO::FETCH_ASSOC);
        return $q->fetchAll();
    }
    
    public function pegaUltimoId($pkname, $tabela){
        $q = $this->conexao->query("SELECT LAST_INSERT_ID(`{$pkname}`) from `{$tabela}` order by `{$pkname}` desc limit 1");
        $q->setFetchMode(PDO::FETCH_ASSOC);
        foreach ($q as $inds => $vals ){
            $valor = $vals;
            foreach ($valor as $ind => $val){
               $id = $val;
            }
        }
        return $id;
    }
}