<?php

class TaxasModel extends Model{
    
    public $_tabela = 'taxas';
    public $pkName  = 'id_taxas';
    
    public $id_taxas;
    public $descricao;
    public $test;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_taxas = $id;
            $this->descricao = $query[0]['descricao'];
        }
    }
    
    public function validar(){
        $val = array();
        $id = 0;
        if($_POST['id_taxas']){
            $id = $_POST['id_taxas'];
        }
        $this->descricao = $_POST['descricao'];
        $this->test = $_POST['test'];
        $msg = array();
            if (!$this->descricao) {
                $msg['descricao'] = "Campo Nome da Taxa Obrigatório!";
            }
        $val['mensagens'] = $msg;
        $val['campos'] = $_POST;
        if (count($msg)) {
            return $val;
        }
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array("descricao" => $this->descricao));
    }
}
