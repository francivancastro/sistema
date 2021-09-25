<?php

class ViewHelper{
    
    public $dados;
    
    public function setDados($dados){
        $this->dados = $dados;
//        echo '<pre>';
//        print_r($dados); die();
//        echo '</pre>';
        //return $this;
        
    }
    public function getDados(){
        return $this->dados;
    }
}

