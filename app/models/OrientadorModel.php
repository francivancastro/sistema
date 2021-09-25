<?php

class OrientadorModel extends Model{
    
    public $_tabela = 'orientador';
    public $pkName  = 'id_orientador';
    public $fkName = "id_pessoa";


    public $id_orientador;
    public $id_pessoa;
    public $matricula;
    


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_colaborador = $id;
            $this->id_pessoa = $query[0]['id_pessoa'];
            $this->matricula = $query[0]['matricula'];
        }
    }
    
    public function listarPorPessoa($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_orientador = 0;
        if($_POST['id_orientador']){
            $this->id_orientador = $_POST['id_orientador'];
        }
        $this->id_pessoa = $_POST['id_pessoa'];
        $this->matricula = $_POST['matricula'];
        $msg = array();
        $sql = "SELECT id_orientador FROM orientador
                WHERE (id_orientador <> {$this->id_orientador})
                AND (id_pessoa = '{$this->id_pessoa}')
                AND (matricula = '{$this->matricula}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Orientador Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    public function salvar (){
        $pes = new PessoaModel();
        return $this->inserir(array("id_pessoa" => $pes->pegaUltimoId(),
                                    "matricula" => $this->matricula));
    }
    
    public function alterar (){
        return $this->atualizar(array("id_pessoa" => $this->id_pessoa,
                                    "matricula" => $this->matricula), $this->id_orientador);
    }
}
