<?php

class BairroModel extends Model {
    
    public $_tabela = 'bairro';
    public $pkName = 'id_bairro';
    public $fkName = 'id_municipio';


    public $id_bairro;
    public $id_municipio;
    public $nome;
    
    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_bairro = $id; 
            $this->id_municipio = $query[0]['id_municipio'];
            $this->nome = $query[0]['nome'];
            
        }
    }
    
    public function listarPorMunicipio($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $id = 0;
        if($_POST['id_bairro']){
            $id = $_POST['id_bairro'];
        }
        $this->id_municipio = $_POST['id_municipio'];
        $this->nome = $_POST['nome'];
        $msg = array();
            if (!$this->id_municipio) {
                $msg[] = $msgs->erro("Campo municipio Obrigatório!");
            } 
            if (!$this->nome) {
                $msg[] = $msgs->erro("Campo Bairro Obrigatório!");
            }
   
        $sql = "SELECT id_bairro FROM bairro
                WHERE (id_bairro <> {$id})
                AND (id_municipio = '{$this->id_municipio}')
                AND (nome = '{$this->nome}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Bairro já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }

     public function salvar(){
        return $this->inserir(array("id_municipio" => $this->id_municipio,
                                        "nome" => $this->nome));
    }
   
}
