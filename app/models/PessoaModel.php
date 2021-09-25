<?php

class PessoaModel extends Model {

    public $_tabela = 'pessoa';
    public $pkName = 'id_pessoa';
    public $id_pessoa;
    public $id_municipio;
    public $id_estado;
    public $nome;
    public $cpf;
    public $rg;
    public $email;
    public $sexo;
    public $data_nascimento;
    public $foto;
    public $nacionalidade;
    public $naturalidade;
    public $pai;
    public $mae;
    public $expedidor;
    public $profissao;
    public $data_expedicao;
    public $pis;
    public $estado_civil;

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if (isset($id)) {
            $query = $this->banco->ler($this->_tabela, $this->pkName . '=' . $id);
            $this->id_pessoa = $id;
            $this->nome = $query[0]['nome'];
            $this->cpf = $query[0]['cpf'];
            $this->rg = $query[0]['rg'];
            $this->email = $query[0]['email'];
            $this->sexo = $query[0]['sexo'];
            $this->data_nascimento = $query[0]['data_nascimento'];
            $this->foto = $query[0]["foto"];
            $this->naturalidade = $query[0]["naturalidade"];
            $this->nacionalidade = $query[0]["nacionalidade"];
            $this->pai = $query[0]["pai"];
            $this->mae = $query[0]["mae"];
            $this->expedidor = $query[0]["expedidor"];
            $this->profissao = $query[0]["profissao"];
            $this->data_expedicao = $query[0]["data_expedicao"];
            $this->pis = $query[0]["pis"];
            $this->estado_civil = $query[0]["estado_civil"];
        }
    }

    public function validar($foto = NULL) {
        
        $msgs = new MsgHelper();
        $caracter = array(".", "/", "-");
        $this->id_pessoa = 0;
        if ($_POST['id_pessoa']) {
            $this->id_pessoa = $_POST['id_pessoa'];
        }
        ($_POST['nome'] ? $this->nome = $_POST['nome'] : $this->nome = NULL);
        ($_POST['cpf'] ?  $this->cpf = str_replace($caracter, "", $_POST['cpf']) : $this->cpf = NULL);
        ($_POST['rg'] ?  $this->rg = $_POST['rg'] : $this->rg = NULL);
        ($_POST['email'] ?  $this->email = $_POST['email'] : $this->email = NULL);
        ($_POST['sexo'] ?  $this->sexo = $_POST['sexo'] : $this->sexo = NULL);
        ($_POST['data_nascimento'] ? $this->data_nascimento = UtilHelper::formatUs($_POST["data_nascimento"]) : $this->data_nascimento = null);
        ($_POST['naturalidade'] ?  $this->naturalidade = $_POST['naturalidade'] : $this->naturalidade = NULL);
        ($_POST['nacionalidade'] ?  $this->nacionalidade = $_POST['nacionalidade'] : $this->nacionalidade = NULL);
        ($_POST['pai'] ?  $this->pai = $_POST['pai'] : $this->pai = NULL);
        ($_POST['mae'] ?  $this->mae = $_POST['mae'] : $this->mae = NULL);
        ($_POST['expedidor'] ?  $this->expedidor = $_POST['expedidor'] : $this->expedidor = NULL);
        ($_POST['profissao'] ?  $this->profissao = $_POST['profissao'] : $this->profissao = NULL);
        ($_POST['data_expedicao'] ?  $this->data_expedicao = UtilHelper::formatUs($_POST["data_expedicao"]) : $this->data_expedicao = null);
        ($_POST['pis'] ?  $this->pis = $_POST["pis"] : $this->pis = null);
        ($_POST['estado_civil'] ?  $this->estado_civil = $_POST["estado_civil"] : $this->estado_civil = NULL);
        ($foto ?  $this->foto = $foto : $this->foto = NULL);
        $msg = array();
        
        if (!$this->nome) {
            $msg[] = $msgs->erro("Campo Nome Completo Obrigatório!");
        }
        /*
        if (!$this->id_estado) {
            $msg[] = $msgs->erro("Campo Estado Obrigatório!");
        }
        if (!$this->id_municipio) {
            $msg[] = $msgs->erro("Campo Municipio Obrigatório!");
        }
        
        if (!$this->rg) {
            $msg[] = $msgs->erro("Campo RG Obrigatório!");
        }
        if (!$this->email) {
            $msg[] = $msgs->erro("Campo Email Obrigatório!");
        }
        if (!$this->sexo) {
            $msg[] = $msgs->erro("Campo Sexo Obrigatório!");
        }
        if (!$this->data_nascimento) {
            $msg[] = $msgs->erro("Campo Data de Nascimento Obrigatório!");
        }
        if (!$this->rg) {
            $msg[] = $msgs->erro("Campo RG Obrigatório!");
        }
        
         */
        $sql = "SELECT id_pessoa FROM pessoa
                WHERE (id_pessoa <> {$this->id_pessoa})
                AND (nome = '{$this->nome}')
                AND (cpf = '{$this->cpf}');";
        $rg = $this->consultar($sql);
        if ($rg) {
            $msg[] = $msgs->erro("Pessoa Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }

        return false;
    }

    public function salvar() {
        return $this->inserir(array(
                    "nome" => $this->nome,
                    "cpf" => $this->cpf,
                    "rg" => $this->rg,
                    "email" => $this->email,
                    "sexo" => $this->sexo,
                    "data_nascimento" => $this->data_nascimento,
                    "foto" => $this->foto,
                    "naturalidade" => $this->naturalidade,
                    "nacionalidade" => $this->nacionalidade,
                    "pai" => $this->pai,
                    "mae" => $this->mae,
                    "expedidor" => $this->expedidor,
                    "profissao" => $this->profissao,
                    "data_expedicao" => $this->data_expedicao,
                    "pis" => $this->pis,
                    "estado_civil" => $this->estado_civil,
                    "foto" => $this->foto,
        ));
    }

    public function alterar() {
        return $this->atualizar(array(
                    "nome" => $this->nome,
                    "cpf" => $this->cpf,
                    "rg" => $this->rg,
                    "email" => $this->email,
                    "sexo" => $this->sexo,
                    "data_nascimento" => $this->data_nascimento,
                    "foto" => $this->foto,
                    "naturalidade" => $this->naturalidade,
                    "nacionalidade" => $this->nacionalidade,
                    "pai" => $this->pai,
                    "mae" => $this->mae,
                    "expedidor" => $this->expedidor,
                    "profissao" => $this->profissao,
                    "data_expedicao" => $this->data_expedicao,
                    "pis" => $this->pis,
                    "estado_civil" => $this->estado_civil,
                        ), $this->id_pessoa);
    }
}
