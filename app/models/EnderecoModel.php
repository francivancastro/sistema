<?php

class EnderecoModel extends Model {
    
    public $_tabela = 'endereco';
    public $pkName = 'id_endereco';
    
    public $id_endereco;
    public $endereco;
    public $cep;
    public $numero;
    public $complemento;
    public $id_bairro;
    
    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if($id){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_endereco = $id; 
            $this->endereco = $query[0]['endereco'];
            $this->cep = $query[0]['cep'];
            $this->numero = $query[0]['numero'];
	    $this->complemento = $query[0]['complemento'];
	    $this->id_bairro = $query[0]['id_bairro'];
	}
    }
    public function validar(){
        $msgs = new MsgHelper();
        $this->id_endereco = 0;
        if($_POST['id_endereco']){
            $this->id_endereco = $_POST['id_endereco'];
        }
        $caracter = array(".", "-");
        $this->endereco = $_POST['endereco'];
        $this->cep = str_replace($caracter, "", $_POST['cep']);
        $this->numero = $_POST['numero'];
        $this->complemento = $_POST['complemento'];
        $this->id_bairro = $_POST['id_bairro'];
        $msg = array();
            if (!$this->endereco) {
                $msg[] = $msgs->erro("Campo Endereço Obrigatório!");
            }
            if(!$this->id_bairro){
                $msg[] = $msgs->erro("Campo Bairro Obrigatório!");
            }
   /*
        $sql = "SELECT id_endereco FROM endereco
                WHERE (id_endereco <> {$this->id_endereco})
                AND (endereco = '{$this->endereco}')
                AND (cep = '{$this->cep}')
                AND (numero = '{$this->numero}')
                AND (complemento = '{$this->complemento}')
                AND (id_bairro = '{$this->id_bairro}') ;";
                       
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Endereço já Cadastrado!");
        }
    */
        if (count($msg)) {
            return $msg;
        }
       
        return false;
    }

    public function salvar(){
        return $this->inserir(array("endereco" => $this->endereco,
                                      "cep" => $this->cep,
                                      "numero" => $this->numero,
                                      "complemento" => $this->complemento,
                                      "id_bairro" => $this->id_bairro));
    }
    
    public function alterar(){
        return $this->atualizar(array("endereco" => $this->endereco,
                                      "cep" => $this->cep,
                                      "numero" => $this->numero,
                                      "complemento" => $this->complemento,
                                      "id_bairro" => $this->id_bairro), $this->id_endereco);
    }

    
}
