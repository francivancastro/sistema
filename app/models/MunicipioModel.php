<?php

class MunicipioModel extends Model {
    
    public $_tabela = 'municipio';
    public $pkName = 'id_municipio';
    
    public $id_municipio;
    public $nome;
    public $id_estado;
    
    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_municipio = $id; 
            $this->nome = $query[0]['nome'];
            $this->id_estado = $query[0]['id_estado'];
        }
    }
    
     public function validar(){
        $msgs = new MsgHelper();
        $this->id_municipio = 0;
        if($_POST['id_municipio']){
            $this->id_municipio = $_POST['id_municipio'];
        }
        $this->nome = $_POST['nome'];
        $this->id_estado = $_POST['id_estado'];
        $msg = array();
            if (!$this->nome) {
                $msg[] = $msgs->erro("Campo Nome Obrigatório!");
            }
            if (!$this->id_estado) {
                $msg[] = $msgs->erro("Campo Estado Obrigatório!");
            }
        $sql = "SELECT id_municipio FROM municipio
                WHERE (id_municipio <> {$this->id_municipio})
                AND (nome = '{$this->nome}')
                AND (id_estado = '{$this->id_estado}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Municipio Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array("nome" => $this->nome,
                               "id_estado" => $this->id_estado));
    }

}
