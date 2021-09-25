<?php

class ProtestotaxasModel extends Model {
    
    public $_tabela = 'protesto_taxas';
    public $pkName = 'id_protesto_taxas';
    public $fkName = 'id_protesto';
    
    public $id_protesto_taxas;
    public $id_protesto;
    public $id_taxas;
    public $valor;


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_protesto_taxas = $id;
            $this->id_protesto = $query[0]['id_protesto'];
            $this->id_taxas = $query[0]['id_taxas'];
            $this->valor = $query[0]['valor'];
        }
    }
    
    public function validar($taxas){
        $caracter = array('.');
        $msgs = new MsgHelper();
        $id = 0;
        if($taxas['id_protesto_taxas']){
            $id = $taxas['id_protesto_taxas'];
        }
        $this->id_protesto = $taxas['id_protesto'];
        $this->id_taxas = $taxas['id_taxas'];
        $this->valor = str_replace(',', '.', str_replace($caracter,"",  $taxas['valor']));
        $msg = array();
        if(!$this->valor){
            $msg[] = $msgs->erro("Campo valor Obrigatório!");
        }
        /*
        $sql = "SELECT id_protesto_taxas FROM protesto_taxas
                WHERE (id_protesto_taxas <> {$id})
                AND (id_taxas = '{$this->id_taxas}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Tipo de Curso Já Cadastrado!");
        }
        
        */
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
                                      "valor" => $this->valor
            ));
    }
}
