<?php

class EstadoModel extends Model {
    
    public $_tabela = 'estado';
    public $pkName = 'id_estado';
    
    public $id_estado;
    public $nome;
    public $sigla;
    public $id_pais;
    
    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_estado = $id; 
            $this->nome = $query[0]['nome'];
            $this->sigla = $query[0]['sigla'];
            $this->id_pais = $query[0]['id_pais'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $id = 0;
        if($_POST['id_estado']){
            $id = $_POST['id_estado'];
        }
        $this->nome = $_POST['nome'];
        $this->sigla = $_POST['sigla'];
        $this->id_pais = $_POST['id_pais'];
        $msg = array();
            if (!$this->nome) {
                $msg[] = $msgs->erro("Campo Nome Obrigatório!");
            } 
            if (!$this->sigla) {
                $msg[] = $msgs->erro("Campo Sigla Obrigatório!");
            }
            if(!$this->id_pais){
                $msg[] = $msgs->erro("Campo País Obrigatório!");
            }
   
        $sql = "SELECT id_estado FROM estado
                WHERE (id_estado <> {$id})
                AND (nome = '{$this->nome}')
                AND (sigla = '{$this->sigla}')
                AND (id_pais = '{$this->id_pais}');";
                       
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Estado já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }

     public function salvar(){
        return $this->inserir(array("nome" => $this->nome,
                                    "sigla" => $this->sigla,
                                    "id_pais" => $this->id_pais));
    }

}
