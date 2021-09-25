<?php

class SeguroModel extends Model {
    
    public $_tabela = 'seguro';
    public $pkName  = 'id_seguro';
    public $fkName = 'id_colaborador';


    public $id_seguro;
	public $id_seguro_status;
	public $id_seguro_tipo;
	public $valor;
    public $data_seguro;
    public $descricao;
	public $id_colaborador; 

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_seguro = $id['id_seguro'];
			$this->id_seguro_status = $id['id_seguro_status'];
			$this->id_seguro_tipo = $id['id_seguro_tipo'];
            $this->valor = $query[0]['valor'];
            $this->data_seguro = $query[0]['data_seguro'];
            $this->descricao = $query[0]['descricao'];
	    $this->id_colaborador = $id['id_colaborador'];
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
        $this->id_seguro = 0;
        if($_POST['id_seguro']){
            $this->id_seguro = $_POST['id_seguro'];
        }
        $this->id_seguro_status = $_POST['id_seguro_status'];
        $this->id_seguro_tipo = $_POST['id_seguro_tipo'];
        $this->valor = $_POST['valor'];
        $this->data_seguro = UtilHelper::formatUs($_POST['data_seguro']);
        $this->descricao = $_POST['descricao'];
        $this->id_colaborador = $_POST['id_colaborador'];
        $msg = array();
            if (!$this->id_seguro_status) {
                $msg[] = $msgs->erro("Campo status do seguro Obrigatório!");
            } 
            if (!$this->id_seguro_tipo) {
                $msg[] = $msgs->erro("Campo tipo do seguro Obrigatório!");
            }
            if(!$this->valor){
                $msg[] = $msgs->erro("Campo valor Obrigatório!");
            }
            if(!$this->data_seguro){
                $msg[] = $msgs->erro("Campo data do seguro Obrigatório!");
            }
            if(!$this->descricao){
                $msg[] = $msgs->erro("Campo descrição Obrigatório!");
            }
   
        $sql = "SELECT id_seguro FROM seguro
                WHERE (id_seguro <> {$this->id_seguro})
                AND (id_seguro_status = '{$this->id_seguro_status}')
                AND (id_seguro_tipo = '{$this->id_seguro_tipo}')
                AND (valor = '{$this->valor}')
                AND (data_seguro = '{$this->data_seguro}')
                AND (descricao = '{$this->descricao}') 
                AND (id_colaborador = '{$this->id_colaborador}') ;";
                       
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Seguro já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }

    public function salvar(){
        return $this->inserir(array("id_seguro_status" => $this->id_seguro_status,
                                      "id_seguro_tipo" => $this->id_seguro_tipo,
                                      "valor" => $this->valor,
                                      "data_seguro" => $this->data_seguro,
                                      "descricao" => $this->descricao,
                                      "id_colaborador" => $this->id_colaborador));
    }
    
    public function alterar(){
        return $this->atualizar(array("id_seguro_status" => $this->id_seguro_status,
                                      "id_seguro_tipo" => $this->id_seguro_tipo,
                                      "valor" => $this->valor,
                                      "data_seguro" => $this->data_seguro,
                                      "descricao" => $this->descricao,
                                      "id_colaborador" => $this->id_colaborador), $this->id_seguro);
    }
}
