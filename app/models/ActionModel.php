<?php

class ActionModel extends Model {
    
    public $_tabela = 'action';
    public $pkName = 'id_action';
    public $fkName = 'id_controller';
    
    public $id_action;
    public $action;
    public $descricao;
    public $id_controller;

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_action = $query[0]['id_action'];
            $this->action = $query[0]['action'];
            $this->descricao = $query[0]['descricao'];
            $this->id_controller = $query[0]['id_controller'];
        }
    }
    
    public function validar($act){
        $msgs = new MsgHelper();
        $id = 0;
        if($act['id_action']){
            $id = $act['id_action'];
        }
        $this->action = $act['action'];
        $this->descricao = $act['descricao'];
        $this->id_controller = $act['id_controller'];
        $msg = array();
            if (!$this->action) {
                $msg[] = $msgs->erro("Campo Action Obrigatório!");
            }
            if (!$this->id_controller) {
                $msg[] = $msgs->erro("Campo Controller Obrigatório!");
            }
        $sql = "SELECT id_action FROM action
                WHERE (id_action <> {$id})
                AND (action = '{$this->action}')
                AND (id_controller = '{$this->id_controller}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Tipo de Funcionário Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function listarPorController($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }


    public function salvar(){
        return $this->inserir(array(
                        "action" => $this->action,
                        "descricao" => $this->descricao,
                        "id_controller" => $this->id_controller
                ));
    }
    
    public function alterar(){
        return $this->atualizar(array(
                        "action" => $this->action,
                        "descricao" => $this->descricao,
                        "id_controller" => $this->id_controller
                ), $this->id_action);
    }
    
    public function verificaAction($controller, $action) {
        $pms = $this->banco->ler($this->_tabela, " id_controller = {$controller}", null, null, null, ' action ="'.$action.'"');
        if ($pms) {
            foreach ($pms as $pm){
                return $pm;
            }
        }
        return false;
    }
}
