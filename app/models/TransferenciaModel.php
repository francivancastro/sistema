<?php

class TransferenciaModel extends Model {
    
    public $_tabela = 'transferencia';
    public $pkName = 'id_transferencia';
    public $fkName = 'id_pagamento';
    
    public $id_transferencia;
    public $id_pagamento;
    public $agencia_cliente;
    public $conta_cliente;
    public $agencia_empresa;
    public $conta_empresa;
    public $banco_empresa;
    public $banco_cliente;
    public $valor;
    public $data_transferencia;


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_transferencia = $id;
            $this->id_pagamento = $query[0]['id_pagamento'];
            $this->agencia_cliente = $query[0]['agencia_cliente'];
            $this->agencia_empresa = $query[0]['agencia_empresa'];
            $this->conta_cliente = $query[0]['conta_cliente'];
            $this->conta_empresa = $query[0]['conta_empresa'];
            $this->banco_empresa = $query[0]['banco_empresa'];
            $this->banco_cliente = $query[0]['banco_cliente'];
            $this->valor = $query[0]['valor'];
            $this->data_transferencia = $query[0]['data_transferencia'];
        }
    }
    
    public function validar($dados){
        $caracter = array('.');
        $msgs = new MsgHelper();
        $id = 0;
        if($taxas['id_transferencia']){
            $id = $taxas['id_transferencia'];
        }
        $this->id_pagamento = $dados['id_pagamento'];
        $this->agencia_cliente = $dados['agencia_cliente'];
        $this->agencia_empresa = $dados['agencia_empresa'];
        $this->conta_cliente = $dados['conta_cliente'];
        $this->conta_empresa = $dados['conta_empresa'];
        $this->banco_empresa = $dados['banco_empresa'];
        $this->banco_cliente = $dados['banco_cliente'];
        $this->valor = str_replace(',', '.', str_replace($caracter,"",  $dados['valor_transferencia']));
        $this->data_transferencia = UtilHelper::formatUs($dados['data_transferencia']);
        $msg = array();
        if(!$this->agencia_cliente){
            $msg[] = $msgs->erro("Campo agencia Obrigatório!");
        }
        /*
        $sql = "SELECT id_transferencia FROM transferencia
                WHERE (id_transferencia <> {$id}));";
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
                                    "agencia_cliente" => $this->agencia_cliente,
                                    "agencia_empresa" => $this->agencia_empresa,
                                    "conta_cliente" => $this->conta_cliente,
                                    "conta_empresa" => $this->conta_empresa,
                                    "banco_empresa" => $this->banco_empresa,
                                    "banco_cliente" => $this->banco_cliente,
                                    "valor" => $this->valor,
                                    "data_transferencia" => $this->data_transferencia,
            ));
    }
}
