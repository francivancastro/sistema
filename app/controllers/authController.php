<?php

class Auth extends Controller{
    
    private $auth, $db, $_msg, $_redirect, $_session; 
    
    public function init() {
        $this->_msg = new MsgHelper();
        $this->auth = new AuthHelper();
        $this->db = new AdminModel();
        $this->_session = new SessionHelper();
//        $this->auth->setLoginControllerAction('auth', 'login');
//        $this->auth->checkLogin('redirect');
        $this->_redirect = new RedirectorHelper();
    }
    
    public function index_action(){
        echo '6e563844f4bb626750c135b1717572cd!';
    }
    
    public function login(){
        $acao = $this->getParam('acao');
        if(!empty($_POST)){
            switch ($acao){
                case 'logar':
                    $caracter = array("'",'"','-','.',',','*','+','+');
                    $login = str_replace($caracter , "", $_POST["login"]);
                    $this->auth->setTableName("usuario")
                       ->setUserConlumn("login")
                       ->setPassColumn("senha")
                       ->setUser($login)
                       ->setPass(md5($_POST["senha"]))       
                       ->setLoginControllerAction("index", "index")
                       ->login();
                    break;
                case 'recup':
                    $this->auth->setNome($_POST["nome"])
                           ->setCpf($_POST["cpf"])
                           ->setData($_POST["data"])
                           ->setLoginControllerAction("auth", "recuperar")
                           ->recup();
                   break;
                case 'recuperar':
                    $usu = new UsuarioModel();
                    $usuario = $usu->validar();
                    if(!$usuario){
                        $usu->alterar();
                        $this->_session->deletarSession('userRecup');
                        $this->_msg->sucesso("Senha Alterada Com Sucesso!");
                    }
                    break;
            }
        }
        $this->view();
    }
    
    public function logout(){
        $this->_session->deletarSession('anobase');
        $this->_msg->sucesso("Deslogado com Sucesso!");
        $this->auth->setLogoutControllerAction("index", "index")
                   ->logout();
        $this->view();
    }
    
    public function recuperar(){
        if(!empty($_POST)){
        }
    }
}
