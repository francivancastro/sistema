<?php

class Menutipo extends Controller{
    
    private $menutipo, $_msg, $_redirect;
    
    public function init() {
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->menutipo = new MenutipoModel();
    }
    
    public function index_action(){
        $sql = $this->menutipo->listarMenutipo();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $this->menutipo->inserirMenutipo($_POST);
             $this->_msg->sucesso("Operação Efetuada com Sucesso!");
             $this->_redirect->goToController("grupo");
        }  else {
            $this->view();
        }
    }
    
    public function excluir() {
        $id = $this->getParam('id');
        $excuir = $this->menutipo->deletarMenutipo($id);
        if($excuir){
            $this->_msg->sucesso("Operação Efetuada com Sucesso!");
            $this->_redirect->goToController("grupo");
        } else {
            $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
            $this->_redirect->goToController("grupo");
        }
            
    }
    
}