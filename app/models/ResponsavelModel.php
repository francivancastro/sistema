<?php

class ResponsavelModel extends Model {
    
    public $_tabela = 'responsavel';
    public $pkName = 'id_responsavel';
    
    public $id_responsavel;
    public $id_pessoa;
    
    
    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_responsavel = $id; 
            $this->id_pessoa = $query[0]['id_pessoa'];
            }
    }
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_responsavel = 0;
        if($_POST['id_responsavel']){
            $this->id_responsavel = $_POST['id_responsavel'];
        }
        $this->id_pessoa = $_POST['id_pessoa'];
        $msg = array();
            
            if(!$this->id_pessoa){
                $msg[] = $msgs->erro("Campo Pessoa Obrigatório!");
            }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }

    public function salvar(){
        return $this->inserir(array("id_pessoa" => $this->id_pessoa));
    }
    
    public function salvarNovo(){
        $pes = new PessoaModel();
        return $this->inserir(array("id_pessoa" => $pes->pegaUltimoId()));
    }
    
    public function alterar(){
        return $this->atualizar(array("id_pessoa" => $this->id_pessoa), $this->id_responsavel);
    }

    
}
