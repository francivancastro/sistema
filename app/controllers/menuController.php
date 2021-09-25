<?php

class Menu extends Controller{
    
    private $mps, $mts, $menu, $_redirect;
    
    public function init() {
        $this->menu = new MenuModel();
        $this->_redirect = new RedirectorHelper();
        $this->_session = new SessionHelper();
        $this->_session->checkSession("userAuth");
        $this->_msg = new MsgHelper();
        $this->mps = new MenuposicaoModel();
        $this->mts = new MenutipoModel();
    }
    
    public function index_action(){
        $sql = $this->menu->listarMenu();
        $datas['sql'] = $sql;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $this->menu->inserirMenu($_POST);
             $this->_msg->sucesso("Operação Efetuada com Sucesso!");
             $this->_redirect->goToController("menu");
        }  else {
            $mts = $this->mts->listarMenutipo();
            $mps = $this->mps->listarMenuposicao();
            $mss = $this->menu->listarMenu();
            $dados['mts'] = $mts;
            $dados['mps'] = $mps;
            $dados['mss'] = $mss;
            $this->view($dados);
        }
    }
}