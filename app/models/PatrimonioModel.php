<?php

class PatrimonioModel extends Model {
    
    public $_tabela = 'bem_patrimonial';
    public $pkName  = 'id_bem_patrimonial';
    private $fkName = "id_colaborador";


    public $id_bem_patrimonial;
    public $nome;
    public $codigo;
    public $descricao;
    public $id_colaborador;
    

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_fbem_patrimonial = $id;
            $this->id_colaborador = $query[0]["id_colaborador"];
            $this->nome = $query[0]['nome'];
            $this->codigo = $query[0]['codigo'];
            $this->descricao = $query[0]['descricao'];
        }
    }
    
    public function listarPorColaborador($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }

    public function validar(){
        
        $msgs = new MsgHelper();
        $this->id_bem_patrimonial = 0;
        if($_POST['id_bem_patrimonial']){
            $this->id_bem_patrimonial = $_POST['id_bem_patrimonial'];
        }
        $this->id_colaborador = $_POST["id_colaborador"];
        $this->nome = $_POST['nome'];
        $this->codigo = $_POST['codigo'];
        $this->descricao = $_POST['descricao'];
        $msg = array();
            if (!$this->nome) {
                $msg[] = $msgs->erro("Campo Nome Obrigatório!");
            }
            if (!$this->codigo) {
                $msg[] = $msgs->erro("Campo Codigo Obrigatório!");
            }
        $sql = "SELECT id_bem_patrimonial FROM bem_patrimonial
                WHERE (id_bem_patrimonial <> {$this->id_bem_patrimonial})
                AND (codigo = '{$this->codigo}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Patrimonios Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array("nome" => $this->nome,
                                       "codigo" => $this->codigo,
                                      "descricao" => $this->descricao,
                                    "id_colaborador" => $this->id_colaborador));
    }
    
    public function alterar(){
        return $this->atualizar(array("nome" => $this->nome,
                                         "codigo" => $this->codigo,
                                        "descricao" => $this->descricao,
                                   "id_colaborador" => $this->id_colaborador), $this->id_bem_patrimonial);
    }
}
