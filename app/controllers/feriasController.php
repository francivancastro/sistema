<?php

class Ferias extends Controller {

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
        $db = $this->db;
        $sql = $db->listarFerias();
        $datas['sql'] = $sql;
        $this->view('ferias', $datas);
    }
    
    public function inserir(){
        $this->view('feriasInserir');
        if(!empty($_POST)){
            $this->db->inserirFuncao($_POST);
             $this->_msg->sucesso("Operação Efetuada com Sucesso!");
             $this->_redirect->goToController("ferias");
        }  else {
            //msg de erro
        }
    }
    
    public function visualizar(){
        $id = $this->getParam('id');
        if($id){
            $db = $this->db;
            $sql = $db->busca($id);
            if($sql){
                $datas['sql'] = $sql[0];
                $this->view('feriasVisualizar', $datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("ferias");
            }
        } else {
            $this->_msg->erro("Pagina não encontrada");
            $this->_redirect->goToController("ferias");
        }
        //$this->mostrar($sql);
    }

    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $db = $this->db;
            $sql = $db->busca($id);
            if($sql){
                //$this->mostrar($sql);   
                $datas['sql'] = $sql[0];
                $this->view('feriasEditar', $datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("ferias");
            }
            if(!empty($_POST)){
                $db->atualizarFerias($_POST, $id);
                 $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                 $this->_redirect->goToController("ferias");
            }  else {
                //msg de erro
            }
        } else {
            $this->_msg->erro("Pagina não encontrada");
            $this->_redirect->goToController("ferias");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        $db = $this->db;
        $db->deletarFuncao($id);
        if($db->deletarFerias($id)){
            $this->_msg->sucesso("Operação Efetuada com Sucesso!");
            $this->_redirect->goToController("ferias");
        } else {
            $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
            $this->_redirect->goToController("ferias");
        }
            
    }

}
