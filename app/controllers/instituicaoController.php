<?php

class Instituicao extends Controller{
    
    private $instituicao;
    private $curso;
    private $ctipo;
    private $turno;
    private $status;
    private $estado;
    private $municipio;
    private $_redirect, $_msg, $_session;




    public function init() {
        $this->instituicao = new InstituicaoModel();
        $this->curso = new CursoModel();
        $this->ctipo = new CursotipoModel();
        $this->turno = new TurnoModel();
        $this->status = new InstituicaostatusModel();
        $this->estado =  new EstadoModel();
        $this->municipio = new MunicipioModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_session = new SessionHelper();
    }
    
    public function index_action(){
        
        $this->_session->deletarSession("instituicao");
        $sql = $this->instituicao->listar();
        if(!empty($_POST)){
            $select = "SELECT * FROM instituicao ";
            if($_POST['nome']){
                $busca = $select."WHERE nome LIKE '%{$_POST['nome']}%'";
            } else {
                $this->_msg->informacao("Você precisar digitar um nome!");
                $this->_redirect->goToAction('index');
            }
            
            $sql = $this->instituicao->pesquisar($busca);
            if(count($sql) == 0){
                $this->_msg->informacao("Nenhuma Instituição Encontrado!");
            }
            
            $dados['sql'] = $sql;
        }
        //$this->mostrar($sql);
        $dados['sql'] = $sql;
        $this->view($dados);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $instituicao = $this->instituicao->validar();
            if($instituicao){
                $this->_redirect->goToControllerAction("instituicao", 'inserir');
            } else {
                $this->instituicao->salvar();
                $this->_msg->sucesso("Instituição Salvo com Sucesso!");
                $this->_redirect->goToController("instituicao");
            }
        } else {
            $dados["mun"] = $this->municipio->listar();
            $dados["est"] = $this->estado->listar();
            $dados["iss"] = $this->status->listar();
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->instituicao->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("instituicao");
            }
            if(!empty($_POST)){
                $instituicao = $this->instituicao->validar();
                if($instituicao){
                    $this->_redirect->goToUrl("instituicao/editar/id/$id");
                } else {
                    $this->instituicao->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("instituicao");
                }
                
            }  else {
                $dados["mun"] = $this->municipio->listar();
                $dados["est"] = $this->estado->listar();
                $dados["iss"] = $this->status->listar();
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("instituicao");
        }
    }

    public function visualizar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->instituicao->buscar($id);
            if($sql){
                $dados['sql'] = $sql[0];
                $dados["mun"] = $this->municipio->listar();
                $dados["est"] = $this->estado->listar();
                $dados["iss"] = $this->status->listar();
                $this->view($dados); 
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("instituicao");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("instituicao");
        }
    }
    
    public function excluir() {
        $id = $this->getParam('id');
        if ($id) {
            $instituicao = $this->instituicao->excluir($id);
            if($instituicao){
                $this->_msg->sucesso("Instituicao deletado com Sucesso!");
                $this->_redirect->goToController("instituicao");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("instituicao");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("instituicao");
        }
    }
    
    public function cursos(){
        if (!$this->_session->checkSession("instituicao")){
            $id = $this->getParam("id");
            if($id){
                $this->_session->createSession("instituicao", $id);
                $sql = $this->curso->listaPorInstituicao($id);
                $dados['sql'] = $sql;
                $this->view($dados);
            }  else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("instituicao");
            }
        }  else {
            $id = $_SESSION["instituicao"];
            $sql = $this->curso->listaPorInstituicao($id);
            $dados['sql'] = $sql;
            $this->view($dados);
        }
    }
    
    public function cursoinserir(){
        if(!empty($_POST)){
            $curso = $this->curso->validar();
            if($curso){
                $this->_redirect->goToControllerAction("instituicao", 'cursoinserir');
            } else {
                $this->curso->salvar();
                $this->_msg->sucesso("Curso Salvo com Sucesso!");
                $this->_redirect->goToControllerAction("instituicao", "cursos");
            }
        } else {
            $dados["ctp"] = $this->ctipo->listar();
            $dados["tur"] = $this->turno->listar();
            $this->view($dados);
        }
    }
    
    public function cursoeditar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->curso->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToControllerAction("instituicao", "cursos");
            }
            if(!empty($_POST)){
                $curso = $this->curso->validar();
                if($curso){
                    $this->_redirect->goToUrl("instituicao/cursoeditar/id/$id");
                } else {
                    $this->curso->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToControllerAction("instituicao", "cursos");
                }
                
            }  else {
                $dados["ctp"] = $this->ctipo->listar();
                $dados["tur"] = $this->turno->listar();
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("instituicao", "cursos");
        }
    }
    
    public function cursoexcluir() {
        $id = $this->getParam('id');
        if ($id) {
            $curso = $this->curso->excluir($id);
            if($curso){
                $this->_msg->sucesso("Curso deletado com Sucesso!");
                $this->_redirect->goToControllerAction("instituicao", "cursos");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToControllerAction("instituicao", "cursos");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("instituicao", "cursos");
        }
    }
}

