<?php

class PermissaoModel extends Model {
    
    public $_tabela = 'permissao';
    public $pkName  = 'id_permissao';
    public $fkName  = 'id_action';
    public $fkName2  = 'id_grupo';
    
    public $id_permissao;
    public $id_grupo;
    public $id_action;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_permissao = $id;
            $this->id_grupo = $query[0]['id_grupo'];
            $this->id_action = $query[0]['id_action'];
        }
    }
    
    public function listarPorAction($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id);
        }
        return false;
    }
    
    public function listarPorGrupo($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id);
        }
        return false;
    }
    
    public function validar($array){
        $id_permissao = 0;
        if($array['id_permissao']){
            $id_permissao = $array['id_permissao'];
        }
        $this->id_grupo = $array['id_grupo'];
        $this->id_action = $array['id_action'];
        $msg = array();
        if (!$this->id_grupo) {
            $msg[] = "Campo Nome da Taxa Obrigatório!";
        }
        
        if (count($msg)) {
            return $msg;
        }
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array(
            "id_grupo" => $this->id_grupo,
            "id_action" => $this->id_action
        ));
    }
    public function alterar(){
        return $this->atualizar(array(
            "id_grupo" => $this->id_grupo,
            "id_action" => $this->id_action
        ), $this->id_permissao);
    }
    
    public function excluirPorAction($id, $id_grupo){
        if($id){
            return $this->banco->excluir($this->_tabela, $this->fkName."=".$id . " AND id_grupo = {$id_grupo}");
        }
        return false;
    }
    
    public function possuiPermissao($id_grupo, $id_action) {
        $pms = $this->banco->ler($this->_tabela, " id_grupo = {$id_grupo}", null, null, null, ' id_action ="'. $id_action.'"');
        if ($pms) {
            return $pms;
        }
        return false;
    }
    
    public function verificaPermissao($id){
        $pms = $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        if ($pms) {
            return $pms;
        }
    }
}
