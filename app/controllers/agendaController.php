<?php

class Agenda extends Controller {

    private $agenda;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->matriculaespera = new MatriculaesperaModel();
        $this->agenda = new AgendaModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        //$this->mostrar($this->ftipo);die();
        $sql = $this->agenda->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->agenda->validar();
            if($validar){
                $this->_redirect->goToUrl('matriculaespera/visitante/id/'. $_POST['id_turma']);
            } else {
                $this->agenda->salvar();
                $this->_msg->sucesso("Agendamento Realizado com Sucesso!");
                $this->_redirect->goToUrl('matriculaespera/visitante/id/'. $_POST['id_turma']);
            }
        } else {
            $this->view();
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->agenda->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("agenda");
            }
            if(!empty($_POST)){
                if($_POST['tipo_agenda']){
                    $action = strtolower($_POST['tipo_agenda']);
                } else {
                    $action =  'index';
                }
                
                if(isset($_POST['aprovado'])){
                    $_POST['aprovado'] = "S";
                } else {
                    $_POST['aprovado'] = "N";
                }
                
                $status = $_POST["tipo_agenda"].$_POST['aprovado'];
                $validar = $this->agenda->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("agenda", $action);
                } else {
                    $this->agenda->alterar();
                    $this->_msg->sucesso("Salvo com Sucesso!");
                    $this->_redirect->goToControllerAction("agenda", $action);
                }
            }  else {
              $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("agenda");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->agenda->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
                 $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("agenda");
            }
            
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("agenda");
        }
        
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->agenda->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("agenda");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("agenda");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("agenda");
        }
    }
    
    public function nap(){
        $this->addNavegacao(array('Agendamento NAP' => "agenda/nap"));
        $sql = $this->agenda->listarPorTipo('NAP');     
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function vp(){
        $this->addNavegacao(array('Agendamento Visita Pedagógica' => "agenda/vp"));
        $sql = $this->agenda->listarPorTipo('VP');     
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function ps(){
        $this->addNavegacao(array('Agendamento de Processo Seletivo' => "agenda/vp"));
        $sql = $this->agenda->listarPorTipo('PS');     
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function remarcar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->agenda->buscar($id);
            if(!empty($_POST)){
                if(isset($_POST['remarcar'])){
                    if($sql){
                        $validar = $this->agenda->validar();
                        if($validar){
                            $this->_redirect->goToUrl('matriculaespera/visitante/id/'. $_POST['id_turma']);
                        } else {

                            $this->agenda->alterar();
                            $this->_msg->sucesso("Salvo com Sucesso!");
                            $this->_redirect->goToUrl('matriculaespera/visitante/id/'. $_POST['id_turma']);
                        }
                    } else {
                        $this->_redirect->goToUrl('matriculaespera/visitante/id/'. $_POST['id_turma']);
                    }
                } 
                if(isset($_POST['cancelar'])){
                    if($this->agenda->excluir($id)){
                        $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                        $this->_redirect->goToUrl('matriculaespera/visitante/id/'. $_POST['id_turma']);
                    } else {
                        $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                        $this->_redirect->goToUrl('matriculaespera/visitante/id/'. $_POST['id_turma']);
                    }
                }
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("agenda");
        }
    }
}