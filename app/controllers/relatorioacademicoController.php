<?php

class Relatorioacademico extends Controller{
    
    public $matricula, $turma, $_msg;
    private $_redirect;
    private $_session;
    private $serie;
    private $mstatus;


    public function init() {
        parent::init();
        $this->matricula = new MatriculaModel();
        $this->turma = new TurmaModel();
        $this->serie = new SerieModel();
        $this->mstatus = new MatriculastatusModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_session = new SessionHelper();
    }

    public function index_action(){
        if($this->_session->checkSession("anobase")){
            $anoselecionado = $_SESSION['anobase'];
            $txt_ano = new AnoModel($anoselecionado);
            $this->addNavegacao(array("Relatórios Academicos $txt_ano->descricao" => "relatorioacademico/index"));
            //$this->mostrar($this->ftipo);die();
        }  else {
            $this->_msg->informacao("Por favor, selecione o ano base em que deseja trabalhar!");
            $this->_redirect->goToControllerAction('relatorioacademico', 'selecioneano');
        }
        $this->view();
    }
    
    public function alunos(){
        $this->addNavegacao(array('Relatórios Acadêmicos' => "relatorioacademico/index", 
            "Relação de Alunos" => "relatorioacademico/alunos"
        ));
        $anoselecionado = $_SESSION['anobase'];
        if(!empty($_POST)){
            $caracter = array("'",'"',"-","*","_",',');
                $select = "SELECT e.id_turma, e.descricao, e.vagas, e.turno, e.id_serie
                            FROM ano a, segmento b, ano_letivo c, serie d, turma e
                            WHERE (a.id_ano = c.id_ano) 
                            AND (c.id_segmento = b.id_segmento)
                            AND (c.id_ano_letivo = d.id_ano_letivo)
                            AND (d.id_serie = e.id_serie)
                            AND (a.id_ano = $anoselecionado)";
                if($_POST['id_turma']){
                    $turma = str_replace($caracter, "", $_POST['id_turma']);
                    $andnome = " AND (e.id_turma = {$turma})";
                }

                $busca = $select.$andnome;

                $sql = $this->matricula->pesquisar($busca);
            // $this->mostrar($result);die();
            if($sql){
                $rel = new Relatorioaluno($sql);
                $rel->gerarRelatorio(); 
            } else {
                $this->_msg->informacao("Nenhun registro encontrado!");
            }
            $this->view();
        } else {
            $anoletivo = new AnoletivoModel();
                $anolet = $anoletivo->listarPorAno($anoselecionado);
                
                $ct = -1;
                foreach ($anolet as $val){
                    $ct++;
                   $array[$ct] = $this->serie->listarPorAnoletivo($val['id_ano_letivo']);
                   foreach ($array[$ct] as $key){
                       $array_serie[] = $key;
                   }
                }

                $serie = array();

                if($array_serie){
                    $serie = $array_serie;
                }

                $cont = -1;
                foreach ($serie as $ser){
                    $cont++;
                    $array_turmas[$cont] =  $this->turma->listarPorSerie($ser['id_serie']);
                    foreach ($array_turmas[$cont] as $key){
                        $turmas[] = $key;
                    }
                }
                $dados['turma'] = $turmas;
            $this->view($dados);
        }
    }

