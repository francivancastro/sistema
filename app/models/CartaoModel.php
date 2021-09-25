<?php

class CartaoModel extends Model {
    
    public $_tabela = 'cartao';
    public $pkName = 'id_cartao';
    public $fkName = 'id_pagamento';
    
    public $id_cartao;
    public $id_pagamento;
    public $id_cartao_tipo;
    public $bandeira;
    public $codigo;
    public $parcelas;
    public $valor;
    public $data_cartao;


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_cartao = $id;
            $this->id_pagamento = $query[0]['id_pagamento'];
            $this->id_cartao_tipo = $query[0]['id_cartao_tipo'];
            $this->bandeira = $query[0]['bandeira'];
            $this->codigo = $query[0]['codigo'];
            $this->parcelas = $query[0]['parcela'];
            $this->valor = $query[0]['valor'];
            $this->data_cartao = $query[0]['data_cartao'];
        }
    }
    
    public function validar($dados){
        $caracter = array('.');
        $msgs = new MsgHelper();
        $id = 0;
        if($dados['id_cartao']){
            $id = $dados['id_cartao'];
        }
        $this->id_pagamento = $dados['id_pagamento'];
        $this->id_cartao_tipo = $dados['id_cartao_tipo'];
        $this->bandeira = $dados['bandeira'];
        $this->codigo = $dados['codigo'];
        $this->parcelas = $dados['parcelas'];
        $this->valor = str_replace(',', '.', str_replace($caracter,"",  $dados['valor_cartao']));
        $this->data_cartao = UtilHelper::formatUs($dados['data_cartao']);
        $msg = array();
        if(!$this->codigo){
            $msg[] = $msgs->erro("Campo codigo Obrigatório!");
        }
        /*
        $sql = "SELECT id_cartao FROM cartao
                WHERE (id_cartao <> {$id})
                AND (id_cartao_tipo = '{$this->id_cartao_tipo}');";
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
                                      "id_cartao_tipo" => $this->id_cartao_tipo,
                                      "bandeira" => $this->bandeira,
                                      "codigo" => $this->codigo,
                                      "parcelas" => $this->parcelas,
                                      "valor" => $this->valor,
                                      "data_cartao" => $this->data_cartao
            ));
    }
}
