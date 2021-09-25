<?php

class AreaatuacaoModel extends Model{
    
    public $_tabela = 'area_atuacao';
    public $pkName  = 'id_area_atuacao';
    
    public $id_area_atuacao;
    public $descricao;
    public $chave;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_area_atuacao = $id;
            $this->descricao = $query[0]['descricao'];
            $this->chave = $query[0]['chave'];
        }
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_area_atuacao = 0;
        if($_POST['id_area_atuacao']){
            $this->id_area_atuacao = $_POST['id_area_atuacao'];
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
        $sql = "SELECT id_area_atuacao FROM area_atuacao
                WHERE (id_area_atuacao <> {$this->id_area_atuacao})
                AND (descricao = '{$this->descricao}')
                AND (chave = '{$this->chave}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Área de Atuação Já Cadastrado!");
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
    public function alterar(){
        return $this->atualizar(array("descricao" => $this->descricao,
                                        "chave" => $this->chave), $this->id_area_atuacao);
    }
}
