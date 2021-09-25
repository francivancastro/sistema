<?php

class Pais extends Controller {

    private $pais;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->pais = new PaisModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->pais->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->pais->validar();
            if($validar){
                $this->_redirect->goToControllerAction("pais", 'inserir');
            } else {
                $this->pais->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("pais");
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->pais->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("pais");
            }
            if(!empty($_POST)){
                $validar = $this->pais->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("pais", 'editar');
                }  else {
                    $this->pais->atualizar($_POST, $id);
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("pais");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("pais");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->pais->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("pais");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("pais");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("pais");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->pais->buscar($id);
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
    
}
