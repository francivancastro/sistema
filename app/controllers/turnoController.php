<?php

class Turno extends Controller {

    private $turno;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->turno = new TurnoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->turno->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->turno->validar();
            if($validar){
                $this->_redirect->goToControllerAction("turno", 'inserir');
            } else {
                $this->turno->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("turno");
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->turno->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("turno");
            }
            if(!empty($_POST)){
                $validar = $this->turno->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("turno", 'editar');
                }  else {
                    $this->turno->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("turno");
                }
                
            }  else {
              $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("turno");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->turno->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("turno");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("turno");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("turno");
        }
    }
    
}
