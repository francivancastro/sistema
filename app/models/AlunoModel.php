<?php

class AlunoModel extends Model{
    
    public $_tabela = 'aluno';
    public $pkName  = 'id_aluno';
    public $fkName = "id_responsavel";


    public $id_aluno;
    public $id_pessoa;
    public $id_responsavel;
    

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_aluno = $id;
            $this->id_pessoa = $query[0]['id_pessoa'];
            $this->id_responsavel = $query[0]['id_responsavel'];
        }
    }
    
    public function pegaAlunoPorProtesto($id){
        if($id){
            return $this->banco->ler($this->_tabela, "id_protesto=".$id);
        }
        return false;
        
    }
    
    public function pegaAlunoPorResponsavel($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
        
    }

    public function validar(){
        $msgs = new MsgHelper();
        $this->id_aluno = 0;
        if($_POST['id_aluno']){
            $this->id_aluno = $_POST['id_aluno'];
        }
        $this->id_pessoa = $_POST['id_pessoa'];
        $this->id_responsavel = $_POST['id_responsavel'];
        $msg = array();
            if (!$this->id_responsavel) {
                $msg[] = $msgs->erro("selecione o responsavel");
            }
        
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array("id_pessoa" => $this->id_pessoa,
                                     "id_responsavel" => $this->id_responsavel,
                                     ));
    }
    
    public function salvarNovo(){
        $pes = new PessoaModel();
        return $this->inserir(array("id_pessoa" => $pes->pegaUltimoId(),
                                     "id_responsavel" => $this->id_responsavel,
                                     ));
    }
    
    public function alterar(){
        return $this->atualizar(array("id_pessoa" => $this->id_pessoa,
                                    "id_responsavel" => $this->id_responsavel), $this->id_aluno);
    }
}
