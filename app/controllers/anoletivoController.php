<?php

class Anoletivo extends Controller {

    private $anoletivo;
    private $_redirect;
    private $_msg;
    private $_session;
    private $serie;
    private $ano;
    private $segmento;

    public function init() {
        $this->anoletivo = new AnoletivoModel();
        $this->_redirect = new RedirectorHelper();
        $this->ano = new AnoModel();
        $this->segmento = new SegmentoModel();
        $this->serie = new SerieModel();
        $this->_msg = new MsgHelper();
        $this->_session = new SessionHelper();
    }

    public function index_action() {
        $this->_session->deletarSession("anoletivo");
        //$this->mostrar($this->ftipo);die();
        $sql = $this->anoletivo->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->anoletivo->validar();
            if($validar){
                $this->_redirect->goToControllerAction("anoletivo", 'inserir');
            } else {
                $this->anoletivo->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("anoletivo");
            }
        } else {
            $dados['ano'] = $this->ano->listar();
            $dados['seg'] = $this->segmento->listar();
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->anoletivo->buscar($id);
            $dados['ano'] = $this->ano->listar();
            $dados['seg'] = $this->segmento->listar();
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("anoletivo");
            }
            if(!empty($_POST)){
                $validar = $this->anoletivo->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("anoletivo", 'editar');
                } else {
                    $this->anoletivo->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("anoletivo");
                }
            } else {
              $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("anoletivo");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->anoletivo->buscar($id);
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
            if($this->anoletivo->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("anoletivo");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("anoletivo");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("anoletivo");
        }
    }
    
    public function serie(){
        if (!$this->_session->checkSession("anoletivo")){
            $id = $this->getParam("id");
            if($id){
                $this->_session->createSession("anoletivo", $id);
                $sql = $this->serie->listarPorAnoletivo($id);
                $dados['sql'] = $sql;
                $this->view($dados);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("anoletivo");
            }
        } else {
            $id = $_SESSION["anoletivo"];
            $sql = $this->serie->listarPorAnoletivo($id);
            $dados['sql'] = $sql;
            $this->view($dados);
        }
    }
    
    public function inserirserie(){
        if ($this->_session->checkSession("anoletivo")){
            if(!empty($_POST)){
                $serie = $this->serie->validar();
                if($serie){
                    $this->_redirect->goToControllerAction("anoletivo", 'inserirserie');
                } else {
                    $this->serie->salvar();
                    $this->_msg->sucesso("Salvo com Sucesso!");
                    $this->_redirect->goToControllerAction("anoletivo", 'inserirserie');
                }
            } else {
                $this->view();
            }
        } else {
            $this->_redirect->goToController("anoletivo");
        }
    }
    
    public function editarserie(){
        $id = $this->getParam('id');
        if($id){
            $idano = $_SESSION['anoletivo'];
            $this->addNavegacao(array(
                'Ano Letivo' => "anoletivo/index", 
                'Serie' => "anoletivo/serie/id/".$idano, 
                'Editar' => 'taxas/editarserie/id/'.$id,
                ));
            $sql = $this->serie->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("taxas");
            }
            if(!empty($_POST)){
                $validar = $this->serie->validar();
                if($validar){
                    $this->_redirect->goToUrl('anoletivo/editarserie/id/'.$id);
                } else {
                    $this->serie->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToUrl('anoletivo/editarserie/id/'.$id);
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("taxas");
        }
    }
    
    public function excluirserie() {
        $id = $this->getParam('id');
        $idano = $_SESSION['anoletivo'];
        if($id){
            
            if($this->serie->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToUrl('anoletivo/serie/id/'.$idano);
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("anoletivo");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("anoletivo");
        }
    }
    
}

