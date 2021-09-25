<?php

class MalunoModel extends Model{
    
    public $_tabela = 'm_aluno';
    public $pkName  = 'id_m_aluno';
    
    public $id_m_aluno;
    public $nome;
    public $matricula;
    public $data_nascimento;
    
    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_m_aluno = $query[0]['id_m_aluno'];
            $this->nome = $query[0]['nome'];
            $this->matricula = $query[0]['matricula'];
            $this->data_nascimento = $query[0]['data_nascimento'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_m_aluno = 0;
        $caracter = array("'");
        if($_POST['id_m_aluno']){
            $this->id_m_aluno = $_POST['id_m_aluno'];
        }
        $this->nome = str_replace($caracter, "\'", $_POST['nome']);
        $this->matricula = $_POST['matricula'];
        ($_POST['data_nascimento']) ? $this->data_nascimento = UtilHelper::formatUs($_POST["data_nascimento"]) : $this->data_nascimento = null;
        
        $msg = array();
            if (!$this->nome) {
                $msg[] = $msgs->erro("Nome do aluno Obrigatório!");
            }
        $sql = "SELECT id_m_aluno FROM m_aluno
                WHERE (id_m_aluno <> {$this->id_m_aluno})
                AND (nome = '{$this->nome}')
                AND (matricula = '{$this->matricula}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Aluno já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    public function salvar(){
        return $this->inserir(array(
            "nome" => $this->nome,
            "matricula" => $this->matricula,
            "data_nascimento" => $this->data_nascimento,
        ));
    }
    
    public function alterar(){
        return $this->atualizar(array(
            "nome" => $this->nome,
            "matricula" => $this->matricula,
            "data_nascimento" => $this->data_nascimento,
        ), $this->id_m_aluno);
    }
}

