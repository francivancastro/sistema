<?php

class CursotipoModel extends Model{
    
    public $_tabela = 'curso_tipo';
    public $pkName  = 'id_curso_tipo';
    
    public $id_curso_tipo;
    public $descricao;
    public $chave;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_curso_tipo = $id;
            $this->descricao = $query[0]['descricao'];
            $this->chave = $query[0]['chave'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $id = 0;
        if($_POST['id_curso_tipo']){
            $id = $_POST['id_curso_tipo'];
        }
        $this->descricao = $_POST['descricao'];
        $this->chave = $_POST['chave'];
        $msg = array();
            if (!$this->descricao) {
                $msg[] = $msgs->erro("Campo Descrição Obrigatório!");
            }
            if (!$this->chave) {
                $msg[] = $msgs->erro("Campo Chave Obrigatório!");
            }
        $sql = "SELECT id_curso_tipo FROM curso_tipo
                WHERE (id_curso_tipo <> {$id})
                AND (descricao = '{$this->descricao}')
                AND (chave = '{$this->chave}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Tipo de Curso Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    public function salvar (){
        return $this->inserir(array("descricao" => $this->descricao,
                                        "chave" => $this->chave));
    }
}
