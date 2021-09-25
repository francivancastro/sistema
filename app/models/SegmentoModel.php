<?php

class SegmentoModel extends Model{
    
    public $_tabela = 'segmento';
    public $pkName  = 'id_segmento';
    
    public $id_segmento;
    public $descricao;
    public $sigla;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_segmento = $id;
            $this->descricao = $query[0]['descricao'];
            $this->sigla = $query[0]['sigla'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_segmento = 0;
        if($_POST['id_segmento']){
            $this->id_segmento = $_POST['id_segmento'];
        }
        $this->descricao = $_POST['descricao'];
        $this->sigla = $_POST['sigla'];
        $msg = array();
            if (!$this->descricao) {
                $msg[] = $msgs->erro("Campo descricao Obrigatório!");
            }
            if (!$this->sigla) {
                $msg[] = $msgs->erro("Campo sigla Obrigatório!");
            }
        $sql = "SELECT id_segmento FROM segmento
                WHERE (id_segmento <> {$this->id_segmento})
                AND (descricao = '{$this->descricao}')
                AND (sigla = '{$this->sigla}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Segmento Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array(
            "descricao" => $this->descricao,
            "sigla" => $this->sigla
        ));
    }
    
    public function alterar(){
        return $this->atualizar(array(
            "descricao" => $this->descricao,
            "sigla" => $this->sigla
        ), $this->id_segmento);
    }
}

