<?php

class Instituicaostatus extends Controller {

    private $istatus;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->istatus = new InstituicaostatusModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->istatus->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->istatus->validar($_POST);
            if($validar){
                $this->_redirect->goToControllerAction("instituicaostatus", 'inserir');
            } else {
                $this->istatus->inserir($_POST);
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("instituicaostatus");
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->istatus->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("instituicaostatus");
            }
            if(!empty($_POST)){
                $validar = $this->istatus->validar($_POST);
                if($validar){
                    $this->_redirect->goToControllerAction("instituicaostatus", 'editar');
                }  else {
                    $this->istatus->atualizar($_POST, $id);
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("instituicaostatus");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("instituicaostatus");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->istatus->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("instituicaostatus");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("instituicaostatus");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("instituicaostatus");
        }
    }
    
}
