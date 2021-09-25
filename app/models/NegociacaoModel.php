<?php

class NegociacaoModel extends Model {

    public $_tabela = 'negociacao';
    public $pkName = 'id_negociacao';
    public $fkName = 'id_protesto';
    public $id_negociacao;
    public $ano_base;
    public $parcela;
    public $valor_parcela;
    public $multa;
    public $juros;
    public $desconto;
    public $id_protesto;

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if (isset($id)) {
            $query = $this->banco->ler($this->_tabela, $this->pkName . '=' . $id);
            $this->id_arquivo = $id;
            $this->ano_base = $query[0]['ano_base'];
            $this->parcela = $query[0]['parcela'];
            $this->valor_parcela = $query[0]['valor_parcela'];
            $this->multa = $query[0]['multa'];
            $this->juros = $query[0]['juros'];
            $this->desconto = $query[0]['desconto'];
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
        $caracter = array('.');
        $msgs = new MsgHelper();
        $id = 0;
        if ($_POST['id_arquivo']) {
            $id = $_POST['id_arquivo'];
        }
        $this->ano_base = $dados['ano_base'];
        $this->parcela = $dados['parcela'];
        $this->valor_parcela = str_replace(',', '.', str_replace($caracter,"",  $dados['valor_parcela']));
        ($dados['multa'] ? $this->multa = str_replace(',', '.', str_replace($caracter,"",  $dados['multa'])) : $this->multa = 0);
        ($dados['juros'] ? $this->juros = str_replace(',', '.', str_replace($caracter,"",  $dados['juros'])) : $this->juros = 0);
        ($dados['desconto'] ? $this->desconto = str_replace(',', '.', str_replace($caracter,"",  $dados['desconto'])): $this->desconto = 0);
        $this->id_protesto = $dados['id_protesto'];
        $msg = array();
        /*
        $sql = "SELECT id_negociacao FROM negociacao
                WHERE (id_negociacao <> {$id})
                AND (ano_base = '{$this->ano_base}')
                AND (valor_parcela = '{$this->valor_parcela}');";
        $rg = $this->consultar($sql);
        if ($rg) {
            $msg[] = $msgs->erro("Negociação já Cadastrada!");
        }
         */
        if (count($msg)) {
            return $msg;
        }

        return false;
    }

    public function salvar() {
        return $this->inserir(array(
            "ano_base" => $this->ano_base,
            "parcela" => $this->parcela,
            "valor_parcela" => $this->valor_parcela,
            "multa" => $this->multa,
            "juros" => $this->juros,
            "desconto" => $this->desconto,
            "id_protesto" => $this->id_protesto));
    }
}
