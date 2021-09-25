<?php

class Segurotipo extends Controller {

    private $stipo;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->stipo = new SegurotipoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->stipo->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->stipo->validar();
            if($validar){
                $this->_redirect->goToControllerAction("segurotipo", 'inserir');
            } else {
                $this->stipo->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("segurotipo");
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->stipo->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("segurotipo");
            }
            if(!empty($_POST)){
                $validar = $this->stipo->validar($_POST);
                if($validar){
                    $this->_redirect->goToControllerAction("segurotipo", 'editar');
                }  else {
                    $this->stipo->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("segurotipo");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("segurotipo");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->stipo->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("segurotipo");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("segurotipo");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("segurotipo");
        }
    }
    
}
