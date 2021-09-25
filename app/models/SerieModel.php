<?php

class SerieModel extends Model{
    
    public $_tabela = 'serie';
    public $pkName  = 'id_serie';
    public $fkName  = 'id_ano_letivo';
    
    public $id_serie;
    public $descricao;
    public $id_ano_letivo;
    public $sigla;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_serie = $id;
            $this->descricao = $query[0]['descricao'];
            $this->id_ano_letivo = $query[0]['id_ano_letivo'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_serie = 0;
        if($_POST['id_serie']){
            $this->id_serie = $_POST['id_serie'];
        }
        $this->descricao = $_POST['descricao'];
        $this->id_ano_letivo = $_POST['id_ano_letivo'];
        $msg = array();
            if (!$this->descricao) {
                $msg[] = $msgs->erro("Campo descricao Obrigatório!");
            }
        $sql = "SELECT id_serie FROM serie
                WHERE (id_serie <> {$this->id_serie})
                AND (descricao = '{$this->descricao}')
                AND (id_ano_letivo = '{$this->id_ano_letivo}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Serie Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function listarPorAnoletivo($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array(
            "descricao" => $this->descricao,
            "id_ano_letivo" => $this->id_ano_letivo
        ));
    }
    
    public function alterar(){
        return $this->atualizar(array(
            "descricao" => $this->descricao,
            "id_ano_letivo" => $this->id_ano_letivo
        ), $this->id_serie);
    }
}

