<?php

class Alunosdesativados extends Controller {

    private $aluno;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->matriculaespera = new MatriculaesperaModel();
        $this->matricula= new MatriculaModel();
        $this->aluno = new MalunoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        $sql = $this->aluno->pesquisar('SELECT * FROM m_aluno WHERE nome IN ( SELECT B.nome FROM m_aluno B GROUP BY B.nome HAVING COUNT(*) > 1 ) ORDER BY nome');
        $alunos = $this->aluno->listar();
        $datas['sql'] = $sql;
        $datas['alunos'] = $alunos;
        $this->view($datas);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $validar = $this->aluno->validar();
            if($validar){
                $this->_redirect->goToUrl('matriculaespera/visitante/id/'. $_POST['id_turma']);
            } else {
                $this->aluno->salvar();
                $this->_msg->sucesso("Agendamento Realizado com Sucesso!");
                $this->_redirect->goToUrl('matriculaespera/visitante/id/'. $_POST['id_turma']);
            }
        } else {
            $this->view();
        }
    }
    
    
    

    public function excluir() {
        die('em construção');
        $id = $this->getParam('id');
        if($id){
            if($this->aluno->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("aluno");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("aluno");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("aluno");
        }
    }
    
}