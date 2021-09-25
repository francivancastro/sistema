<?php

class Grupo extends Controller{
    
    private $grupo, $permissao,$controller,$action, $_redirect;
    
    public function init() {
        parent::init();
        $this->grupo = new GrupoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->permissao = new PermissaoModel();
        $this->controller = new ControllerModel();
        $this->action = new ActionModel();
    }
    
    public function index_action(){
        $sql = $this->grupo->listar();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $grupo = $this->grupo->validar($_POST);
            if($grupo){
                $this->_redirect->goToUrl("grupo/inserir");
            } else {
                $this->grupo->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                 $this->_redirect->goToUrl("grupo/inserir");
            }
        }  else {
            $this->view();
        }
    }
    
    public function excluir() {
        $id = $this->getParam('id');
        $excuir = $this->grupo->deletarGrupo($id);
        if($excuir){
            $this->_msg->sucesso("Operação Efetuada com Sucesso!");
            $this->_redirect->goToController("grupo");
        } else {
            $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
            $this->_redirect->goToController("grupo");
        }
            
    }
    
    public function permissao(){
        $id_grupo = $this->getParam('id');
        if($id_grupo){
            $gp = new GrupoModel($id_grupo);
            $dados['grupo'] = $gp->nome;
            $per =  $this->permissao->listarPorGrupo($id_grupo);
            $sql = $this->controller->listar();
            $listaction = $this->action->listar();
            $dados['id_grupo'] = $id_grupo;
            $dados['sql'] = $sql;
            $dados['per'] = $per;
            
            if(!empty($_POST)){
                $actions = $_POST['id_action'];
                foreach ($listaction as $cha => $perms){
                    if (!in_array($perms["id_action"], $actions)) {
                        $id_ac = $perms["id_action"];
                        $this->permissao->excluirPorAction($id_ac, $id_grupo);
                    }
                }
                foreach ($actions as $act) {
                    $array = array(
                        'id_action' => $act,
                        'id_grupo' => $id_grupo
                    );
                    $validar = $this->permissao->validar($array);
                    if($validar){
                       $this->_redirect->goToUrl("grupo/permissao/id/$id_grupo"); 
                    }  else {
                        $this->permissao->salvar();
                    }
                }
                $this->_msg->sucesso("Salvo com Sucesso!"); 
                $this->view($dados);
            }  else {
                $this->view($dados);
            }
        }
    }
}