<?php

class Areaatuacao extends Controller {

    private $atuacao;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->atuacao = new AreaatuacaoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->atuacao->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->atuacao->validar();
            if($validar){
                $this->_redirect->goToControllerAction("areaatuacao", 'inserir');
            } else {
                $this->atuacao->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("areaatuacao");
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->atuacao->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("areaatuacao");
            }
            if(!empty($_POST)){
                $validar = $this->atuacao->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("areaatuacao", 'editar');
                }  else {
                    $this->atuacao->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("areaatuacao");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("areaatuacao");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->atuacao->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("areaatuacao");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("segurotipo");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("areaatuacao");
        }
    }
    
}
