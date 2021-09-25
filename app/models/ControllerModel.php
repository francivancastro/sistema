<?php

class ControllerModel extends Model {
    
    public $_tabela = 'controller';
    public $pkName = 'id_controller';
    
    public $id_controller;
    public $descricao;
    public $habilitado;
    public $controller;

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_controller = $id;
            $this->descricao = $query[0]['descricao'];
            $this->habilitado = $query[0]['habilitado'];
            $this->controller = $query[0]['controller'];
        }
    }
    
    public function validar() {
        
        $msgs = new MsgHelper();
        $this->id_controller = 0;
        if ($_POST['id_controller']) {
            $this->id_controller = $_POST['id_controller'];
        }
        ($_POST['descricao'] ? $this->descricao = $_POST['descricao'] : $this->descricao = NULL);
        ($_POST['habilitado'] ? $this->habilitado = $_POST['habilitado'] : $this->habilitado = NULL);
        ($_POST['controller'] ? $this->controller = $_POST['controller'] : $this->controller = NULL);
        
        $msg = array();
        
        if (!$this->controller) {
            $msg[] = $msgs->erro("Digite o nome do controller!");
        }
        
        if($this->id_controller == 0){
            $sql = "SELECT id_controller FROM controller
                WHERE (id_controller <> {$this->id_controller})
                AND (descricao = '{$this->descricao}');";
                $rg = $this->consultar($sql);
                if ($rg) {
                    $msg[] = $msgs->erro("Controller Já Cadastrado!");
                }
        }
        if (count($msg)) {
            return $msg;
        }
        return false;
    }

    public function salvar() {
        return $this->inserir(array(
                    "descricao" => $this->descricao,
                    "habilitado" => $this->habilitado,
                    "controller" => $this->controller,
        ));
    }

    public function alterar() {
        return $this->atualizar(array(
                    "descricao" => $this->descricao,
                    "habilitado" => $this->habilitado,
                    "controller" => $this->controller,
                    ));
    }
    
    public function verificaController($controller) {
        $pms = $this->banco->ler($this->_tabela, " controller = '{$controller}'");
        if ($pms) {
            foreach ($pms as $pm){
                return $pm;
            }
        }
        return false;
    }
}
