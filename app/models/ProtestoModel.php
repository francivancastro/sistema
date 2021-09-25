<?php

class ProtestoModel extends Model {
    
    public $_tabela = 'protesto';
    public $pkName  = 'id_protesto';
    private $fkName = "id_responsavel";


    public $id_protesto;
    public $id_responsavel;
    public $id_empresa;
    public $id_protesto_status;
    public $id_protesto_tipo;
    public $valor_contrato;
    public $valor_protesto;
    public $data_emissao;
    public $data_vencimento;
    public $data_inclusao;
    public $numero; 
    public $id_aluno;
    public $ano_base;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_protesto = $id;
            $this->id_responsavel = $query[0]["id_responsavel"];
            $this->id_empresa = $query[0]["id_empresa"];
            $this->id_protesto_status = $query[0]["id_protesto_status"];
            $this->id_protesto_tipo = $query[0]["protesto"];
            $this->valor_contrato = $query[0]['valor_contrato'];
            $this->valor_protesto = $query[0]['valor_protesto'];
            $this->data_emissao = $query[0]['data_emissao'];
            $this->data_vencimento = $query[0]['data_vencimento'];
            $this->data_inclusao = $query[0]['data_inclusao'];
            $this->numero = $query[0]['numero'];
            $this->id_aluno = $query[0]['id_aluno'];
            $this->ano_base = $query[0]['ano_base'];
        }
    }
    
    public function listarPorColaborador($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }

    public function validar(){
        $caracter = array('.');
        $this->id_protesto = 0;
        if($_POST['id_protesto']){
            $this->id_protesto = $_POST['id_protesto'];
        }
        $this->id_responsavel = $_POST["id_responsavel"];
        $this->id_empresa = $_POST["id_empresa"];
        $this->id_protesto_status = $_POST["id_protesto_status"];
        $this->id_protesto_tipo = $_POST["id_protesto_tipo"];
        $this->valor_contrato = str_replace(',', '.', str_replace($caracter,"",  $_POST['valor_contrato']));
        $this->valor_protesto = str_replace(',', '.', str_replace($caracter,"",  $_POST['valor_protesto']));
        $this->data_emissao = UtilHelper::formatUs($_POST['data_emissao']);
        $this->data_vencimento = UtilHelper::formatUs($_POST['data_vencimento']);
        $this->data_inclusao = date('Y-m-d');
        $this->numero = $_POST['numero'];
        $this->id_aluno = $_POST['id_aluno'];
        $this->ano_base = $_POST['ano_base'];
        $msg = array();
        $val = array();
            if (!$this->id_responsavel) {
                $msg['id_responsavel'] = "Por favor, Selecione um Responsável!";
            }
            if (!$this->ano_base) {
                $msg['ano_base'] = "Por favor, Selecione uma Ano Base!";
            }
            if (!$this->id_empresa) {
                $msg['id_empresa'] = "Por favor, Selecione uma Empresa!";
            }
            if (!$this->valor_contrato) {
                $msg['valor_contrato'] = "Campo Valor do Contrato Obrigatório!";
            }
            if (!$this->valor_protesto) {
                $msg['valor_protesto'] = "Campo Valor do Protesto Obrigatório!";
            }
            if (!$this->id_protesto_tipo) {
                $msg['id_protesto_tipo'] = "Por favor, selecione a espécie de protesto!";
            }
            if (!$this->id_aluno) {
                $msg['id_aluno'] = "Por favor, selecione o aluno!";
            }
            if($this->data_emissao > $this->data_vencimento){
                $msg[] = "Data de Emissão é Menor que a Data de Vencimento!";
            }
            if (!$this->data_emissao) {
                $msg['data_emissao'] = "Campo Data de Emissão Obrigatório!";
            }
            if (!$this->data_vencimento) {
                $msg['data_vencimento'] = "Campo Data de Vencimento Obrigatório!";
            }
            if (!$this->numero) {
                $msg['numero'] = "Campo Numero Obrigatório!";
            }
        $sql = "SELECT id_protesto FROM protesto
                WHERE (id_protesto <> {$this->id_protesto})
                AND (id_responsavel = '{$this->id_responsavel}')
                AND (id_empresa = '{$this->id_empresa}')
                AND (id_aluno = '{$this->id_aluno}')
                AND (numero = '{$this->numero}')
                AND (valor_protesto = '{$this->valor_protesto}')
                AND (data_vencimento = '{$this->data_vencimento}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = "Protesto Já Cadastrado!";
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array("valor_contrato" => $this->valor_contrato,
                                    "valor_protesto" => $this->valor_protesto,
                                    "data_inclusao" => $this->data_inclusao,
                                    "data_emissao" => $this->data_emissao,
                                    "data_vencimento" => $this->data_vencimento,
                                    "numero" => $this->numero,
                                    "id_responsavel" => $this->id_responsavel,
                                    "id_empresa" => $this->id_empresa,
                                    "id_protesto_status" => $this->id_protesto_status,
                                    "id_protesto_tipo" => $this->id_protesto_tipo,
                                    "id_aluno" => $this->id_aluno,
                                    "ano_base" => $this->ano_base,
                ));
    }
    
    public function alterar(){
        return $this->atualizar(array("valor_contrato" => $this->valor_contrato,
                                    "valor_protesto" => $this->valor_protesto,
                                    "data_emissao" => $this->data_emissao,
                                    "data_vencimento" => $this->data_vencimento,
                                    "numero" => $this->numero,
                                    "id_responsavel" => $this->id_responsavel,
                                    "id_empresa" => $this->id_empresa,
                                    "id_protesto_status" => $this->id_protesto_status,
                                    "id_protesto_tipo" => $this->id_protesto_tipo,
                                    "id_aluno" => $this->id_aluno,
                                    "ano_base" => $this->ano_base,), $this->id_protesto);
    }
}
