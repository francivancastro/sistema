<?php

class PagamentoModel extends Model {
    
    public $_tabela = 'pagamento';
    public $pkName = 'id_pagamento';
    public $fkName = 'id_protesto';
    
    public $id_pagamento;
    public $id_protesto;
    public $id_pagamento_tipo;


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_pagamento = $id;
            $this->id_protesto = $query[0]['id_protesto'];
            $this->id_pagamento_tipo = $query[0]['id_pagamento_tipo'];
        }
    }
    
    public function validar($dados){
        $msgs = new MsgHelper();
        $id = 0;
        if($dados['id_pagamento']){
            $id = $dados['id_pagamento'];
        }
        $this->id_protesto = $dados['id_protesto'];
        $this->id_pagamento_tipo = $dados['id_pagamento_tipo'];
        $msg = array();
        if(!$this->id_pagamento_tipo){
            $msg[] = $msgs->erro("Informa uma forma de Pagamento!");
        }
        /*
        $sql = "SELECT id_pagamento FROM pagamento
                WHERE (id_pagamento <> {$id})
                AND (id_protesto = '{$this->id_protesto}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Pagamento Já Cadastrado!");
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
        return $this->inserir(array(
            "id_protesto" => $this->id_protesto,
            "id_pagamento_tipo" => $this->id_pagamento_tipo,
        ));
    }
}
