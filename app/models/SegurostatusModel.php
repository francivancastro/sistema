<?php

class SegurostatusModel extends Model{
    
    public $_tabela = 'seguro_status';
    public $pkName  = 'id_seguro_status';
    
    public $id_seguro_status;
    public $descricao;
    public $chave;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_seguro_status = $id;
            $this->descricao = $query[0]['descricao'];
            $this->chave = $query[0]['chave'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $id = 0;
        if($_POST['id_seguro_status']){
            $id = $_POST['id_seguro_status'];
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
        $sql = "SELECT id_seguro_status FROM seguro_status
                WHERE (id_seguro_status <> {$id})
                AND (descricao = '{$this->descricao}')
                AND (chave = '{$this->chave}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Status Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
     public function salvar(){
        return $this->inserir(array("descricao" => $this->descricao,
                                      "chave" => $this->chave));
    }
}
