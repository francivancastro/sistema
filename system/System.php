<?php

class System{
    private $_msg;
    private $_redirect;
    //private $_session;
    private $_url;
    private $_explode;
    public $_controller;
    public $_action;
    public $_params;
    

    public function __construct() {
        $this->_msg = new MsgHelper();
        $this->_redirect = new RedirectorHelper();
        //$this->_session = new SessionHelper();
        $this->setUrl();
        $this->setExplode();
        $this->setController();
        $this->setAction();
        $this->setParams();
    }
    
    private function setUrl(){
        $_GET['url'] = (isset($_GET['url']) ? $_GET['url'] : 'index/index_action' );
        $this->_url = $_GET['url'];
    }
    
    private function setExplode(){
        $this->_explode = explode('/', $this->_url);
    }
    
    public function setController(){
        $this->_controller = $this->_explode[0];
    }
    
    public function setAction(){
        $ac = (!isset($this->_explode[1]) || $this->_explode[1] == null || $this->_explode[1] == 'index' ? 'index_action' : $this->_explode[1]);
        $this->_action = $ac;
    }   
    
    public function setParams(){
        unset($this->_explode[0], $this->_explode[1]); 
        if(end($this->_explode) == null){
            array_pop($this->_explode);
        }
        $i = 0;
        if(!empty($this->_explode)){
            foreach ($this->_explode as $val){
                if($i % 2 == 0){
                    $ind[] = $val;
                } else {
                    $value[] = $val;
                }
                $i++;
            }
        } else {
            $ind = array();
            $value = array();
        }
        if(count($ind) == count($value) && !empty($ind) && !empty($value) ){
            $this->_params = array_combine($ind, $value);
        }  else {
            $this->_params = array();
        }
        
        
    }
    
    public function getParam($name = null){
        if( $name != null ){
            if(array_key_exists($name, $this->_params)){
                return $this->_params[$name];
            } else{
                return false;
            }
        } else {
            return $this->_params;
        }
    }
    
    public function run(){
        $controller_path = CONTROLLERS . $this->_controller . 'Controller.php';
        if(!file_exists($controller_path)){
            $this->_msg->erro("Ouve um erro Controller não Existe");
            $this->_redirect->goToController("index");
            //die("Ouve um erro Controller não Existe");
        }
        require_once ($controller_path);
        $app = new $this->_controller();
        
        if(!method_exists($app, $this->_action)){
            $this->_msg->erro("Ouve um erro action não Existe");
            $this->_redirect->goToController("index");
           // die("Ouve um erro. A action não existe.");
        }
            
        $action = $this->_action;
        $app->init();
        $app->$action();
    }
    
    public function mostrar($texto){
        echo "<pre>";
        print_r($texto);
        echo "</pre>";
    }
}
