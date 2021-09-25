<?php

class Bairro extends Controller {

    private $bai;
    private $mun;
    private $_redirect;
    private $_msg;
    private $paginador;

    public function init() {
        $this->bai = new BairroModel();
        $this->mun = new MunicipioModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        $page = $this->getParam("page");
        if(!$page){
            $page = 1;
        }
        //$this->mostrar($this->ftipo);die();
        $tt = $this->bai->listarPorPagina();
        $total = count($tt);
        $registro = 6;
        $numero = ceil($total/$registro);
        $inicio = ($registro * $page) -1;
        $sql = $this->bai->listarPorPagina(null, $inicio.",".$registro);
        $datas['numero'] = $numero;
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->bai->validar();
            if($validar){
                $this->_redirect->goToControllerAction("bairro", 'inserir');
            } else {
                $this->bai->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("bairro");
            }
        } else {
            $mun = $this->mun->listar();
            $dados['mun'] = $mun;
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->bai->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("bairro");
            }
            if(!empty($_POST)){
                $validar = $this->bai->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("bairro", 'editar');
                }  else {
                    $this->bai->atualizar($_POST, $id);
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("bairro");
                }
                
            } else {
                $mun = $this->mun->listar();
                $dados['mun'] = $mun;
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("bairro");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->bai->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("bairro");
            } else {
                $this->_msg->erro("Existe Registros vinculador a Este Registro!");
                $this->_redirect->goToController("bairro");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("bairro");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->bai->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
                 $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("bairro");
            }
            
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("bairro");
        }
        
    }
    
}
