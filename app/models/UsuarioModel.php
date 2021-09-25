<?php

class UsuarioModel extends Model{
    
    public $_tabela = "usuario";
    public $pkName = "id_usuario";
    public $fkName = 'id_pessoa';


    public $id_usuario;
    public $login;
    public $senha;
    public $senhaconf;
    public $habilitado;
    public $id_pessoa;

    public function __construct($id = null){
        $this->banco = Banco::instanciar();
        if(isset($id)){
            $query = $this->banco->ler($this->_tabela, $this->pkName.'='.$id);
            $this->id_usuario = $id;
            $this->login = $query[0]['login'];
            $this->senha = $query[0]['senha'];
            $this->habilitado = $query[0]['habilitado'];
            $this->id_pessoa = $query[0]['id_pessoa'];
        }
    }
    
    public function getUsuarioHabilitado(){
        $habilitado = "Sim";
        if($this->habilitado == "N"){
            $habilitado = "Não";
        }
        return $habilitado;
    }
    
    public function pegaUsuarioPorPessoa($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
        
    }

    public function validar(){
        
        $msgs = new MsgHelper();
        $this->id_usuario = 0;
        if($_POST['id_usuario']){
            $this->id_usuario = $_POST['id_usuario'];
        }
        $this->id_pessoa = $_POST['id_pessoa'];
        $this->login = $_POST['login'];
        $this->senha = $_POST['senha'];
        $this->senhaconf = $_POST['senhaconf'];
        $this->habilitado = $_POST['habilitado'];
        $msg = array();
            if (!$this->login) {
                $msg[] = $msgs->erro("Campo Login Obrigatório!");
            }
            if (!$this->senha) {
                $msg[] = $msgs->erro("Campo Senha Obrigatório!");
            }
            if (!$this->senhaconf) {
                $msg[] = $msgs->erro("Campo Confirmar Senha Obrigatório!");
            }
            if ($this->senha != $this->senhaconf) {
                $msg[] = $msgs->erro("Senhas não Correspondem!");
            }
            
            if (!$this->habilitado) {
                $msg[] = $msgs->erro("Campo Habilitar não Selecionado!");
            }
        $sql = "SELECT id_usuario FROM usuario
                WHERE (id_usuario <> {$this->id_usuario})
                AND (id_pessoa = '{$this->id_pessoa}')
                AND (login = '{$this->login}')
                AND (senha = '{$this->senha}');";
                       
        $rg = $this->consultar($sql);
        if ($rg) {
                $msg[] = $msgs->erro("Usuário já Cadastrado!");
        }
        if (count($msg)) {
            return $msg;
        }
        return false;
    }
    
    public function validarExcluir($id) {
            $this->id_usuario = $id;
            $msgs = new MsgHelper();
            $msg = array();
            if ($this->id_usuario) {
                $sql = "select id_usuario_grupo
                        from usuario_grupo
                        where (id_usuario = {$this->id_usuario})";
                $rg = $this->consultar($sql);
                if ($rg) {
                    $msg[] = $msgs->erro("Existem Ocorrências vinculadas a este Usuário!");
                }
            }
            if (count($msg)) {
                return $msg;
            }
            return false;
    }
    
    public function salvar(){
        return $this->inserir(array("id_pessoa" => $this->id_pessoa,
                                        "login" => $this->login,
                                        "senha" => md5($this->senha),
                                   "habilitado" => $this->habilitado));
    }
    
    public function alterar(){
        return $this->atualizar(array("id_usuario" => $this->id_usuario,
                                       "id_pessoa" => $this->id_pessoa,
                                           "login" => $this->login,
                                           "senha" => md5($this->senha),
                                      "habilitado" => $this->habilitado), $this->id_usuario);
    }
}
