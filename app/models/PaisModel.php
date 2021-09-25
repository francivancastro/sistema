<?php

class PaisModel extends Model{
    
    public $_tabela = 'pais';
    public $pkName  = 'id_pais';
    
    public $id_pais;
    public $nome;
    public $chave;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_pais = $id;
            $this->nome = $query[0]['nome'];
            $this->chave = $query[0]['chave'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_pais = 0;
        if($_POST['id_pais']){
            $this->id_pais = $_POST['id_pais'];
        }
        $this->nome = $_POST['nome'];
        $this->chave = $_POST['chave'];
        $msg = array();
            if (!$this->nome) {
                $msg[] = $msgs->erro("Campo nome Obrigatório!");
            }
            if (!$this->chave) {
                $msg[] = $msgs->erro("Campo Chave Obrigatório!");
            }
        $sql = "SELECT id_pais FROM pais
                WHERE (id_pais <> {$this->id_pais})
                AND (nome = '{$this->nome}')
                AND (chave = '{$this->chave}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("País Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
     public function salvar(){
        return $this->inserir(array(
            "nome" => $this->nome,
            "chave" => $this->chave
        ));
    }
}
