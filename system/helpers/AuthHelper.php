<?php

class AuthHelper{
    
    protected $sessionHelper,  $redirectorHelper, $tableName, $userColumn,
              $passColumn, $user, $pass, $loginController = 'index', $loginAction = 'index',
              $logoutController = 'index', $logoutAction = 'index', $nome, $cpf, $data ;
    private $_msg;
    public $banco;


    public function __construct() {
        $this->banco = Banco::instanciar();
        $this->_msg = new MsgHelper();
        $this->sessionHelper = new SessionHelper();
        $this->redirectorHelper = new RedirectorHelper();
        return $this;
    }
    
    public function setTableName($val){
        $this->tableName = $val;
        return $this;
    }
    
    public function setUserConlumn($val){
        $this->userColumn = $val;
        return $this;
    }
    
    public function setPassColumn($val){
        $this->passColumn = $val;
        return $this;
    }
    
    public function setUser($val){
        $this->user = $val;
        return $this;
    }
    public function setPass($val){
        $this->pass = $val;
        return $this;
    }
    public function setNome($val){
        $this->nome = $val;
        return $this;
    }
    public function setCpf($val){
        $caracter = array(".","-");
        $this->cpf = str_replace($caracter, "", $val);
        return $this;
    }
    public function setData($val){
        if($val){
            $this->data = UtilHelper::formatUs($val);
        }
        return $this;
    }
    
    public function setLoginControllerAction($controller, $action){
        $this->loginController = $controller;
        $this->loginAction = $action;
        return $this;
    }
    
    public function setLogoutControllerAction($controller, $action){
        $this->logoutController = $controller;
        $this->logoutAction = $action;
        return $this;
    }
    
    public function login(){
        $where = $this->userColumn ."='".$this->user."' AND ".$this->passColumn."='".$this->pass."'";
        $sql = $this->banco->ler($this->tableName, $where, '1');
        
        $msg = array();
        if(count($sql) > 0){
            $this->sessionHelper->createSession("userAuth", true)
                                ->createSession("userData", $sql[0]);
        } else {
            $msg[] = $this->_msg->erro("Usuário ou Senha Invalido!");
        }
        if(count($msg)){
            return $msg;
        }
        $this->redirectorHelper->goToControllerAction($this->loginController, $this->loginAction);
        return $this;
    }
    
    public function recup(){
        $msg = array();
        $where = " nome ="."'".$this->nome ."' AND cpf ="."'".$this->cpf."' AND data_nascimento = "."'".$this->data."'";
        
        $sql = $this->banco->ler("pessoa", $where, '1');
        
        if(count($sql) > 0){
            $this->sessionHelper->createSession("userRecup", true)
                                ->createSession("userPes", $sql[0]);
        } else {
           $msg[] = $this->_msg->erro("Falha ao Recuperar Senha!");
           $msg[] = $this->_msg->informacao("Verifique se os campos foram preenchidos corretamente.");
        }
        if(count($msg)){
            return $msg;
        }
        $this->redirectorHelper->goToControllerAction($this->loginController, $this->loginAction);
        return $this;
    }
    
    public function logout(){
        $this->sessionHelper->deletarSession("userAuth")
                            ->deletarSession("userRecup")
                            ->deletarSession("userData");
        $this->redirectorHelper->goToControllerAction($this->logoutController, $this->logoutAction);
        return $this;
    }
    
    public function checklogin( $action ){
        switch ($action){
            case "boolean":
                if (!$this->sessionHelper->checkSession("userAuth")){
                    return false;
                } else {
                    return true;
                }
                break;
            case "redirect":
                if (!$this->sessionHelper->checkSession("userAuth")) {
                    if ($this->redirectorHelper->getCurrentController() != $this->loginController || $this->redirectorHelper->getCurrentAction() != $this->loginAction) {
                        $this->redirectorHelper->goToControllerAction($this->loginController, $this->loginAction);
                    }
                }
                break;
            case "stop":
                 if (!$this->sessionHelper->checkSession("userAuth")) {
                     exit;
                 }
                break;
        }
    }
    
    public function checkRecup( $action ){
        switch ($action){
            case "boolean":
                if (!$this->sessionHelper->checkSession("userRecup")){
                    return false;
                } else {
                    return true;
                }
                break;
            case "redirect":
                if (!$this->sessionHelper->checkSession("userRecup")) {
                    if ($this->redirectorHelper->getCurrentController() != $this->loginController || $this->redirectorHelper->getCurrentAction() != $this->loginAction) {
                        $this->redirectorHelper->goToControllerAction($this->loginController, $this->loginAction);
                    }
                }
                break;
            case "stop":
                 if (!$this->sessionHelper->checkSession("userRecup")) {
                     exit;
                 }
                break;
        }
    }
    
    public function userData($key){
        $s =  $this->sessionHelper->selectSession("userData");
        return $s[$key];
    }
}

