<?php

class EmpresarefModel extends Model {
    
    public $_tabela = 'empresa_ref';
    public $pkName = 'id_empresa_ref';
    public $fkName = 'id_empresa';
    
    public $id_empresa_ref;
    public $id_empresa;
    public $id_endereco;


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_empresa_ref = $id;
            $this->id_empresa = $query[0]['id_empresa'];
            $this->id_endereco = $query[0]['id_endereco'];
        }
    }
    
    public function listarPorEmpresa($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    
}
