<?php

class AgendaModel extends Model {

    public $_tabela = 'agenda';
    public $pkName = 'id_agenda';
    public $fkName = 'id_matricula_espera';
    public $fkName2 = 'id_usuario';
    
    public $id_agenda;
    public $id_matricula_espera;
    public $id_usuario;
    public $data_operacao;
    public $data_agendamento;
    public $data_conclusao;
    public $hora_inicio;
    public $hora_fim;
    public $aprovado;
    public $tipo_agenda;
    public $obs_agenda;
    public $obs_atendimento;
    

    public function __construct($id = null) {
        $this->banco = Banco::instanciar();
        if (isset($id)) {
            $query = $this->banco->ler($this->_tabela, $this->pkName . '=' . $id);
            $this->id_agenda = $query[0]['id_agenda'];
            $this->id_matricula_espera = $query[0]['id_matricula_espera'];
            $this->id_usuario = $query[0]['id_usuario'];
            $this->data_operacao = $query[0]['data_operacao'];
            $this->data_agendamento = $query[0]['data_agendamento'];
            $this->data_conclusao = $query[0]['data_conclusao'];
            $this->hora_inicio = $query[0]['hora_inicio'];
            $this->hora_fim = $query[0]['hora_fim'];
            $this->aprovado = $query[0]['aprovado'];
            $this->tipo_agenda = $query[0]['tipo_agenda'];
            $this->obs_agenda = $query[0]['obs_agenda'];
            $this->obs_atendimento = $query[0]['obs_atendimento'];
            
        }
    }
    
    public function listarPorMatricula($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName."=".$id);
        }
        return false;
    }
    
    public function listarPorUsuario($id){
        if($id){
            return $this->banco->ler($this->_tabela, $this->fkName2."=".$id);
        }
        return false;
    }
    
    public function listarPorTipo($tipo){
        if($tipo){
            $sql = "SELECT a.id_agenda, a.id_matricula_espera,
                a.id_usuario, a.data_operacao, a.data_agendamento, 
                a.data_conclusao, a.hora_inicio, a.hora_fim, a.aprovado, 
                a.tipo_agenda, a.obs_agenda, a.obs_atendimento,c.nome, d.nome as vnome, e.descricao as turma, e.turno
                FROM agenda a, matricula_espera b, m_aluno c , visitante d, turma e
                WHERE a.id_matricula_espera = b.id_matricula_espera
                AND b.id_m_aluno = c.id_m_aluno
                AND b.id_visitante = d.id_visitante
                AND b.id_turma = e.id_turma
                AND a.tipo_agenda = '{$tipo}';";
            return $this->banco->busca($sql);
        }
        return false;
    }
    
    public function listarPorTipoeMatricula($id, $tipo){
        if($tipo){
            $sql = "SELECT a.id_agenda, a.id_matricula_espera, a.id_usuario, a.data_operacao, a.data_agendamento, a.data_conclusao, a.hora_inicio, a.hora_fim, a.aprovado, a.tipo_agenda, a.obs_agenda,c.nome 
                FROM agenda a, matricula_espera b, m_aluno c 
                WHERE a.id_matricula_espera = b.id_matricula_espera
                AND b.id_m_aluno = c.id_m_aluno
                AND a.id_matricula_espera = '{$id}'
                AND a.tipo_agenda = '{$tipo}';";
            return $this->banco->busca($sql);
        }
        return false;
    }
    
    public function validar() {
        $msgs = new MsgHelper();
        $this->id_agenda = 0;
        if ($_POST['id_agenda']) {
            $this->id_agenda = $_POST['id_agenda'];
        }
        $caracter = array(' AM', ' PM');
        $data_opera = new DateTime($_POST['data_operacao']);
        $data_con = new DateTime($_POST['data_conclusao']);
        ($_POST['id_matricula_espera'] ? $this->id_matricula_espera = $_POST['id_matricula_espera'] : $this->id_matricula_espera = NULL);
        ($_POST['id_usuario'] ? $this->id_usuario = $_POST['id_usuario'] : $this->id_usuario = NULL);
        
        ($_POST['data_operacao'] ? $this->data_operacao = $data_opera->format('Y-m-d H:i:s') : $this->data_operacao = NULL);
        ($_POST['data_agendamento'] ? $this->data_agendamento = UtilHelper::formatUs($_POST['data_agendamento']) : $this->data_agendamento = NULL);
        ($_POST['data_conclusao'] ?  $this->data_conclusao = $data_con->format('Y-m-d H:i:s') : $this->data_conclusao = NULL);
        
        $this->hora_inicio = str_replace($caracter, '', $_POST['hora_inicio']);
        $this->hora_fim = str_replace($caracter, '', $_POST['hora_fim']);
        ($_POST['aprovado'] ? $this->aprovado = $_POST['aprovado'] : $this->aprovado = NULL);
        ($_POST['tipo_agenda'] ? $this->tipo_agenda = $_POST['tipo_agenda'] : $this->tipo_agenda = NULL);
        ($_POST['obs_agenda'] ? $this->obs_agenda = $_POST['obs_agenda'] : $this->obs_agenda = NULL);
        ($_POST['obs_atendimento'] ? $this->obs_atendimento = $_POST['obs_atendimento'] : $this->obs_atendimento = NULL);
        
        $msg = array();
        
        $sql = "SELECT id_agenda FROM agenda
                WHERE (id_agenda <> {$this->id_agenda})
                AND (tipo_agenda = '{$this->tipo_agenda}')
                AND (id_matricula_espera = '{$this->id_matricula_espera}');";
        $rg = $this->consultar($sql);
        if ($rg) {
            $msg[] = $msgs->erro("Agendamento já Cadastrado!");
        }
        
        if (count($msg)) {
            return $msg;
        }
        return false;
    }

    public function salvar() {
        return $this->inserir(array(
            "id_matricula_espera" => $this->id_matricula_espera,
            "id_usuario" => $this->id_usuario,
            "data_operacao" => $this->data_operacao,
            "data_agendamento" => $this->data_agendamento,
            "data_conclusao" => $this->data_conclusao,
            "hora_inicio" => $this->hora_inicio,
            "hora_fim" => $this->hora_fim,
            "aprovado" => $this->aprovado,
            "tipo_agenda" => $this->tipo_agenda,
            "obs_agenda" => $this->obs_agenda,
            "obs_atendimento" => $this->obs_atendimento,
            ));
    }
    public function alterar() {
        
        return $this->atualizar(array(
                "id_matricula_espera" => $this->id_matricula_espera,
                "id_usuario" => $this->id_usuario,
                "data_operacao" => $this->data_operacao,
                "data_agendamento" => $this->data_agendamento,
                "data_conclusao" => $this->data_conclusao,
                "hora_inicio" => $this->hora_inicio,
                "hora_fim" => $this->hora_fim,
                "aprovado" => $this->aprovado,
                "tipo_agenda" => $this->tipo_agenda,
                "obs_agenda" => $this->obs_agenda,
                "obs_atendimento" => $this->obs_atendimento,
            ), $this->id_agenda);
    }
}
