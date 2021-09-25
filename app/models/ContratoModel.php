<?php

class ContratoModel extends Model {
    
    public $_tabela = 'contrato';
    public $pkName  = 'id_contrato';
    public $fkName = "id_colaborador";
    
    public $id_contrato;
    public $data_inicio;
    public $data_final;
    public $projeto;
    public $id_colaborador;
    public $id_area_atuacao;
    public $valor; 

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_contrato = $id; 
            $this->data_inicio = $query[0]['data_inicio'];
            $this->data_final = $query[0]['data_final'];
            $this->projeto = $query[0]["id_projeto"];
            $this->id_colaborador = $query[0]['id_colaborador'];
            $this->id_area_atuacao = $query[0]['id_area_atuacao'];
            $this->valor = $query[0]['valor'];
			
        }
    }
    
    public function listarPorColaborador($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
   public function validar(){
        $msgs = new MsgHelper();
        $this->id_contrato = 0;
        if($_POST['id_contrato']){
            $this->id_contrato = $_POST['id_contrato'];
        }
        $this->data_inicio = UtilHelper::formatUs($_POST['data_inicio']);
        $this->data_final = UtilHelper::formatUs($_POST['data_final']);
        $this->projeto = $_POST["projeto"];
        $this->id_colaborador = $_POST['id_colaborador'];
        $this->id_area_atuacao = $_POST['id_area_atuacao'];
        $this->valor = $_POST['valor'];
        $msg = array();
            if (!$this->data_inicio) {
                $msg[] = $msgs->erro("Periodo Inicial de Contrato Obrigatório!");
            } 
            if (!$this->data_final) {
                $msg[] = $msgs->erro("Periodo Final de Contrato Obrigatório!");
            }
            if($this->data_inicio > $this->data_final){
                $msg[] = $msgs->erro("A Data Inicial é maior que a Final!");
            }
            if(!$this->id_colaborador){
                $msg[] = $msgs->erro("Campo Colaborador Obrigatório!");
            }
            if(!$this->id_area_atuacao){
                $msg[] = $msgs->erro("Selecione Uma Área de Atuação!");
            }
            if(!$this->valor){
                $msg[] = $msgs->erro("Campo Valor Obrigatório!");
            }
            if(!$this->projeto){
                $msg[] = $msgs->erro("Campo Projeto Obrigatório!");
            }
   
        $sql = "SELECT id_contrato FROM contrato
                WHERE (id_contrato <> {$this->id_contrato})
                AND (data_inicio = '{$this->data_inicio}')
                AND (data_final = '{$this->data_final}')
                AND (id_colaborador = '{$this->id_colaborador}')
                AND (id_area_atuacao = '{$this->id_area_atuacao}')
                AND (valor = '{$this->valor}');";
                       
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Contrato já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }

    public function salvar(){
        return $this->inserir(array("data_inicio" => $this->data_inicio,
                                    "data_final" => $this->data_final,
                                    "projeto" => $this->projeto,
                                    "id_colaborador" => $this->id_colaborador,
                                    "id_area_atuacao" => $this->id_area_atuacao,
                                    "valor" => $this->valor));
    }
    
    public function alterar(){
        return $this->atualizar(array("data_inicio" => $this->data_inicio,
                                    "data_final" => $this->data_final,
                                    "projeto" => $this->projeto,
                                    "id_colaborador" => $this->id_colaborador,
                                    "id_area_atuacao" => $this->id_area_atuacao,
                                    "valor" => $this->valor), $this->id_contrato);
    }
 
    
}
