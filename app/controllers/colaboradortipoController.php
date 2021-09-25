<?php

class Colaboradortipo extends Controller {

    private $ctipo;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->ctipo = new ColaboradortipoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ctipo);die();
        $sql = $this->ctipo->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->ctipo->validar();
            if($validar){
                $this->_redirect->goToControllerAction("colaboradortipo", 'inserir');
            } else {
                $this->ctipo->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("colaboradortipo");
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->ctipo->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("colaboradortipo");
            }
            if(!empty($_POST)){
                $validar = $this->ctipo->validar($_POST);
                if($validar){
                    $this->_redirect->goToControllerAction("colaboradortipo", 'editar');
                }  else {
                    $this->ctipo->atualizar($_POST, $id);
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("colaboradortipo");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("colaboradortipo");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->ctipo->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("colaboradortipo");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("colaboradortipo");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("colaboradortipo");
        }
    }
    
}
