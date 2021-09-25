<?php

class AnoletivoModel extends Model {
    
    public $_tabela = 'ano_letivo';
    public $pkName  = 'id_ano';
    public $fkName  = 'id_segmento';
    public $fkName2  = 'id_ano';
    
    public $id_ano_letivo;
    public $id_ano;
    public $id_segmento;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_ano_letivo = $id;
            $this->id_ano = $query[0]['id_ano'];
            $this->id_segmento = $query[0]['id_segmento'];
        }
    }
    
    public function listarPorAno($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id);
        }
        return false;
    }
    public function validar(){
        $this->id_ano_letivo = 0;
        if($_POST['id_ano_letivo']){
            $this->id_ano_letivo = $_POST['id_ano_letivo'];
        }
        $this->id_ano = $_POST['id_ano'];
        $this->id_segmento = $_POST['id_segmento'];
        $msg = array();
        if (!$this->id_ano) {
            $msg[] = "Campo Nome da Taxa Obrigatório!";
        }
        $sql = "SELECT id_ano_letivo FROM ano_letivo
                WHERE (id_ano_letivo <> {$this->id_ano_letivo})
                AND (id_segmento = '{$this->id_segmento}')
                AND (id_ano = '{$this->id_ano}');";
        $rg = $this->consultar($sql);
        if ($rg) {
             $msg[] = "Já cadastrado!";
        }
        if (count($msg)) {
            return $msg;
        }
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array(
            "id_ano" => $this->id_ano,
            "id_segmento" => $this->id_segmento
        ));
    }
    public function alterar(){
        return $this->atualizar(array(
            "id_ano" => $this->id_ano,
            "id_segmento" => $this->id_segmento
        ), $this->id_ano_letivo);
    }
    
}
