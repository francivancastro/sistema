<?php

class ColaboradorModel extends Model{
    
    public $_tabela = 'colaborador';
    public $pkName  = 'id_colaborador';
    public $fkName = 'id_pessoa';


    public $id_colaborador;
    public $id_colaborador_tipo;
    public $id_pessoa;
    public $id_orientador;
    


    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_colaborador = $id;
            $this->id_colaborador_tipo = $query[0]['id_colaborador_tipo'];
            $this->id_pessoa = $query[0]['id_pessoa'];
            $this->id_orientador = $query[0]['id_orientador'];
        }
    }
    
    public function pegaPorPessoa($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
        
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_colaborador = 0;
        if($_POST['id_colaborador']){
            $this->id_colaborador = $_POST['id_colaborador'];
        }
        $this->id_colaborador_tipo = $_POST['id_colaborador_tipo'];
        $this->id_pessoa = $_POST['id_pessoa'];
        $this->id_orientador = $_POST['id_orientador'];
        $msg = array();
            if (!$this->id_colaborador_tipo) {
                $msg[] = $msgs->erro("Campo Tipo de Colaborador Obrigatório!");
            }
            if (!$this->id_orientador) {
                $msg[] = $msgs->erro("Um Orientador deve ser Selecionado!");
            }
        $sql = "SELECT id_colaborador FROM colaborador
                WHERE (id_colaborador <> {$this->id_colaborador})
                AND (id_colaborador_tipo = '{$this->id_colaborador_tipo}')
                AND (id_pessoa = '{$this->id_pessoa}');";
        $rg = $this->consultar($sql);
        
        if ($rg) {
                $msg[] = $msgs->erro("Colaborador Já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
    
    public function validarExcluir($id) {
            $this->id_colaborador = $id;
            $msgs = new MsgHelper();
            $msg = array();
            if ($this->id_colaborador) {
                $ferias = "select id_colaborador
                        from ferias
                        where (id_colaborador = {$this->id_colaborador})";
                        
                $contrato = "select id_colaborador
                        from contrato
                        where (id_colaborador = {$this->id_colaborador})";
                        
                $seguro = "select id_colaborador
                        from seguro
                        where (id_colaborador = {$this->id_colaborador})";
                        
                $patrimonio = "select id_colaborador
                        from bem_patrimonial
                        where (id_colaborador = {$this->id_colaborador})";
                
                $rg1 = $this->consultar($ferias);
                $rg2 = $this->consultar($contrato);
                $rg3 = $this->consultar($seguro);
                $rg4 = $this->consultar($patrimonio);
                if ($rg1 || $rg2 || $rg3 || $rg4) {
                    $msg[] = $msgs->erro("Existem Ocorrências vinculadas a este Colaborador!");
                }
            }
            if (count($msg)) {
                return $msg;
            }
            return false;
    }
    
    public function salvar(){
        $pes = new PessoaModel();
        return $this->inserir(array("id_colaborador_tipo" => $this->id_colaborador_tipo,
                                              "id_pessoa" => $pes->pegaUltimoId(),
                                             "id_orientador" => $this->id_orientador));
    }
    
    public function alterar(){
        return $this->atualizar(array("id_colaborador_tipo" => $this->id_colaborador_tipo,
                                              "id_pessoa" => $this->id_pessoa,
                                        "id_orientador" => $this->id_orientador), $this->id_colaborador);
    }
}
