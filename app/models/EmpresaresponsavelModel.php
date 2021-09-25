<?php

class EmpresaresponsavelModel extends Model {
    
    public $_tabela = 'empresa_responsavel';
    public $pkName = 'id_empresa_responsavel';
    public $fkName = 'id_responsavel';
    
    public $id_empresa_responsavel;
    public $id_responsavel;
    public $id_empresa;


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_empresa_responsavel = $id;
            $this->id_responsavel = $query[0]['id_responsavel'];
            $this->id_empresa = $query[0]['id_empresa'];
        }
    }
    
    public function listarPorResponsavel($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function salvarNovo($ide){
        if($ide){
            $res = new ResponsavelModel();
            return $this->inserir(
                    array("id_responsavel" => $res->pegaUltimoId(),
                          "id_empresa" => $ide,
            ));
        }
        return false;
    }
    
    public function salvar(){
        
    }
}
