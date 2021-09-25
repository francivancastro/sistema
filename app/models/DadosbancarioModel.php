<?php

class DadosbancarioModel extends Model{
    
    public $_tabela = "dados_bancario";
    public $pkName = "id_dados_bancario";
    public $fkName = "id_pessoa";


    public $id_dados_bancario;
    public $_banco;
    public $agencia;
    public $conta;
    public $id_pessoa;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_dados_bancario = $id;
            $this->_banco = $query[0]['banco'];
            $this->agencia = $query[0]['agencia'];
            $this->conta = $query[0]['conta'];
            $this->id_pessoa = $query[0]['id_pessoa'];
        }
    }
    
    public function pegaDadosPorPessoa($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }

    public function validar(){
        $msgs = new MsgHelper();
        $this->id_dados_bancario = 0;
        if($_POST['id_dados_bancario']){
            $this->id_dados_bancario = $_POST['id_dados_bancario'];
        }
        $this->id_pessoa = $_POST['id_pessoa'];
        $this->_banco = $_POST['banco'];
        $this->agencia = $_POST['agencia'];
        $this->conta = $_POST['conta'];
        
        $msg = array();
            if (!$this->_banco) {
                $msg[] = $msgs->erro("Campo Banco Obrigatório!");
            }
            if (!$this->agencia) {
                $msg[] = $msgs->erro("Campo Agencia Obrigatório!");
            }
            if (!$this->conta) {
                $msg[] = $msgs->erro("Campo Conta Obrigatório!");
            }
        $sql = "SELECT id_dados_bancario FROM dados_bancario
                WHERE (id_dados_bancario <> {$this->id_dados_bancario})
                AND (id_pessoa = '{$this->id_pessoa}')
                AND (banco = '{$this->_banco}')
                AND (agencia = '{$this->agencia}')
                AND (conta = '{$this->conta}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Dados Bancario já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        return false;
    }
    
    public function salvar(){
        $pes = new PessoaModel();
        return $this->inserir(array("id_pessoa" => $pes->pegaUltimoId(),
                                        "banco" => $this->_banco,
                                      "agencia" => $this->agencia,
                                        "conta" => $this->conta));
    }
    
    public function alterar(){
        return $this->atualizar(array("id_pessoa" => $this->id_pessoa,
                                           "banco" => $this->_banco,
                                         "agencia" => $this->agencia,
                                           "conta" => $this->conta), $this->id_dados_bancario);
    }
}