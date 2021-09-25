<?php

class EmpresaModel extends Model {
    
    public $_tabela = 'empresa';
    public $pkName = 'id_empresa';
    
    public $id_empresa;
    public $empresa_nome;
    public $nome_fantasia;
    public $cnpj;
    public $email;
    public $telefone1;
    public $telefone2;
    public $telefone3;
    public $inscricao_estadual;
    public $empresa_logo;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_empresa = $id;
            $this->empresa_nome = $query[0]['empresa_nome'];
            $this->nome_fantasia = $query[0]['nome_fantasia'];
            $this->cnpj = $query[0]['cnpj'];
            $this->email = $query[0]['email'];
            $this->telefone1 = $query[0]['telefone1'];
            $this->telefone2 = $query[0]['telefone2'];
            $this->telefone3 = $query[0]['telefone3'];
            $this->inscricao_estadual = $query[0]['inscricao_estadual'];
            
            $this->empresa_logo = $query[0]['empresa_logo'];
        }
    }
    
    public function validar(){
        $caracter = array("'",'"','-','.',',','*','(',')',"/", "+",' ');
        $msgs = new MsgHelper();
        $this->id_empresa = 0;
        if($_POST['id_empresa']){
            $this->id_empresa = $_POST['id_empresa'];
        }
        
        $this->empresa_nome = $_POST['empresa_nome'];
        $this->nome_fantasia = $_POST['nome_fantasia'];
        $this->cnpj = str_replace($caracter, "", $_POST['cnpj']);
        $this->email = $_POST['email'];
        $this->telefone1 = str_replace($caracter, "", $_POST['telefone1']);
        $this->telefone2 = str_replace($caracter, "", $_POST['telefone2']);
        $this->telefone3 = str_replace($caracter, "", $_POST['telefone3']);
        $msg = array();
            if (!$this->empresa_nome) {
                $msg[] = $msgs->erro("Campo Nome da Empresa Obrigatório!");
            }
            if (!$this->nome_fantasia) {
                $msg[] = $msgs->erro("Campo Nome Fantasia Obrigatório!");
            }
            if (!$this->cnpj) {
                $msg[] = $msgs->erro("Campo CNPJ Obrigatório!");
            }
        $sql = "SELECT id_empresa FROM empresa
                WHERE (id_empresa <> {$this->id_empresa})
                AND (empresa_nome = '{$this->empresa_nome}')
                AND (cnpj = '{$this->cnpj}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Empresa Já Cadastrada!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }
     public function salvar(){
        return $this->inserir(array(
                "empresa_nome" => $this->empresa_nome,
                "nome_fantasia" => $this->nome_fantasia,
                "cnpj" => $this->cnpj,
                "telefone1" => $this->telefone1,
                "telefone2" => $this->telefone2,
                "telefone3" => $this->telefone3,
                "empresa_logo" => $this->empresa_logo
            ));
    }
    
    public function alterar(){
        return $this->atualizar(array(
                "empresa_nome" => $this->empresa_nome,
                "nome_fantasia" => $this->nome_fantasia,
                "cnpj" => $this->cnpj,
                "telefone1" => $this->telefone1,
                "telefone2" => $this->telefone2,
                "telefone3" => $this->telefone3,
                "empresa_logo" => $this->empresa_logo
            ), $this->id_empresa);
    }
}
