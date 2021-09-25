<?php 

abstract class Model {
    
    public $_tabela;
    public $pkName;
    protected $banco;
    
    
    final public function listar(){
        return $this->banco->ler($this->_tabela);
    }
    
    final public function listarPorPagina($where = null , $limit = null, $offset = null,  $orderby = null){
        return $this->banco->ler($this->_tabela, $where, $limit, $offset);
    }
    
    final public function pesquisar($sql){
        return $this->banco->busca($sql);
    }

    final public function inserir($dados){
        return $this->banco->inserir($this->_tabela, $dados);
    }
    
    final public function buscar($id){
        return $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
    }

    final public function atualizar($dados, $id){
        return $this->banco->atualizar($this->_tabela, $dados, $this->pkName.'='.$id);
    }
    
    final public function atualizaUnico($dados, $id, $and = null){
        return $this->banco->atualizar($this->_tabela, $dados, $id , $and );
    }
    
    final public function excluir($id){
        return $this->banco->excluir($this->_tabela, $this->pkName.'='.$id);
    }
    final public function consultar($sql){
        return $this->banco->consultar($sql);
    }
    
    final public function pegaUltimoId(){
        return $this->banco->pegaUltimoId($this->pkName, $this->_tabela);
    }
    
    final public function transacao(){
        return $this->banco->transacaoStart();
    }
    
    final public function refazer(){
        return $this->banco->transacaoRefazer();
    }
    
    final public function save(){
        return $this->banco->transacaoSalvar();
    }
}