<?php

class MatriculaModel extends Model {

    public $_tabela = 'matricula';
    public $pkName = 'id_matricula';
    public $fkName = 'id_m_aluno';
    public $fkName2 = 'id_turma';
    
    public $id_matricula;
    public $id_m_aluno;
    public $id_turma;
    public $id_matricula_status;
    public $situacao;
    public $id_usuario;
    public $data;
    

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if (isset($id)) {
            $query = $this->banco->ler($this->_tabela, $this->pkName . '=' . $id);
            $this->id_matricula = $query[0]['id_matricula'];
            $this->id_m_aluno = $query[0]['id_m_aluno'];
            $this->id_turma = $query[0]['id_turma'];
            $this->id_matricula_status = $query[0]['id_matricula_status'];
            $this->situacao = $query[0]['situacao'];
            $this->id_usuario = $query[0]['id_usuario'];
            $this->data = $query[0]['data'];
            
        }
    }
    
    public function listarPorAno($ano){
        
        if($ano){
            $sql = "SELECT f.id_matricula, f.id_m_aluno, f.id_turma, f.id_matricula_status, f.id_usuario, f.data_cadastro from ano a, segmento b, ano_letivo c, serie d, turma e, matricula f WHERE (a.id_ano = c.id_ano) 
                AND (c.id_segmento = b.id_segmento)
                AND (c.id_ano_letivo = d.id_ano_letivo)
                AND (d.id_serie = e.id_serie)
                AND (e.id_turma = f.id_turma)
                AND (a.descricao = $ano);";
            $q = $this->pesquisar($sql);
            return $q;
        } 
        return false;
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
    
    public function listarPorTurmaAtivos($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id, null, null, null, "situacao='A'");
        }
        return false;
    }
    public function listarPorTurmaDesativados($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id, null, null, null, "situacao='D'");
        }
        return false;
    }
    
    public function listarPorTurmaMatricula($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id, null, null, null, "id_matricula_status=1 AND situacao='A'" );
        }
        return false;
    }
    public function listarPorTurmaRematricula($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id, null, null, null, "id_matricula_status=2 AND situacao='A'" );
        }
        return false;
    }
    public function listarPorTurmaReserva($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id, null, null, null, "id_matricula_status=3 AND situacao='A'" );
        }
        return false;
    }
    public function listarPrimeiro(){
        return $this->banco->ler($this->_tabela, null, null, null, "data_cadastro DESC", null, "data_cadastro");
    }

    public function validar() {
        $msgs = new MsgHelper();
        $this->id_matricula = 0;
        if ($_POST['id_matricula']) {
            $this->id_matricula = $_POST['id_matricula'];
        }
        $this->id_m_aluno = $_POST['id_m_aluno'];
        $this->id_turma = $_POST['id_turma'];
        $this->id_matricula_status = $_POST['id_matricula_status'];
        $this->id_usuario = $_POST['id_usuario'];
        $this->situacao = $_POST['situacao'];
        $this->data = $_POST['data'];
        
        $msg = array();
        
        
        $sql = "SELECT id_matricula FROM matricula
                WHERE (id_matricula <> {$this->id_matricula})
                AND (id_turma = '{$this->id_turma}')
                AND (id_matricula_status = '{$this->id_matricula_status}')
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
            "id_matricula_status" => $this->id_matricula_status,
            "id_usuario" => $this->id_usuario,
            "situacao" => $this->situacao,
            "data_cadastro" => $this->data,
        ));
    }
    public function alterar() {
        return $this->atualizar(array(
            "id_m_aluno" => $this->id_m_aluno,
            "id_turma" => $this->id_turma,
            "id_matricula_status" => $this->id_matricula_status,
            "id_usuario" => $this->id_usuario,
            "situacao" => $this->situacao,
            "data_cadastro" => $this->data,
        ), $this->id_matricula);
    }
    
    public function excluirPorAluno($id, $id_aluno){
        if($id){
            return $this->banco->excluir($this->_tabela, $this->fkName."=".$id . " AND id_aluno = {$id_aluno}");
        }
        return false;
    }
}
