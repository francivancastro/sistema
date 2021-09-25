<?php

class Matriculastatus extends Controller {

    private $mstatus;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->mstatus = new MatriculastatusModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->mstatus->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->mstatus->validar();
            if($validar){
                $this->_redirect->goToControllerAction("matriculastatus", 'inserir');
            } else {
                $this->mstatus->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToControllerAction("matriculastatus", 'inserir');
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->mstatus->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("matriculastatus");
            }
            if(!empty($_POST)){
                $validar = $this->mstatus->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("matriculastatus", 'editar');
                }  else {
                    $this->mstatus->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("matriculastatus");
                }
                
            }  else {
              $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("matriculastatus");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->mstatus->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
                 $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("matriculastatus");
            }
            
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("matriculastatus");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->mstatus->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("matriculastatus");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("matriculastatus");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("matriculastatus");
        }
    }
    
}

