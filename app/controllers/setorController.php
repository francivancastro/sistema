<?php

class Setor extends Controller {

    private $setor;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->setor = new SetorModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->setor->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->setor->validar($_POST);
            if($validar){
                $this->_redirect->goToControllerAction("setor", 'inserir');
            } else {
                $this->setor->inserir($_POST);
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("setor");
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->setor->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("setor");
            }
            if(!empty($_POST)){
                $validar = $this->setor->validar($_POST);
                if($validar){
                    $this->_redirect->goToControllerAction("setor", 'editar');
                }  else {
                    $this->setor->atualizar($_POST, $id);
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("setor");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("setor");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->setor->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("setor");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("setor");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("setor");
        }
    }
    
}
