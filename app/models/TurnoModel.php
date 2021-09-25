<?php

class TurnoModel extends Model{
    
    public $_tabela = 'turno';
    public $pkName  = 'id_turno';
    
    public $id_turno;
    public $descricao;
    public $chave;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_turno = $id;
            $this->descricao = $query[0]['descricao'];
            $this->chave = $query[0]['chave'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_turno = 0;
        if($_POST['id_turno']){
            $this->id_turno = $_POST['id_turno'];
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
        $sql = "SELECT id_turno FROM turno
                WHERE (id_turno <> {$this->id_turno})
                AND (descricao = '{$this->descricao}')
                AND (chave = '{$this->chave}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Turno Já Cadastrado!");
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
    
    public function alterar(){
        return $this->atualizar(array("descricao" => $this->descricao,
                                        "chave" => $this->chave), $this->id_turno);
    }
}
