<?php

class ChatModel extends Model {
    
    public $_tabela = 'chat';
    public $pkName  = 'id';
    public $toCol  = 'chat.to';


    public $id;
    public $from;
    public $to;
    public $message; 
    public $sent; 
    public $recd; 

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id = $id;
            $this->from = $query[0]["from"];
            $this->to = $query[0]['to'];
            $this->message = $query[0]['message'];
            $this->sent = $query[0]['sent'];
            $this->recd = $query[0]['recd'];
        }
    }
    
    public function listarChat($id, $and, $order){
        
        return $this->banco->ler($this->_tabela, $this->toCol."='".$id."' ",null ,null ,$order, $and);
    }
    
    public function setDados(Array $array = array()){
        $this->from = $array['from'];
        $this->to = $array['to'];
        $this->message = $array['message'];
        $this->sent = $array['sent'];
    }

    public function salvar(){
        return $this->inserir(array("chat.from" => $this->from,
                                       "chat.to" => $this->to,
                                      "message" => $this->message,
                                      "sent" => $this->sent));
    }
    
    public function alterar($id, $and = null){
        return $this->atualizaUnico(array("recd" => 1), $this->toCol."='".$id."' " , $and);
    }
}
