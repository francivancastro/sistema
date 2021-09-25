<?php

class Pagamentotipo extends Controller {

    private $pgtipo;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->pgtipo = new PagamentotipoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        $this->addNavegacao(array('Tipo de Pagamento' => "pagamentotipo/index"));
        //$this->mostrar($this->ftipo);die();
        $sql = $this->pgtipo->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        $this->addNavegacao(array('Tipo de Pagamento' => "pagamentotipo/index", 'Inserir' => 'protesto/inserir'));
        
        if(!empty($_POST)){
            $validar = $this->pgtipo->validar();
            if($validar){
                $dados = array();
                foreach ($validar['mensagens'] as $mm){
                    $this->_msg->erro($mm);
                }
                $dados['ms'] = $validar['mensagens'];
                $dados['cp'] = $validar['campos'];
                
            } else {
                $this->pgtipo->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("pagamentotipo");
            }
            $this->view($dados);
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        
        $id = $this->getParam('id');
        if($id){
            $this->addNavegacao(array('Tipo de Pagamento' => "pagamentotipo/index", 'Editar' => 'pagamentotipo/editar/id/'.$id));
            $sql = $this->pgtipo->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("pagamentotipo");
            }
            if(!empty($_POST)){
                $validar = $this->pgtipo->validar($_POST);
                if($validar){
                    $this->_redirect->goToControllerAction("pagamentotipo", 'editar');
                }  else {
                    $this->pgtipo->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("pagamentotipo");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("pagamentotipo");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            
            $this->addNavegacao(array('Tipo de Pagamento' => "pagamentotipo/index", 'Visualizar' => 'pagamentotipo/visualizar/id/'.$id));
            $sql = $this->pgtipo->buscar($id);
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

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->pgtipo->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("pagamentotipo");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("pagamentotipo");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("pagamentotipo");
        }
    }
    
}
