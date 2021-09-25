<?php

class Controle extends Controller {

    private $controller;
    private $action;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->controller = new ControllerModel();
        $this->action = new ActionModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        $this->addNavegacao(array('Controller' => "controller/index"));
        //$this->mostrar($this->ftipo);die();
        $sql = $this->controller->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        $this->addNavegacao(array('Controller' => "controller/index", 'Inserir' => 'protesto/inserir'));
        
        if(!empty($_POST)){
            $validar = $this->controller->validar();
            if($validar){
                $this->_redirect->goToControllerAction("controle", 'inserir');
            } else {
                $this->controller->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToControllerAction("controle", 'inserir');
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        
        $id = $this->getParam('id');
        if($id){
            $this->addNavegacao(array('Controller' => "controle/index", 'Editar' => 'controle/editar/id/'.$id));
            $sql = $this->controller->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("controle");
            }
            if(!empty($_POST)){
                $validar = $this->controller->validar();
                if($validar){
                    
                    $this->_redirect->goToUrl("/controle/editar/id/$id");
                }  else {
                    
                    $this->controller->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToUrl("/controle/editar/id/$id");
                }
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToUrl("/controle/editar/id/$id");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            
            $this->addNavegacao(array('Controller' => "controller/index", 'Visualizar' => 'controller/visualizar/id/'.$id));
            $sql = $this->controller->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
                 $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("pais");
            }
            
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("pais");
        }
        
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->controller->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("controller");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("controller");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("controller");
        }
    }
    
    public function action(){
        $id = $this->getParam('id');
        if($id){
             $sql = $this->action->listarPorController($id);
            $this->addNavegacao(array(
                'Controller' => "controle/index",
                'Actions' => "controle/action/id/$id"
                ));
            //$this->mostrar($this->ftipo);die();
            if(!empty($_POST)){
                $c = -1;
                $t_array = array();
                foreach ($_POST['action'] as $ac){
                    $c++;
                    $ac;
                    $t_array[] = array(
                        'id_controller' => $id,
                        'action' => $ac ,
                        'descricao' => $_POST['descricao'][$c]);
                }
                foreach ($t_array as $act){
                    if($this->action->validar($act)){
                        $this->_redirect->goToUrl("/controle/editar/id/$id");
                    }  else {
                        $save = $this->action->salvar();
                    }
                }
                if($save){
                    $this->_msg->sucesso("Taxas adionadas ao protesto!");
                }  else {
                    $this->_msg->erro("Falha ao adionar taxas ao protesto!");
                }
            }
           
            $ctl = new ControllerModel($id);
            $datas['ctl'] = $ctl;
            $datas['sql'] = $sql;
            $this->view($datas);
        }
    }
    
}
