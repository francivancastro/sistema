<?php

class GrupoModel extends Model {
    
    public $_tabela = 'grupo';
    public $pkName = 'id_grupo';
    
    public $id_grupo;
    public $padrao;
    public $nome;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_grupo = $id;
            $this->padrao = $query[0]['padrao'];
            $this->nome = $query[0]['nome'];
        }
    }
    
    public function validar() {
        
        $msgs = new MsgHelper();
        $this->id_grupo = 0;
        if ($_POST['id_grupo']) {
            $this->id_grupo = $_POST['id_grupo'];
        }
        ($_POST['nome'] ? $this->nome = $_POST['nome'] : $this->nome = NULL);
        ($_POST['padrao'] ? $this->padrao = $_POST['padrao'] : $this->padrao = NULL);
        
        $msg = array();
        
        if (!$this->nome) {
            $msg[] = $msgs->erro("Digite o nome do grupo!");
        }
        
        $sql = "SELECT id_grupo FROM grupo
                WHERE (id_grupo <> {$this->id_grupo})
                AND (nome = '{$this->nome}')
                AND (padrao = '{$this->padrao}');";
        $rg = $this->consultar($sql);
        if ($rg) {
            $msg[] = $msgs->erro("Grupo Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }

        return false;
    }

    public function salvar() {
        return $this->inserir(array(
                    "nome" => $this->nome,
                    "padrao" => $this->padrao,
        ));
    }

    public function alterar() {
        return $this->atualizar(array(
                    "nome" => $this->nome,
                    "padrao" => $this->padrao,
                    ), $this->id_grupo);
    }
    
}
