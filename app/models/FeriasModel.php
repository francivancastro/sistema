<?php

class FeriasModel extends Model {
    
    public $_tabela = 'ferias';
    public $pkName  = 'id_ferias';
    private $fkName = "id_colaborador";


    public $id_ferias;
    public $id_colaborador;
    public $data_inicio;
    public $data_fim;
    public $descricao; 

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_ferias = $id;
            $this->id_colaborador = $query[0]["id_colaborador"];
            $this->data_inicio = $query[0]['data_inicio'];
            $this->data_fim = $query[0]['data_fim'];
            $this->descricao = $query[0]['descricao'];
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
        $this->id_ferias = 0;
        if($_POST['id_ferias']){
            $this->id_ferias = $_POST['id_ferias'];
        }
        $this->id_colaborador = $_POST["id_colaborador"];
        $this->data_inicio = UtilHelper::formatUs($_POST['data_inicio']);
        $this->data_fim = UtilHelper::formatUs($_POST['data_fim']);
        $this->descricao = $_POST['descricao'];
        $msg = array();
            if (!$this->descricao) {
                $msg[] = $msgs->erro("Campo Descrição Obrigatório!");
            }
            if($this->data_inicio > $this->data_fim){
                $msg[] = $msgs->erro("Data Inicial é Menor que a Data final!");
            }
            if (!$this->data_inicio) {
                $msg[] = $msgs->erro("Campo Inicio de Ferias Obrigatório!");
            }
            if (!$this->data_fim) {
                $msg[] = $msgs->erro("Campo Termino de Ferias Obrigatório!");
            }
        $sql = "SELECT id_ferias FROM ferias
                WHERE (id_ferias <> {$this->id_ferias})
                AND (data_inicio = '{$this->data_inicio}')
                AND (data_fim = '{$this->data_fim}')
                AND (descricao = '{$this->descricao}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Férias Já Cadastrada!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function salvar(){
        return $this->inserir(array("data_inicio" => $this->data_inicio,
                                       "data_fim" => $this->data_fim,
                                      "descricao" => $this->descricao,
                                    "id_colaborador" => $this->id_colaborador));
    }
    
    public function alterar(){
        return $this->atualizar(array("data_inicio" => $this->data_inicio,
                                         "data_fim" => $this->data_fim,
                                        "descricao" => $this->descricao,
                                   "id_colaborador" => $this->id_colaborador), $this->id_ferias);
    }
}
