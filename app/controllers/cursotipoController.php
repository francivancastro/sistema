<?php

class Cursotipo extends Controller {

    private $ctipo;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->ctipo = new CursotipoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->ctipo->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->ctipo->validar($_POST);
            if($validar){
                $this->_redirect->goToControllerAction("cursotipo", 'inserir');
            } else {
                $this->ctipo->inserir($_POST);
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("cursotipo");
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
                $this->_redirect->goToController("cursotipo");
            }
            if(!empty($_POST)){
                $validar = $this->ctipo->validar($_POST);
                if($validar){
                    $this->_redirect->goToControllerAction("cursotipo", 'editar');
                }  else {
                    $this->ctipo->atualizar($_POST, $id);
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("cursotipo");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("cursotipo");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->ctipo->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("cursotipo");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("cursotipo");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("cursotipo");
        }
    }
    
}
