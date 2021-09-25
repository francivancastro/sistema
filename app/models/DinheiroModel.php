<?php

class DinheiroModel extends Model {
    
    public $_tabela = 'dinheiro';
    public $pkName = 'id_dinheiro';
    public $fkName = 'id_pagamento';
    
    public $id_dinheiro;
    public $id_pagamento;
    public $id_taxas;
    public $valor;
    public $data_dinheiro;


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_dinheiro = $id;
            $this->id_pagamento = $query[0]['id_pagamento'];
            $this->valor = $query[0]['valor'];
            $this->data_dinheiro = $query[0]['data_dinheiro'];
        }
    }
    
    public function validar($taxas){
        $caracter = array('.');
        $msgs = new MsgHelper();
        $id = 0;
        if($taxas['id_dinheiro']){
            $id = $taxas['id_dinheiro'];
        }
        $this->id_pagamento = $taxas['id_pagamento'];
        $this->valor = str_replace(',', '.', str_replace($caracter,"",  $taxas['valor_dinheiro']));
        $this->data_dinheiro = UtilHelper::formatUs($taxas['data_dinheiro']);
        $msg = array();
        if(!$this->valor){
            $msg[] = $msgs->erro("Campo valor Obrigatório!");
        }
        /*
        $sql = "SELECT id_dinheiro FROM dinheiro
                WHERE (id_dinheiro <> {$id})
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
    
    public function listarPorPagamento($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array("id_pagamento" => $this->id_pagamento,
                                      "valor" => $this->valor,
                                      "data_dinheiro" => $this->data_dinheiro
            ));
    }
}
