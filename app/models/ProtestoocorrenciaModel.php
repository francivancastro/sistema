<?php

class ProtestoocorrenciaModel extends Model {

    public $_tabela = 'ocorrencia_protesto';
    public $pkName = 'id_ocorrencia_protesto';
    public $fkName = 'id_protesto';
    
    public $id_ocorrencia_protesto;
    public $id_protesto;
    public $titulo;
    public $texto;
    public $data;
    

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if (isset($id)) {
            $query = $this->banco->ler($this->_tabela, $this->pkName . '=' . $id);
            $this->id_ocorrencia_protesto = $id;
            $this->id_protesto = $query[0]['id_protesto'];
            $this->titulo = $query[0]['titulo'];
            $this->texto = $query[0]['texto'];
            $this->data = $query[0]['data'];
            
        }
    }
    
    public function listarPorProtesto($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }

    public function validar() {
        $this->id_ocorrencia_protesto = 0;
        if ($_POST['id_arquivo']) {
            $this->id_ocorrencia_protesto = $_POST['id_arquivo'];
        }
        $this->titulo = $_POST['titulo'];
        $this->texto = $_POST['texto'];
        $this->data = date("Y-m-d");
        $this->id_protesto = $_POST['id_protesto'];
        $msg = array();
        /*
        $sql = "SELECT id_negociacao FROM negociacao
                WHERE (id_negociacao <> {$id})
                AND (ano_base = '{$this->ano_base}')
                AND (valor_parcela = '{$this->valor_parcela}');";
        $rg = $this->consultar($sql);
        if ($rg) {
            $msg[] = $msgs->erro("Negociação já Cadastrada!");
        }
         */
        if (count($msg)) {
            return $msg;
        }

        return false;
    }

    public function salvar() {
        return $this->inserir(array(
            "titulo" => $this->titulo,
            "texto" => $this->texto,
            "data" => $this->data,
            "id_protesto" => $this->id_protesto));
    }
}
