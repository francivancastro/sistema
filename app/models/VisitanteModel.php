<?php

class VisitanteModel extends Model{
    
    public $_tabela = 'visitante';
    public $pkName  = 'id_visitante';
    
    public $id_visitante;
    public $nome;
    public $grau_parentesco;
    public $cliente;
    public $escola_anterior;
    public $como_conheceu;
    public $obs;
    public $email;
    public $celular1;
    public $celular2;
    public $telefone;
    public $cep;
    public $uf;
    public $cidade;
    public $endereco;
    public $bairro;
    public $troca;
    
    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_visitante = $query[0]['id_visitante'];
            $this->nome = $query[0]['nome'];
            $this->grau_parentesco = $query[0]['grau_parentesco'];
            $this->cliente = $query[0]['cliente'];
            $this->escola_anterior = $query[0]['escola_anterior'];
            $this->como_conheceu = $query[0]['como_conheceu'];
            $this->obs = $query[0]['obs'];
            $this->email = $query[0]['email'];
            $this->celular1 = $query[0]['celular1'];
            $this->celular2 = $query[0]['celular2'];
            $this->telefone = $query[0]['telefone'];
            $this->cep = $query[0]['cep'];
            $this->uf = $query[0]['uf'];
            $this->cidade = $query[0]['cidade'];
            $this->endereco = $query[0]['endereco'];
            $this->bairro = $query[0]['bairro'];
            $this->troca = $query[0]['troca'];
        }
    }
    
    public function listarPorCliente(){
        return $this->banco->ler($this->_tabela, "cliente=S");
    }
    public function listarPornaoCliente(){
        return $this->banco->ler($this->_tabela, "cliente=N");
    }
    
    public function validar(){
        $msgs = new MsgHelper();
        $caracter = array(".", "-",'(',')',' ');
        $this->id_visitante = 0;
        if($_POST['id_visitante']){
            $this->id_visitante = $_POST['id_visitante'];
        }
        
        $this->nome = $_POST['nome_visitante'];
        $this->grau_parentesco = $_POST['grau_parentesco'];
        $this->cliente = $_POST['cliente'];
        $this->escola_anterior = $_POST['escola_anterior'];
        $this->como_conheceu = $_POST['como_conheceu'];
        $this->obs = $_POST['obs'];
        $this->email = $_POST['email'];
        $this->celular1 = str_replace($caracter, "", $_POST['celular1']);
        $this->celular2 = str_replace($caracter, "", $_POST['celular2']);
        $this->telefone = str_replace($caracter, "", $_POST['telefone']);
        $this->cep = str_replace($caracter, "", $_POST['cep']);
        $this->uf = $_POST['uf'];
        $this->cidade = $_POST['cidade'];
        $this->endereco = $_POST['endereco'];
        $this->bairro = $_POST['bairro'];
        $this->troca = $_POST['troca'];
        $msg = array();
            if (!$this->nome) {
                $msg[] = $msgs->erro("Nome do aluno Obrigatório!");
            }
        /*
        $sql = "SELECT id_visitante FROM visitante
                WHERE (id_visitante <> {$this->id_visitante})
                AND (nome = '{$this->nome}')
                AND (email = '{$this->email}')
                AND (grau_parentesco = '{$this->grau_parentesco}');";
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Visitante já Cadastrado!");
        }
        */
        if (count($msg)) {
            return $msg;
        }
        return false;
    }
    public function salvar(){
        return $this->inserir(array(
            "nome" => $this->nome,
            "grau_parentesco" => $this->grau_parentesco,
            "cliente" => $this->cliente,
            "escola_anterior" => $this->escola_anterior,
            "como_conheceu" => $this->como_conheceu,
            "obs" => $this->obs,
            "email" => $this->email,
            "celular1" => $this->celular1,
            "celular2" => $this->celular2,
            "telefone" => $this->telefone,
            "cep" => $this->cep,
            "uf" => $this->uf,
            "cidade" => $this->cidade,
            "endereco" => $this->endereco,
            "bairro" => $this->bairro,
            "troca" => $this->troca,
        ));
    }
    
    public function alterar(){
        return $this->atualizar(array(
            "nome" => $this->nome,
            "grau_parentesco" => $this->grau_parentesco,
            "cliente" => $this->cliente,
            "escola_anterior" => $this->escola_anterior,
            "como_conheceu" => $this->como_conheceu,
            "obs" => $this->obs,
            "email" => $this->email,
            "celular1" => $this->celular1,
            "celular2" => $this->celular2,
            "telefone" => $this->telefone,
            "cep" => $this->cep,
            "uf" => $this->uf,
            "cidade" => $this->cidade,
            "endereco" => $this->endereco,
            "bairro" => $this->bairro,
            "troca" => $this->troca,
        ), $this->id_visitante);
    }
}

