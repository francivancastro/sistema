<?php

class Taxas extends Controller {

    private $taxas;
    private $_redirect;
    private $_msg;

    public function init() {
        parent::init();
        $this->taxas = new TaxasModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        $this->addNavegacao(array('Taxas' => "taxas/index"));
        //$this->mostrar($this->ftipo);die();
        $sql = $this->taxas->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        $this->addNavegacao(array('Taxas' => "taxas/index", 'Inserir' => 'protesto/inserir'));
        
        if(!empty($_POST)){
            $validar = $this->taxas->validar();
            if($validar){
                $dados = array();
                foreach ($validar['mensagens'] as $mm){
                    $this->_msg->erro($mm);
                }
                $dados['ms'] = $validar['mensagens'];
                $dados['cp'] = $validar['campos'];
                
            } else {
                $this->taxas->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("taxas");
            }
            $this->view($dados);
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        
        $id = $this->getParam('id');
        if($id){
            $this->addNavegacao(array('Taxas' => "taxas/index", 'Editar' => 'taxas/editar/id/'.$id));
            $sql = $this->taxas->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("taxas");
            }
            if(!empty($_POST)){
                $validar = $this->taxas->validar($_POST);
                if($validar){
                    $this->_redirect->goToControllerAction("taxas", 'editar');
                }  else {
                    $this->taxas->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("taxas");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("taxas");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            
            $this->addNavegacao(array('Taxas' => "taxas/index", 'Visualizar' => 'taxas/visualizar/id/'.$id));
            $sql = $this->taxas->buscar($id);
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
            if($this->taxas->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("taxas");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("taxas");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("taxas");
        }
    }
    
}
