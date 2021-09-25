<?php

class FuncaoModel extends Model {
    
    public $_tabela = 'funcao';
    public $pkName = 'id_funcao';
    
    public $id_funcao;
    public $descricao;
    public $salario;
    public $carga_horaria;
    
    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_funcao = $id; 
            $this->carga_horaria = $query[0]['carga_horaria'];
            $this->descricao = $query[0]['descricao'];
            $this->salario = $query[0]['salario'];
        }
    }
    
   public function validar(){
        $msgs = new MsgHelper();
        $id = 0;
        if($_POST['id_funcao']){
            $id = $_POST['id_funcao'];
        }
        $this->carga_horaria = $_POST['carga_horaria'];
        $this->descricao = $_POST['descricao'];
        $this->salario = $_POST['salario'];
        $msg = array();
            if (!$this->carga_horaria) {
                $msg[] = $msgs->erro("Campo Carga Horária Obrigatório!");
            } 
            if (!$this->descricao) {
                $msg[] = $msgs->erro("Campo Descrição Obrigatório!");
            }
            if(!$this->salario){
                $msg[] = $msgs->erro("Campo Salário Obrigatório!");
   
        $sql = "SELECT id_funcao FROM funcao
                WHERE (id_funcao <> {$id})
                AND (carga_horaria = '{$this->carga_horaria}')
                AND (descricao = '{$this->descricao}')
                AND (salario = '{$this->salario}');";
                       
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Função já Cadastrada!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
   }
     public function salvar(){
        return $this->inserir(array("carga_horaria" => $this->carga_horaria,
                                      "descricao" => $this->descricao,
                                      "salario" => $this->salario));
    }
}
