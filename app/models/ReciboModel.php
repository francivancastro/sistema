<?php

class ReciboModel extends Model {
    
    public $_tabela = 'recibo';
    public $pkName = 'id_recibo';
    public $fkName = 'id_protesto';
    
    public $id_recibo;
    public $id_protesto;
    public $id_taxas;
    public $valor;
    public $descricao;
    public $data_inclusao;


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_recibo = $id;
            $this->id_protesto = $query[0]['id_protesto'];
            $this->id_taxas = $query[0]['id_taxas'];
            $this->descricao = $query[0]['descricao'];
            $this->data_inclusao = $query[0]['data_inclusao'];
            $this->valor = $query[0]['valor'];
        }
    }
    
    public function validar(){
        $caracter = array('.');
        $msgs = new MsgHelper();
        $this->id_recibo = 0;
        if($_POST['id_recibo']){
            $this->id_recibo = $_POST['id_recibo'];
        }
        $this->id_protesto = $_POST['id_protesto'];
        $this->id_taxas = $_POST['id_taxas'];
        $this->descricao = $_POST['descricao'];
        $this->data_inclusao = date("Y-m-d");
        $this->valor = str_replace(',', '.', str_replace($caracter,"",  $_POST['valor']));
        $msg = array();
        if(!$this->valor){
            $msg[] = $msgs->erro("Campo valor Obrigatório!");
        }
        
        $sql = "SELECT id_recibo FROM recibo
                WHERE (id_recibo <> {$this->id_recibo})
                AND (id_protesto = '{$this->id_protesto}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Recibo já cadastrado para o protesto!");
        }
        
        if (count($msg)) {
            return $msg;
        }
        return false;
    }
    
    public function listarPorProtesto($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array("id_protesto" => $this->id_protesto,
                                      "id_taxas" => $this->id_taxas,
                                      "data_inclusao" => $this->data_inclusao,
                                      "descricao" => $this->descricao,
                                      "valor" => $this->valor
            ));
    }
    
    public function alterar(){
        return $this->atualizar(array("id_protesto" => $this->id_protesto,
                                      "id_taxas" => $this->id_taxas,
                                      "data_inclusao" => $this->data_inclusao,
                                      "descricao" => $this->descricao,
                                      "valor" => $this->valor), $this->id_recibo);
    }
}
