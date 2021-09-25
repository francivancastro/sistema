<?php

class TelefoneModel extends Model {
    
    public $_tabela = 'telefone';
    public $pkName = 'id_telefone';
    
    public $id_telefone;
    public $numero;
    public $descricao;
    public $id_telefone_tipo;
	public $id_pessoa;
    
    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_telefone = $id; 
            $this->numero = $query[0]['numero'];
            $this->descricao = $query[0]['descricao'];
            $this->id_telefone_tipo = $query[0]['id_telefone_tipo'];
			$this->id_pessoa = $query[0]['id_pessoa'];
        }
    }
   public function validar(){
        $msgs = new MsgHelper();
        $id = 0;
        if($_POST['id_telefone']){
            $id = $_POST['id_telefone'];
        }
        $this->numero = $_POST['numero'];
        $this->descricao = $_POST['descricao'];
        $this->id_telefone_tipo = $_POST['id_telefone_tipo'];
        $this->id_pessoa = $_POST['id_pessoa'];
        $msg = array();
            if (!$this->numero) {
                $msg[] = $msgs->erro("Campo Numero de Telefone Obrigatório!");
            } 
            if (!$this->descricao) {
                $msg[] = $msgs->erro("Campo Descrição Obrigatório!");
            }
            if(!$this->id_telefone_tipo){
                $msg[] = $msgs->erro("Campo Tipo de Telefone Obrigatório!");
            }
            if(!$this->id_pessoa){
                $msg[] = $msgs->erro("Campo Pessoa Obrigatório!");
            }
   
        $sql = "SELECT id_telefone FROM telefone
                WHERE (id_telefone <> {$id})
                AND (numero = '{$this->numero}')
                AND (descricao = '{$this->descricao}')
                AND (id_telefone_tipo = '{$this->id_telefone_tipo}')
                AND (id_pessoa = '{$this->id_pessoa}');";
                       
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Telefone já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }

     public function salvar(){
        return $this->inserir(array("numero" => $this->numero,
                                      "descricao" => $this->descricao,
                                      "id_telefone_tipo" => $this->id_telefone_tipo,
                                      "id_pessoa" => $this->id_pessoa,));
    }
 
    
}
