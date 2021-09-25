<?php

class ArquivoModel extends Model {

    public $_tabela = 'arquivo';
    public $pkName = 'id_arquivo';
    public $fkName = 'id_protesto';
    public $id_arquivo;
    public $nome;
    public $descricao;
    public $caminho;
    public $conteudo;
    public $tipo;
    public $data_envio;
    public $id_protesto;

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if (isset($id)) {
            $query = $this->banco->ler($this->_tabela, $this->pkName . '=' . $id);
            $this->id_arquivo = $id;
            $this->nome = $query[0]['nome'];
            $this->descricao = $query[0]['descricao'];
            $this->caminho = $query[0]['caminho'];
            $this->conteudo = $query[0]['conteudo'];
            $this->tipo = $query[0]['tipo'];
            $this->data_envio = $query[0]['data_envio'];
            $this->id_protesto = $query[0]['id_protesto'];
        }
    }
    
    public function listarPorProtesto($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }

    public function validar($dados) {
        $msgs = new MsgHelper();
        $id = 0;
        if ($_POST['id_arquivo']) {
            $id = $_POST['id_arquivo'];
        }
        $this->nome = $dados['nome'];
        $this->descricao = $dados['descricao'];
        $this->caminho = $dados['caminho'];
        $this->conteudo = $dados['conteudo'];
        $this->tipo = $dados['tipo'];
        $this->data_envio = date('Y-m-d');
        $this->id_protesto = $dados['id_protesto'];
        $msg = array();
        $sql = "SELECT id_arquivo FROM arquivo
                WHERE (id_arquivo <> {$id})
                AND (nome = '{$this->nome}')
                AND (data_envio = '{$this->data_envio}');";
        $rg = $this->consultar($sql);
        if ($rg) {
            $msg[] = $msgs->erro("Arquivo Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }

        return false;
    }

    public function salvar() {
        return $this->inserir(array(
            "nome" => $this->nome,
            "descricao" => $this->descricao,
            "caminho" => $this->caminho,
            "conteudo" => $this->conteudo,
            "data_envio" => $this->data_envio,
            "tipo" => $this->tipo,
            "id_protesto" => $this->id_protesto));
    }

}
