<?php

class MsgHelper {
    
    public $_msgs = array();
    public $_session;


    public function __construct() {
        $this->_session = new SessionHelper();
    }
    
    public function limpar() {
        $this->_session->deletarSession("msg");
    }

    public function flash($mensagem = null){
        if ($mensagem) {
            $this->_session->addSession("msg", $mensagem);
        }
    }
    
    public function mostrarMsg(){
        $msgs = $this->_session->selectSession("msg");
        $this->limpar();
        return $msgs;
    }
    
    public function informacao($mensagem = null) {
        return $this->flash(array('alert-info', $mensagem));
    }
    
    public function sucesso($mensagem = null) {
        return $this->flash(array('alert-success', $mensagem));
    }
    
    public function erro($mensagem = null) {
        return $this->flash(array('alert-danger', $mensagem));
    }
    
    public function alerta($mensagem = null) {
        return $this->flash(array('alert-warning', $mensagem));
    }

    
}