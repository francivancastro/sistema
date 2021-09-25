<?php

class Controller extends System {
    
    private $_layout, $auth, $msg, $redirect;
    
    public function __construct() {
        parent::__construct();
        $this->msg = new MsgHelper();
        $this->redirect = new RedirectorHelper();
        $this->auth = new AuthHelper();
        $this->auth->setLoginControllerAction('auth', 'login');
        $this->auth->checkLogin('redirect');
        $this->auth->checkRecup('boolean');
        $this->_layout = new LayoutHelper();
        
        
    }

    protected function view($vars = null){
        if (!$vars) {
            $vars = array();
        }
        $vars["msg"] = $this->msg->mostrarMsg();
        $this->_layout->view($this->_controller, $this->_action, $vars);
        
    }
    
    protected function addNavegacao(Array $array = array()){
        
        $this->_layout->setNav($array);
    }


    public function init(){
        /*
        $perm = new PermissaoModel();
        $actionClass = new ActionModel();
        $controllerClass = new ControllerModel();
        $id_controller = $controllerClass->verificaController($this->_controller);
        if($id_controller){
            $txt_action = $actionClass->verificaAction($id_controller['id_controller'], $this->_action);
            if($txt_action){
                if(!$perm->verificaPermissao($txt_action['id_action'])){
                   $this->msg->erro(".... Voce não possui permissão!");
                }
            } else {
                $this->msg->erro("Action não cadastrada!");
            }
        } else {
            $this->msg->erro("Voce não possui permissão!");
        }
        */
    }
}