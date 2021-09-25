<?php

class TesteModel extends Model{
    
    public $_tabela = "dados_bancario";
    public $pkName = "id_dados_bancario";
    
    public $id_dados_bancario;
    public $banco;
    public $agencia;
    public $conta;
    public $id_pessoa;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_dados_bancario = $id;
            $this->banco = $query[0]['banco'];
            $this->agencia = $query[0]['agencia'];
            $this->conta = $query[0]['conta'];
            $this->id_pessoa = $query[0]['id_pessoa'];
        }
    }
    
    public function validar(){
        $this->id_pessoa = $_POST['id_pessoa'];
        $this->banco = $_POST['banco'];
        $this->agencia = $_POST['agencia'];
        $this->conta = $_POST['conta'];
        
    }
    
    public function salvar(){
        $pes = new PessoaModel();
        return $this->inserir(array("id_pessoa" => $pes->pegaUltimoId(),
                                        "banco" => $this->banco,
                                      "agencia" => $this->agencia,
                                        "conta" => $this->conta));
    }
}

