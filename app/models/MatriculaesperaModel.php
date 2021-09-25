<?php

class MatriculaesperaModel extends Model {

    public $_tabela = 'matricula_espera';
    public $pkName = 'id_matricula_espera';
    public $fkName = 'id_m_aluno';
    public $fkName2 = 'id_turma';
    
    public $id_matricula_espera;
    public $id_m_aluno;
    public $id_turma;
    public $id_visitante;
    public $id_usuario;
    public $data;
    public $status;
    

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if (isset($id)) {
            $query = $this->banco->ler($this->_tabela, $this->pkName . '=' . $id);
            $this->id_matricula_espera = $query[0]['id_matricula_espera'];
            $this->id_m_aluno = $query[0]['id_m_aluno'];
            $this->id_turma = $query[0]['id_turma'];
            $this->id_visitante = $query[0]['id_visitante'];
            $this->id_usuario = $query[0]['id_usuario'];
            $this->data = $query[0]['data'];
            $this->status = $query[0]['status'];
            
        }
    }
    
    public function listarPorAluno($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function listarPorTurma($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id);
        }
        return false;
    }
    
    public function listarTurmaStatus($id, $status){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id . " AND status = '{$status}'");
        }
        return false;
    }
    
    public function validar() {
        $msgs = new MsgHelper();
        $this->id_matricula_espera = 0;
        if ($_POST['id_matricula_espera']) {
            $this->id_matricula_espera = $_POST['id_matricula_espera'];
        }
        $this->id_m_aluno = $_POST['id_m_aluno'];
        $this->id_turma = $_POST['id_turma'];
        $this->id_visitante = $_POST['id_visitante'];
        $this->id_usuario = $_POST['id_usuario'];
        $this->data = $_POST['data'];
        $this->status = $_POST['status'];
        
        $msg = array();
        
        
        $sql = "SELECT id_matricula_espera FROM matricula_espera
                WHERE (id_matricula_espera <> {$this->id_matricula_espera})
                AND (id_m_aluno = '{$this->id_m_aluno}');";
        $rg = $this->consultar($sql);
        if ($rg) {
            $msg[] = $msgs->erro("Aluno já Cadastrado!");
        }
        
        if (count($msg)) {
            return $msg;
        }
        return false;
    }

    public function salvar() {
        return $this->inserir(array(
            "id_m_aluno" => $this->id_m_aluno,
            "id_turma" => $this->id_turma,
            "id_visitante" => $this->id_visitante,
            "id_usuario" => $this->id_usuario,
            "data_cadastro" => $this->data,
            "status" => $this->status,
            ));
    }
    public function alterar() {
        return $this->atualizar(array(
            "id_m_aluno" => $this->id_m_aluno,
            "id_turma" => $this->id_turma,
            "id_visitante" => $this->id_visitante,
            "id_usuario" => $this->id_usuario,
            "data_cadastro" => $this->data,
            "status" => $this->status,
            ), $this->id_matricula_espera);
    }
    
    public function excluirPorAluno($id, $id_aluno){
        if($id){
            return $this->banco->excluir($this->_tabela, $this->pkName."=".$id . " AND id_m_aluno = {$id_aluno}");
        }
        return false;
    }
}
