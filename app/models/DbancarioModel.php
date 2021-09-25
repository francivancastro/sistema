<?php

class DbancarioModel extends Model{
    
    public $_tabela = 'aluno';
    public $pkName  = 'id_aluno';
    
    public $id_aluno;
    public $id_pessoa;
    public $id_curso;
    public $semestre;
    public $data_inicio;
    public $data_fim;
    

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_aluno = $id;
            $this->id_pessoa = $query[0]['id_pessoa'];
            $this->id_curso = $query[0]['id_curso'];
            $this->semestre = $query[0]['semestre'];
            $this->data_fim = $query[0]['data_fim'];
            $this->data_inicio = $query[0]['data_inicio'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_aluno = 0;
        if($_POST['id_aluno']){
            $this->id_aluno = $_POST['id_aluno'];
        }
        $this->id_pessoa = $_POST['id_pessoa'];
        $this->id_curso = $_POST['id_curso'];
        $this->semestre = $_POST['semestre'];
        $this->data_fim = $_POST['data_fim'];
        $this->data_inicio = $_POST['data_inicio'];
        $msg = array();
            if (!$this->semestre) {
                $msg[] = $msgs->erro("Campo semestre Obrigatório");
            }
            if (!$this->data_fim) {
                $msg[] = $msgs->erro("Selecione Uma data Final do Curso!");
            }
            if (!$this->data_inicio) {
                $msg[] = $msgs->erro("Selecione Uma data Inicial do Curso!!");
            }
        $sql = "SELECT id_aluno FROM aluno
                WHERE (id_aluno <> {$this->id_aluno})
                AND (id_curso = '{$this->id_curso}')
                AND (semestre = '{$this->semestre}')
                AND (data_fim = '{$this->data_fim}')
                AND (data_inicio = '{$this->data_inicio}')
                AND (id_pessoa = '{$this->id_pessoa}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Aluno Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
     public function salvar(){
        $pes = new PessoaModel();
        return $this->inserir(array("id_pessoa" => $pes->pegaUltimoId(),
                                     "id_curso" => $this->id_curso,
                                     "semestre" => $this->semestre,
                                     "data_fim" => $this->data_fim,
                                  "data_inicio" => $this->data_inicio));
    }
}
