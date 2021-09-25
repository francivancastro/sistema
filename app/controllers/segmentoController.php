<?php

class Segmento extends Controller {

    private $segmento;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->segmento = new SegmentoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->segmento->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->segmento->validar();
            if($validar){
                $this->_redirect->goToControllerAction("segmento", 'inserir');
            } else {
                $this->segmento->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToControllerAction("segmento", 'inserir');
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->segmento->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("segmento");
            }
            if(!empty($_POST)){
                $validar = $this->segmento->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("segmento", 'editar');
                }  else {
                    $this->segmento->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("segmento");
                }
                
            }  else {
              $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("segmento");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->segmento->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
                 $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("anoletivo");
            }
            
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("anoletivo");
        }
        
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->segmento->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("segmento");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("segmento");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("segmento");
        }
    }
    
}
