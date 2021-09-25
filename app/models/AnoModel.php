<?php

class AnoModel extends Model{
    
    public $_tabela = 'ano';
    public $pkName  = 'id_ano';
    
    public $id_ano;
    public $descricao;
    public $sigla;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_ano = $id;
            $this->descricao = $query[0]['descricao'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_ano = 0;
        if($_POST['id_ano']){
            $this->id_ano = $_POST['id_ano'];
        }
        $this->descricao = $_POST['descricao'];
        $msg = array();
            if (!$this->descricao) {
                $msg[] = $msgs->erro("Campo descriçao Obrigatório!");
            }
        $sql = "SELECT id_ano FROM ano
                WHERE (id_ano <> {$this->id_ano})
                AND (descricao = '{$this->descricao}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Ano letivo já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    public function salvar(){
        return $this->inserir(array("descricao" => $this->descricao));
    }
    
    public function alterar(){
        return $this->atualizar(array("descricao" => $this->descricao), $this->id_ano);
    }
}

