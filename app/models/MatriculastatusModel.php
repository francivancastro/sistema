<?php

class MatriculastatusModel extends Model{
    
    public $_tabela = 'matricula_status';
    public $pkName  = 'id_matricula_status';
    
    public $id_matricula_status;
    public $descricao;
    public $chave;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_matricula_status = $id;
            $this->descricao = $query[0]['descricao'];
            $this->chave = $query[0]['chave'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_matricula_status = 0;
        if($_POST['id_matricula_status']){
            $this->id_matricula_status = $_POST['id_matricula_status'];
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
        $sql = "SELECT id_matricula_status FROM matricula_status
                WHERE (id_matricula_status <> {$this->id_matricula_status})
                AND (descricao = '{$this->descricao}')
                AND (chave = '{$this->chave}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Status de Matricula Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        return false;
    }
    
    public function salvar (){
        return $this->inserir(array("descricao" => $this->descricao,
                                        "chave" => $this->chave));
    }
    
    public function alterar (){
        return $this->atualizar(array(
                "descricao" => $this->descricao,
                "chave" => $this->chave
        ), $this->id_matricula_status);
    }
}
