<?php

class Funcao extends Controller {

    private $db;
    public $_session;
    public $_redirect;
    public $_msg;
    
    public function init() {
        $this->db = new FuncaoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_session = new SessionHelper();
        $this->_session->checkSession("userAuth");
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        $sql = $this->db->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $this->db->inserirFuncao($_POST);
             $this->_msg->sucesso("Operação Efetuada com Sucesso!");
             $this->_redirect->goToController("funcao");
        }  else {
            $this->view();
        }
    }
    
    public function visualizar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->db->buscar($id);
            if ($sql){
                $datas['sql'] = $sql[0];
                $this->view($datas);
            }  else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("funcao");
            }
        } else {
            $this->_msg->erro("Pagina não encontrada");
            $this->_redirect->goToController("funcao");
        }
        //$this->mostrar($sql);
    }

    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->db->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            }  else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("funcao");
            }
            if(!empty($_POST)){
                $this->db->alterar($_POST, $id);
                 $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                 $this->_redirect->goToController("funcao");
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Pagina não encontrada");
            $this->_redirect->goToController("funcao");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        $db = $this->db;
        $db->deletarFuncao($id);
        if($db->deletarFuncao($id)){
            $this->_msg->sucesso("Operação Efetuada com Sucesso!");
            $this->_redirect->goToController("funcao");
        } else {
            $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
            $this->_redirect->goToController("funcao");
        }
            
    }

}
