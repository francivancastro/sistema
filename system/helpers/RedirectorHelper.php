<?php

class RedirectorHelper{
    
    protected $paremetros = array();
    
    protected function go( $data ){
        header("Location:/sistema/".$data);
    }

    public function setUrlParameter($nome , $value){
        $this->paremetros[$nome] =  $value;
        return $this;
    }
    
    protected function getUrlParameter(){
        $params =  "";
        foreach ($this->paremetros as $nome => $valor){
            $params .= $nome.'/'.$valor.'/'; 
        }
        return $params;
       
    }
    
    public function  goToController($controler){
        $this->go($controler . '/index/' . $this->getUrlParameter());
    }
    
    public function  goToAction($action){
        $this->go($this->getCurrentController() . '/' . $action . '/' . $this->getUrlParameter());
    
    }
    
    public function  goToControllerAction(  $controller, $action  ){
        $this->go($controller . '/' . $action . '/' . $this->getUrlParameter());
    }
    
    public function  goToIndex(){
        $this->goToController('index');
    }
    
    public function  goToUrl($url){
        header("Location:/sistema/".$url);
    }
    
    public function getCurrentController(){
        global  $start;
        return $start->_controller;
    }
    public function getCurrentAction(){
        global  $start;
        return $start->_action;
    }
}
