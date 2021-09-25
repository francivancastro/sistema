<?php

class RelatorioModel extends Model{
    
   
    public $id_protesto_tipo;
    public $descricao;
    public $chave;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_protesto_tipo = $id;
            $this->descricao = $query[0]['descricao'];
            $this->chave = $query[0]['chave'];
        }
    }
    
    public function validar(){
        
        $msgs = new MsgHelper();
        $this->id_protesto_tipo = 0;
        if($_POST['id_protesto_tipo']){
            $this->id_protesto_tipo = $_POST['id_protesto_tipo'];
        }
        $this->descricao = $_POST['descricao'];
        $this->chave = $_POST['chave'];
        $msg = array();
        
        if (!$this->descricao) {
            $msg[] = $msgs->erro("Campo Descrição Obrigatório!");
        }
        if (!$this->chave) {
            $msg[] = $msgs->erro("Campo Chave Obrigatório!");
        }
        
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
}
