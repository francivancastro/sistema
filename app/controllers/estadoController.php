<?php

class Estado extends Controller {

    private $est;
    private $pais;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->est = new EstadoModel();
        $this->pais = new PaisModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->est->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->est->validar();
            if($validar){
                $this->_redirect->goToControllerAction("estado", 'inserir');
            } else {
                $this->est->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("estado");
            }
        } else {
            $pais = $this->pais->listar();
            $dados['pais'] = $pais;
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->est->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("estado");
            }
            if(!empty($_POST)){
                $validar = $this->est->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("estado", 'editar');
                }  else {
                    $this->est->atualizar($_POST, $id);
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("estado");
                }
                
            } else {
                $pais = $this->pais->listar();
                $dados['pais'] = $pais;
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("estado");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->est->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("estado");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("estado");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("estado");
        }
    }
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->est->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
                 $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("estado");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("estado");
        }
        $pais = $this->pais->listar();
        $dados['pais'] = $pais;
        $this->view($dados);
    }
    
}
