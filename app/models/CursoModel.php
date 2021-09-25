<?php

class CursoModel extends Model {

    public $_tabela = 'curso';
    public $pkName = 'id_curso';
    public $fkName = 'id_instituicao';


    public $id_curso;
    public $descricao;
    public $id_instituicao;
    public $id_curso_tipo;
    public $id_turno;

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if ($id) {
            $query = $this->banco->ler($this->_tabela, $this->pkName . '=' . $id);
            $this->id_curso = $id;
            $this->descricao = $query[0]['descricao'];
            $this->id_instituicao = $query[0]['id_instituicao'];
            $this->id_curso_tipo = $query[0]['id_curso_tipo'];
            $this->id_turno = $query[0]["id_turno"];
        }
    }
    
    public function listaPorInstituicao($id){
        if($id){
            return $this->banco->ler($this->_tabela, "id_instituicao = ". $id);
        }
        return false;
    }

    public function validar() {
        $msgs = new MsgHelper();
        $this->id_curso = 0;
        if ($_POST['id_curso']) {
            $this->id_curso = $_POST['id_curso'];
        }
        $this->descricao = $_POST['descricao'];
        $this->id_instituicao = $_POST['id_instituicao'];
        $this->id_curso_tipo = $_POST['id_curso_tipo'];
        $this->id_turno = $_POST["id_turno"];
        $msg = array();
        if (!$this->descricao) {
            $msg[] = $msgs->erro("Campo Nome do Curso Obrigatório!");
        }
        if (!$this->id_curso_tipo) {
            $msg[] = $msgs->erro("Campo Tipo do Curso Obrigatório!");
        }
        if (!$this->id_turno) {
            $msg[] = $msgs->erro("Campo Turno Obrigatório!");
        }

        $sql = "SELECT id_curso FROM curso
                WHERE (id_curso <> {$this->id_curso})
                AND (descricao = '{$this->descricao}')
                AND (id_instituicao = '{$this->id_instituicao}')
                AND (id_curso_tipo = '{$this->id_curso_tipo}')
                AND (id_turno = '{$this->id_turno}');";

        $rg = $this->consultar($sql);
        if ($rg) {
            $msg[] = $msgs->erro("Curso já Cadastrado para Esta Instituição!");
        }
        if (count($msg)) {
            return $msg;
        }

        return false;
    }

    public function salvar() {
        return $this->inserir(array("descricao" => $this->descricao,
                               "id_instituicao" => $this->id_instituicao,
                                "id_curso_tipo" => $this->id_curso_tipo,
                                     "id_turno" => $this->id_turno));
    }
    
    public function alterar() {
        return $this->atualizar(array("descricao" => $this->descricao,
                               "id_instituicao" => $this->id_instituicao,
                                "id_curso_tipo" => $this->id_curso_tipo,
                                     "id_turno" => $this->id_turno), $this->id_curso);
    }

}
