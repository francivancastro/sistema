<?php

class TurmaModel extends Model{
    
    public $_tabela = 'turma';
    public $pkName  = 'id_turma';
    public $fkName  = 'id_serie';
    
    public $id_turma;
    public $id_serie;
    public $descricao;
    public $vagas;
    public $turno;
    public $situacao;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_turma = $query[0]['id_turma'];
            $this->id_serie = $query[0]['id_serie'];
            $this->descricao = $query[0]['descricao'];
            $this->turno = $query[0]['turno'];
            $this->vagas = $query[0]['vagas'];
            $this->situacao = $query[0]['situacao'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_turma = 0;
        if($_POST['id_turma']){
            $this->id_turma = $_POST['id_turma'];
        }
        $this->id_serie = $_POST['id_serie'];
        $this->descricao = $_POST['descricao'];
        $this->turno = $_POST['turno'];
        $this->vagas = $_POST['vagas'];
        $this->situacao = $_POST['situacao'];
        $msg = array();
            if (!$this->descricao) {
                $msg[] = $msgs->erro("Campo Descrição Obrigatório!");
            }
            if (!$this->vagas) {
                $msg[] = $msgs->erro("Campo vagas Obrigatório!");
            }
            if (!$this->turno) {
                $msg[] = $msgs->erro("Campo turno Obrigatório!");
            }
        $sql = "SELECT id_turma FROM turma
                WHERE (id_turma <> {$this->id_turma})
                AND (id_serie = '{$this->id_serie}')
                AND (descricao = '{$this->descricao}')
                AND (turno = '{$this->turno}')
                AND (vagas = '{$this->vagas}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Turma já Cadastrada!");
        }
        if (count($msg)) {
            return $msg;
        }
        return false;
    }
    
    public function listarPorSerie($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function listarPorSerieAtivos($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id, null, null, null, "situacao='A'");
        }
        return false;
    }
    
    public function listarPorSerieDesabilitadas($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id, null, null, null, "situacao='D'");
        }
        return false;
    }
        
    public function salvar (){
        return $this->inserir(array(
            "id_serie" => $this->id_serie,
            "descricao" => $this->descricao,
            "turno" => $this->turno,
            "vagas" => $this->vagas,
            "situacao" => $this->situacao
        ));
    }
    public function alterar (){
        return $this->atualizar(array(
            "id_serie" => $this->id_serie,
            "descricao" => $this->descricao,
            "turno" => $this->turno,
            "vagas" => $this->vagas,
            "situacao" => $this->situacao
        ), $this->id_turma);
    }
}
