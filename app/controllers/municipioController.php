<?php

class Municipio extends Controller {

    private $mun;
    private $est;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->mun = new MunicipioModel();
        $this->est = new EstadoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->mun->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->mun->validar();
            if($validar){
                $this->_redirect->goToControllerAction("municipio", 'inserir');
            } else {
                $this->mun->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("municipio");
            }
        } else {
            $est = $this->est->listar();
            $dados['est'] = $est;
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->mun->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("municipio");
            }
            if(!empty($_POST)){
                $validar = $this->mun->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("municipio", 'editar');
                }  else {
                    $this->mun->atualizar($_POST, $id);
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("municipio");
                }
                
            } else {
                $est = $this->est->listar();
                $dados['est'] = $est;
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("municipio");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->mun->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("municipio");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("municipio");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("municipio");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->mun->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
                 $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("municipio");
            }
            
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("municipio");
        }
        
    }
    
}
