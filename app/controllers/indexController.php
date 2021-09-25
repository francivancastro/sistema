<?php
class Index extends Controller{
    
    private $auth, $pessoa, $_msg, $redirect, $session, $protesto;
    
    public function init() {
        $this->pessoa = new PessoaModel();
        $this->_msg = new MsgHelper();
        $this->session = new SessionHelper();
        $this->auth = new AuthHelper();
        $this->redirect = new RedirectorHelper();
        $this->protesto = new ProtestoModel();
        $ss = $this->auth->checklogin("boolean");
        if($ss){
            if($_SESSION["userData"]["habilitado"] == "N"){
                $this->session->deletarSession("userAuth");
                $this->session->deletarSession('userData');
                $this->_msg->erro("Erro ao efetuar o Login!");
            }
        }
        
    }

    public function index_action(){
        $pes = new PessoaModel($_SESSION["userData"]["id_pessoa"]);
        $this->session->createSession('username', UtilHelper::formataNome($pes->nome));
        $select = "SELECT a.numero, b.descricao FROM protesto a, protesto_status b WHERE a.id_protesto_status = b.id_protesto_status ";
        $andamento = $select . "AND b.chave ='AND'";
        $concluido = $select . "AND b.chave ='CON'";
        $cancelado = $select . "AND b.chave ='CAN'";
        $dados = array();
        $dados['total'] = $this->protesto->pesquisar($select);
        $dados['and'] = $this->protesto->pesquisar($andamento);
        $dados['con'] = $this->protesto->pesquisar($concluido);
        $dados['can'] = $this->protesto->pesquisar($cancelado);
        $this->view($dados);
    }
    
    public function up(){
        
    }
    
    

    public function resultado(){
        if(!empty($_POST)){
            $select = "select * from pessoa";
            if($_POST['nome']){
                $caracter = array("'",'"','-','.',',','*','+','+');
                $nome = str_replace($caracter, "", $_POST['nome']);
                $sql = $select." WHERE nome LIKE '%{$nome}%'";
            }
            if($_POST['cpf']){
                $caracter = array(".","-");
                $cpf = str_replace($caracter, "", $_POST['cpf']);
                $sql = $select." WHERE cpf = {$cpf}";
            }
            if(!$_POST['nome'] && !$_POST['cpf']){
                $this->_msg->informacao("Você precisar preencher um campo!");
                $this->redirect->goToAction('index');
            }
            $result = $this->pessoa->consultar($sql);
            if(empty($result)){
                $this->_msg->informacao("Nada Encontrado!");
                $result = array();
            }
            //$this->mostrar($result);
            $dados['numero'] = $numero;
            $dados['result'] = $result;
            $this->view($dados);
        }
    }
}