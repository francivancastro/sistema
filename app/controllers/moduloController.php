<?php

class Modulo extends Controller {

    private $action, $controller;
    public $_redirect;
    public $_msg;
    
    public function init() {
        $this->action = new ActionModel();
        $this->controller = new ControllerModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        $sql = $this->action->listar();
        //$sql = $this->controller->listarController();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $this->action->inserir($_POST);
            $this->_msg->sucesso("Operação Efetuada com Sucesso!");
            $this->_redirect->goToController("modulo");
        }  else {
            $sql = $this->controller->listar();
            $datas['sql'] = $sql;
            $this->view($datas);
        }
    }
    
    public function visualizar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->action->buscar($id);
            if($sql){
                $datas['sql'] = $sql[0];
                $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("modulo");
            }
        } else {
            $this->_msg->erro("Pagina não encontrada");
            $this->_redirect->goToController("modulo");
        }
        //$this->mostrar($sql);
    }

    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->action->buscar($id);
            if(!$sql){
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("modulo");
            }
            if(!empty($_POST)){
                $this->action->atualizar($_POST, $id);
                 $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                 $this->_redirect->goToController("modulo");
            }  else {
                $con = $this->controller->listar();
                $datas['con'] = $con;
                $datas['sql'] = $sql[0];
                $this->view($datas);
            }
        } else {
            $this->_msg->erro("Pagina não encontrada");
            $this->_redirect->goToController("modulo");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            $excluir = $this->action->excluir($id);
            if($excluir){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("modulo");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("modulo");
            }
        }
            
    }
    
    public function index_controller() {
        $sql = $this->controller->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir_controller(){
        if(!empty($_POST)){
            $this->controller->inserirController($_POST);
            $this->_msg->sucesso("Operação Efetuada com Sucesso!");
            $this->_redirect->goToControllerAction("modulo", "index_controller");
        }  else {
            $this->view();
        }
    }

}