    public function matriculados(){
        $this->addNavegacao(array('Relatórios Acadêmicos' => "relatorioacademico/index", 
            "Matriculados / Rematriculados" => "relatorioacademico/matriculados"
        ));
        $anoselecionado = $_SESSION['anobase'];
        if(!empty($_POST)){
            $caracter = array("'",'"',"-","*","_",',');
                $select = "SELECT e.id_turma, e.descricao, e.vagas, e.turno, e.id_serie
                            FROM ano a, segmento b, ano_letivo c, serie d, turma e
                            WHERE (a.id_ano = c.id_ano) 
                            AND (c.id_segmento = b.id_segmento)
                            AND (c.id_ano_letivo = d.id_ano_letivo)
                            AND (d.id_serie = e.id_serie)
                            AND (a.id_ano = $anoselecionado)";
                if($_POST['id_turma']){
                    $turma = str_replace($caracter, "", $_POST['id_turma']);
                    $andnome = " AND (e.id_turma = {$turma})";
                }
                
                $busca = $select.$andnome;
                $sql = $this->matricula->pesquisar($busca);
                $status = null;
                if($_POST['id_matricula_status']){
                    $status = $_POST['id_matricula_status'];
                }
            // $this->mostrar($result);die();
            if($sql){
                $rel = new Relatoriomatriculados($sql, $status);
                $rel->gerarRelatorio(); 
            } else {
                $this->_msg->informacao("Nenhun registro encontrado!");
            }
            $this->view();
        } else {
            $anoletivo = new AnoletivoModel();
                $anolet = $anoletivo->listarPorAno($anoselecionado);
                
                $ct = -1;
                foreach ($anolet as $val){
                    $ct++;
                   $array[$ct] = $this->serie->listarPorAnoletivo($val['id_ano_letivo']);
                   foreach ($array[$ct] as $key){
                       $array_serie[] = $key;
                   }
                }

                $serie = array();

                if($array_serie){
                    $serie = $array_serie;
                }

                $cont = -1;
                foreach ($serie as $ser){
                    $cont++;
                    $array_turmas[$cont] =  $this->turma->listarPorSerie($ser['id_serie']);
                    foreach ($array_turmas[$cont] as $key){
                        $turmas[] = $key;
                    }
                }
                $status = $this->mstatus->listar();
                
                $dados['status'] = $status;
                
                $dados['turma'] = $turmas;
            $this->view($dados);
        }
    }

    public function espera(){
        $this->addNavegacao(array('Relatórios Acadêmicos' => "relatorioacademico/index", 
            "Lista de Espera" => "relatorioacademico/espera"
        ));
        $anoselecionado = $_SESSION['anobase'];
        if(!empty($_POST)){
            $caracter = array("'",'"',"-","*","_",',');
                $select = "SELECT e.id_turma, e.descricao, e.vagas, e.turno, e.id_serie
                            FROM ano a, segmento b, ano_letivo c, serie d, turma e
                            WHERE (a.id_ano = c.id_ano) 
                            AND (c.id_segmento = b.id_segmento)
                            AND (c.id_ano_letivo = d.id_ano_letivo)
                            AND (d.id_serie = e.id_serie)
                            AND (a.id_ano = $anoselecionado)";
                if($_POST['id_turma']){
                    $turma = str_replace($caracter, "", $_POST['id_turma']);
                    $andnome = " AND (e.id_turma = {$turma})";
                }

                $busca = $select.$andnome;

                $sql = $this->matricula->pesquisar($busca);
            // $this->mostrar($result);die();
            if($sql){
                $rel = new Relatorioespera($sql);
                $rel->gerarRelatorio(); 
            } else {
                $this->_msg->informacao("Nenhun registro encontrado!");
            }
            $this->view();
        } else {
            $anoletivo = new AnoletivoModel();
                $anolet = $anoletivo->listarPorAno($anoselecionado);
                
                $ct = -1;
                foreach ($anolet as $val){
                    $ct++;
                   $array[$ct] = $this->serie->listarPorAnoletivo($val['id_ano_letivo']);
                   foreach ($array[$ct] as $key){
                       $array_serie[] = $key;
                   }
                }

                $serie = array();

                if($array_serie){
                    $serie = $array_serie;
                }

                $cont = -1;
                foreach ($serie as $ser){
                    $cont++;
                    $array_turmas[$cont] =  $this->turma->listarPorSerie($ser['id_serie']);
                    foreach ($array_turmas[$cont] as $key){
                        $turmas[] = $key;
                    }
                }
                $dados['turma'] = $turmas;
            $this->view($dados);
        }
    }
    
    
    public function selecioneano(){
        $ano = $this->getParam("ano");
        if($ano){
            $txt_ano = new AnoModel($ano);
            $this->_session->createSession("anobase", $ano);
            $this->_msg->sucesso("Ano <strong>$txt_ano->descricao</strong> foi selecionado!");
            $this->_redirect->goToController('relatorioacademico');
        }  else {
            $this->_session->deletarSession('anobase');
            $ano = new AnoModel();
            $dados['ano'] = $ano->listar();
            $this->view($dados);
        }
    }
    
}