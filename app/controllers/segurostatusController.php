<?php

class Segurostatus extends Controller {

    private $sstatus;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->sstatus = new SegurostatusModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->sstatus->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->sstatus->validar($_POST);
            if($validar){
                $this->_redirect->goToControllerAction("segurostatus", 'inserir');
            } else {
                $this->sstatus->inserir($_POST);
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("segurostatus");
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->sstatus->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("segurostatus");
            }
            if(!empty($_POST)){
                $validar = $this->sstatus->validar($_POST);
                if($validar){
                    $this->_redirect->goToControllerAction("segurostatus", 'editar');
                }  else {
                    $this->sstatus->atualizar($_POST, $id);
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("segurostatus");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("segurostatus");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->sstatus->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("segurostatus");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("segurostatus");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("segurostatus");
        }
    }
    
}
