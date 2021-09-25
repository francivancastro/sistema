<?php

class SessionHelper{
    
    public function createSession($nome, $valor){
        $_SESSION[$nome] = $valor;
        return $this;
    }
    
    public function selectSession($nome){
        if ($this->checkSession($nome)) {
            return $_SESSION[$nome];
        }
        return false;
    }
    
    public function deletarSession($nome){
        unset( $_SESSION[$nome] );
        return $this;
    }
        
    public function checkSession($nome){
        return isset( $_SESSION[$nome] );
    }
    
    public function addSession($nome, $valor) {
        $valores = $this->selectSession($nome);
        if (!$valores) {
            $valores = array();
        }
        $valores[] = $valor;
        $this->createSession($nome, $valores);
    }
}

