<?php

class InstituicaoModel extends Model{
    
    public $_tabela = 'instituicao';
    public $pkName  = 'id_instituicao';
    
    public $id_instituicao;
	public $id_instituicao_status;
    public $nome;
	public $cnpj;
	public $inscricao_estadual;
    public $id_estado;
	public $id_municipio;

   public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_instituicao = $id; 
            $this->id_instituicao_status = $query[0]['id_instituicao_status'];
            $this->nome = $query[0]['nome'];
            $this->cnpj = $query[0]['cnpj'];
			$this->inscricao_estadual = $query[0]['inscricao_estadual'];
			$this->id_estado = $query[0]['id_estado'];
			$this->id_municipio = $query[0]['id_municipio'];
			
        }
    }
    public function validar(){
        $msgs = new MsgHelper();
        $caracter = array(".", "/" , "-");
        $this->id_instituicao = 0;
        if($_POST['id_instituicao']){
            $this->id_instituicao = $_POST['id_instituicao'];
        }
        $this->id_instituicao_status = $_POST['id_instituicao_status'];
        $this->nome = $_POST['nome'];
        $this->cnpj = str_replace($caracter, "", $_POST['cnpj']);
        $this->inscricao_estadual = str_replace($caracter, "", $_POST['inscricao_estadual']);
        $this->id_estado = $_POST['id_estado'];
        $this->id_municipio = $_POST['id_municipio'];
        $msg = array();
            if (!$this->id_instituicao_status) {
                $msg[] = $msgs->erro("Campo Status Obrigatório!");
            } 
            if (!$this->nome) {
                $msg[] = $msgs->erro("Campo Nome Obrigatório!");
            }
            if(!$this->cnpj){
                $msg[] = $msgs->erro("Campo CNPJ Obrigatório!");
            }
            if(!$this->inscricao_estadual){
                $msg[] = $msgs->erro("Campo Inscrição Estadual Obrigatório!");
            }
            if(!$this->id_estado){
                $msg[] = $msgs->erro("Campo Estado Obrigatório!");
            }
            if(!$this->id_municipio){
                $msg[] = $msgs->erro("Campo Municipio Obrigatório!");
            }
   
        $sql = "SELECT id_instituicao FROM instituicao
                WHERE (id_instituicao <> {$this->id_instituicao})
                AND (cnpj = '{$this->cnpj}') ;";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Instituição já Cadastrada!");
        }
        if (count($msg)) {
            return $msg;
        }
        
        return false;
    }

    public function salvar(){
        return $this->inserir(array("id_instituicao_status" => $this->id_instituicao_status,
                                      "nome" => $this->nome,
                                      "cnpj" => $this->cnpj,
                                      "inscricao_estadual" => $this->inscricao_estadual,
                                      "id_estado" => $this->id_estado,
                                      "id_municipio" => $this->id_municipio));
    }
    public function alterar(){
        return $this->atualizar(array("id_instituicao_status" => $this->id_instituicao_status,
                                      "nome" => $this->nome,
                                      "cnpj" => $this->cnpj,
                                      "inscricao_estadual" => $this->inscricao_estadual,
                                      "id_estado" => $this->id_estado,
                                      "id_municipio" => $this->id_municipio), $this->id_instituicao);
    }
 
    
}
