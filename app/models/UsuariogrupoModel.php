<?php

class UsuariogrupoModel extends Model{
    
    public $_tabela = "usuario_grupo";
    public $pkName = "id_usuario_grupo";
    public $fkName = "id_grupo";
    public $fkName2 = "id_usuario";
    
    public $id_usuario_grupo;
    public $id_usuario;
    public $id_grupo;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_usuario_grupo = $id;
            $this->id_grupo = $query[0]['id_grupo'];
        }
    }
    
    public function validar($array){
        $msgs = new MsgHelper();
        $this->id_usuario_grupo = 0;
        if($array['id_usuario_grupo']){
            $this->id_usuario_grupo = $array['id_usuario_grupo'];
        }
        
        $this->id_usuario = $array['id_usuario'];
        $this->id_grupo = $array['id_grupo'];
        $msg = array();
            if (!$this->id_usuario) {
                $msg[] = $msgs->erro("Selecione um usuario!");
            }
            if (!$this->id_grupo) {
                $msg[] = $msgs->erro("Selecione um Grupo!");
            }
        $sql = "SELECT id_usuario_grupo FROM usuario_grupo
                WHERE (id_usuario_grupo <> {$this->id_usuario})
                AND (id_usuario = '{$this->id_usuario}')
                AND (id_grupo = '{$this->id_grupo}');";
                       
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Usuário  já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function listarPorUsuario($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id);
        }
        return false;
    }
    
    public function listarPorGrupo($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array(
            "id_usuario" => $this->id_usuario,
            "id_grupo" => $this->id_grupo
        ));
    }
    
    public function excluirPorGrupo($id, $id_usuario){
        if($id){
            return $this->banco->excluir($this->_tabela, $this->fkName."=".$id . " AND id_usuario = {$id_usuario}");
        }
        return false;
    }
    
    public function possuiGrupo($id_usuario, $id_grupo) {
        $pms = $this->banco->ler($this->_tabela, " id_usuario = {$id_usuario} and id_grupo = {$id_grupo} ");
        if ($pms) {
            return true;
        }
        return false;
    }
}
