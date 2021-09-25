<?php

class PessoarefModel extends Model {
    
    public $_tabela = 'pessoa_ref';
    public $pkName = 'id_pessoa_ref';
    public $fkName = 'id_pessoa';
    
    public $id_pessoa_ref;
    public $id_pessoa;
    public $id_endereco;


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_pessoa_ref = $id;
            $this->id_pessoa = $query[0]['id_pessoa'];
            $this->id_endereco = $query[0]['id_endereco'];
        }
    }
    
    public function listarPorPessoa($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function deletePorPessoa($id){
        if($id){
            return $this->banco->excluir($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }

    public function salvar(){
        $end = new EnderecoModel();
        $pes = new PessoaModel();
        return $this->inserir(array("id_pessoa" => $pes->pegaUltimoId(),
                                    "id_endereco" => $end->pegaUltimoId()));
    }
    
    public function salvarRef(){
        $end = new EnderecoModel();
        return $this->inserir(array("id_pessoa" => $_POST['id_pessoa'],
                                    "id_endereco" => $end->pegaUltimoId()));
    }
    
}
