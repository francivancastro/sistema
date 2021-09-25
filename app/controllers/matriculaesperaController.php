<?php

class Matriculaespera extends Controller {

    private $matriculaespera;
    private $matricula;
    private $aluno;
    private $turma;
    private $serie;
    private $visitante;
    private $_redirect;
    private $_msg;
    private $_session;
    private $agenda;

    public function init() {
        $this->matriculaespera = new MatriculaesperaModel();
        $this->matricula = new MatriculaModel();
        $this->turma = new TurmaModel();
        $this->aluno = new MalunoModel();
        $this->serie = new SerieModel();
        $this->agenda = new AgendaModel();
        $this->visitante = new VisitanteModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_session = new SessionHelper();
    }

    public function index_action() {
        $this->_session->deletarSession("turma");
        if($this->_session->checkSession("anobase")){
            $anoselecionado = $_SESSION['anobase'];
            $txt_ano = new AnoModel($anoselecionado);
            $this->addNavegacao(array("Lista de espera $txt_ano->descricao" => "matriculaespera/index"));
            //$this->mostrar($this->ftipo);die();
            if(!empty($_POST)){
                $caracter = array("'",'"',"-","*","_",',');
                $select = "SELECT e.id_turma, e.descricao, e.vagas, e.turno, e.id_serie FROM ano a, segmento b, ano_letivo c, serie d, turma e";
                if($_POST['nome']){
                    $nome = str_replace($caracter, "", $_POST['nome']);
                    $andnome = " WHERE (a.id_ano = c.id_ano) 
                    AND (c.id_segmento = b.id_segmento)
                    AND (c.id_ano_letivo = d.id_ano_letivo)
                    AND (d.id_serie = e.id_serie)
                    AND (a.id_ano = $anoselecionado)
                    AND (e.descricao LIKE '%{$nome}%')";
                }

                $busca = $select.$andnome;

                $sql = $this->matriculaespera->pesquisar($busca);

                if(!$sql){
                    $this->_msg->informacao("Nada Encontrado!");
                }
                $dados['cont'] = count($sql);
                $dados['turma'] = $sql;
                $dados['sql'] = $this->matriculaespera->listar();
                $this->view($dados);
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
                    $array_turmas[$cont] =  $this->turma->listarPorSerieAtivos($ser['id_serie']);
                    foreach ($array_turmas[$cont] as $key){
                        $turmas[] = $key;
                    }
                }

                $sql = $this->matriculaespera->listar();
                $dados['turma'] = $turmas;
                $dados['sql'] = $sql;
                $this->view($dados);
            }
            
        }  else {
            $this->_msg->informacao("Por favor, selecione o ano base em que deseja trabalhar!");
            $this->_redirect->goToControllerAction('matriculaespera', 'selecioneano');
        }
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $this->matriculaespera->transacao();
            try {
                $visitante = $this->visitante->validar();
                if($visitante){
                    throw new Exception("Falha ao salvar visitante!");
                } else {
                    $this->visitante->salvar();
                    $_POST['id_visitante'] = $this->visitante->pegaUltimoId();
                }
                
                $aluno = $this->aluno->validar();
                if($aluno){
                    throw new Exception("Falha ao salvar Aluno!");
                }  else {
                    $this->aluno->salvar();
                    $_POST['id_m_aluno'] = $this->aluno->pegaUltimoId();

                    $validar = $this->matriculaespera->validar();
                    if($validar){
                        throw new Exception("Falha ao salvar na lista de espera");
                    } else {
                        $this->matriculaespera->salvar();
                    }
                }
                $this->matriculaespera->save();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToControllerAction("matriculaespera", 'index');
            } catch (Exception $ex) {
                $this->matriculaespera->refazer();
                $this->_msg->erro($ex->getMessage());
                $this->_redirect->goToControllerAction("matriculaespera", 'index');
            }
        } else {
            $this->_redirect->goToControllerAction("matriculaespera", 'index');
        }
    }
    
    public function troca(){
        if(!empty($_POST)){
            $this->matriculaespera->transacao();
            try {
                $visitante = $this->visitante->validar();
                if($visitante){
                    throw new Exception("Falha ao salvar visitante!");
                } else {
                    $this->visitante->salvar();
                    $_POST['id_visitante'] = $this->visitante->pegaUltimoId();
                    $validar = $this->matriculaespera->validar();
                    if($validar){
                        throw new Exception("Falha ao salvar na lista de espera");
                    } else {
                        $this->matriculaespera->salvar();
                    }
                }
                $this->matriculaespera->save();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToControllerAction("matriculaespera", 'index');
                
            } catch (Exception $ex) {
                $this->matriculaespera->refazer();
                $this->_msg->erro($ex->getMessage());
                $this->_redirect->goToControllerAction("matriculaespera", 'index');
            }
        } else {
            $this->_redirect->goToControllerAction("matriculaespera", 'index');
        }
    }

    public function turma(){
        if (!$this->_session->checkSession("turma")){
            $id = $this->getParam("id");
            if($id){
                $anoselecionado = $_SESSION['anobase'];
                $txt_turma = new TurmaModel($id);
                $txt_ano = new AnoModel($anoselecionado);
                $this->addNavegacao(array(
                "Lista de espera $txt_ano->descricao" => "matriculaespera/index",
                "Troca - Turma $txt_turma->descricao" => "matriculaespera/turma/id/$id",
                ));
            
                $this->_session->createSession("turma", $id);
                $sql = $this->matriculaespera->listarTurmaStatus($id, "A");
                $dados['sql'] = $sql;
                $dados['id'] = $id;
                $this->view($dados);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("matriculaespera");
            }
        } else {
            $id = $this->getParam("id");
            $txt_turma = new TurmaModel($id);
            $anoselecionado = $_SESSION['anobase'];
            $txt_ano = new AnoModel($anoselecionado);
            $this->addNavegacao(array(
                "Lista de espera $txt_ano->descricao" => "matriculaespera/index",
                "Troca - Turma $txt_turma->descricao" => "matriculaespera/turma/id/$id"));
            $sql = $this->matriculaespera->listarTurmaStatus($id, "A");
            $dados['sql'] = $sql;
            $dados['id'] = $id;
            $this->view($dados);
        }
    }
    
    public function visitante(){
        $agenda = new AgendaModel();
         
        if (!$this->_session->checkSession("turma")){
            $id = $this->getParam("id");
           
            if($id){
                $anoselecionado = $_SESSION['anobase'];
                $txt_turma = new TurmaModel($id);
                $txt_ano = new AnoModel($anoselecionado);
                $serie = new SerieModel($txt_turma->id_serie);
                $this->addNavegacao(array(
                    "Lista de espera $txt_ano->descricao" => "matriculaespera/index", 
                    "Visitante - Turma $txt_turma->descricao" => "matriculaespera/turma/id/$id"
                ));
            
                $this->_session->createSession("turma", $id);
                $sql = $this->matriculaespera->listarPorTurma($id);
                $dados['agenda'] = $agenda->listar();
                $dados['id'] = $id;
                $dados['sql'] = $sql;
                $dados['serie'] = $serie;
                $this->view($dados);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("matriculaespera");
            }
        } else {
            $id = $this->getParam("id");
            $this->_session->createSession("turma", $id);
            $txt_turma = new TurmaModel($id);
            $anoselecionado = $_SESSION['anobase'];
            $txt_ano = new AnoModel($anoselecionado);
            $serie = new SerieModel($txt_turma->id_serie);
            $this->addNavegacao(array(
                "Lista de espera $txt_ano->descricao" => "matriculaespera/index", 
                "Visitante - Turma $txt_turma->descricao" => "matriculaespera/turma/id/$id"
            ));
            $sql = $this->matriculaespera->listarPorTurma($id);
            $dados['sql'] = $sql;
            $dados['agenda'] = $agenda->listar();
            $dados['id'] = $id;
            $dados['serie'] = $serie;
            $this->view($dados);
        }
    }
    
    public function editartroca(){
        $id = $this->getParam('id');
        if($id){
            $ida = $_SESSION['aluno'];
            $anoselecionado = $_SESSION['anobase'];
                $txt_turma = new TurmaModel($id);
                $txt_ano = new AnoModel($anoselecionado);
                $this->addNavegacao(array(
                    "Lista de espera $txt_ano->descricao" => "matriculaespera/index",
                    "Troca - Turma $txt_turma->descricao" => "matriculaespera/turma/id/$id",
                    "Editar" => "matriculaespera/editartroca/$id"
                ));
            $sql = $this->matriculaespera->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("matriculaespera");
            }
            if(!empty($_POST)){
                $this->matriculaespera->transacao();
            try {
                
                $aluno = $this->aluno->validar();
                if($aluno){
                    throw new Exception("Falha ao alterar Aluno!");
                } else {
                    $this->aluno->alterar();
                }
                
                $visitante = $this->visitante->validar();
                if($visitante){
                    throw new Exception("Falha ao alterar visitante!");
                } else {
                    $this->visitante->alterar();
                }
                
                $validar = $this->matriculaespera->validar();
                if($validar){
                    throw new Exception("Falha ao alterar na lista de espera");
                } else {
                    $this->matriculaespera->alterar();
                    $this->matriculaespera->save();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToUrl("matriculaespera/editartroca/id/$id");
                }
            } catch (Exception $ex) {
                $this->matriculaespera->refazer();
                $this->_msg->erro($ex->getMessage());
                $this->_redirect->goToUrl("matriculaespera/editartroca/id/$id");
            }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("taxas");
        }
    }
    
    public function editarvisitante(){
        $id = $this->getParam('id');
        if($id){
            $ida = $_SESSION['aluno'];
            $anoselecionado = $_SESSION['anobase'];
                $txt_turma = new TurmaModel($id);
                $txt_ano = new AnoModel($anoselecionado);
                $this->addNavegacao(array(
                    "Lista de espera $txt_ano->descricao" => "matriculaespera/index",
                    "Visitante - Turma $txt_turma->descricao" => "matriculaespera/visitante/id/$id",
                    "Editar" => "matriculaespera/editarvisitante/$id"
                ));
            $sql = $this->matriculaespera->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("matriculaespera");
            }
            if(!empty($_POST)){
                $this->matriculaespera->transacao();
            try {
                
                $aluno = $this->aluno->validar();
                if($aluno){
                    throw new Exception("Falha ao alterar Aluno!");
                } else {
                    $this->aluno->alterar();
                }
                
                $visitante = $this->visitante->validar();
                if($visitante){
                    throw new Exception("Falha ao alterar visitante!");
                } else {
                    $this->visitante->alterar();
                }
                
                $validar = $this->matriculaespera->validar();
                if($validar){
                    throw new Exception("Falha ao alterar na lista de espera");
                } else {
                    $this->matriculaespera->alterar();
                    $this->matriculaespera->save();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToUrl("matriculaespera/editarvisitante/id/$id");
                }
            } catch (Exception $ex) {
                $this->matriculaespera->refazer();
                $this->_msg->erro($ex->getMessage());
                $this->_redirect->goToUrl("matriculaespera/editarvisitante/id/$id");
            }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("matriculaespera");
        }
    }
    
    public function selecioneano(){
        $ano = $this->getParam("ano");
        if($ano){
            $txt_ano = new AnoModel($ano);
            $this->_session->createSession("anobase", $ano);
            $this->_msg->sucesso("Ano <strong>$txt_ano->descricao</strong> foi selecionado!");
            $this->_redirect->goToController('matriculaespera');
        }  else {
            $this->_session->deletarSession('anobase');
            $ano = new AnoModel();
            $dados['ano'] = $ano->listar();
            $this->view($dados);
        }
    }
    
    public function buscarturma(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->matricula->listarPorTurmaAtivos($id);
            ?>
            <select class="form-control" name="id_matricula" id="list_aluno">
                <option value="">Selecione</option>
              <?php foreach($sql as $trm){
                $aluno = new MalunoModel($trm['id_m_aluno']);
                echo "<option value='{$trm['id_matricula']}'>{$aluno->nome}</option>";
              }
            ?>
            </select>
            <?php
        }
        
    }
    
    public function excluirespera() {
        $id = $this->getParam('id');
        $ida = $_SESSION['turma'];
        if($id){
            $this->matriculaespera->transacao();
            try {
                $espera = $this->matriculaespera->buscar($id);
                if($espera){
                    $ag = $this->agenda->listarPorMatricula($espera[0]['id_matricula_espera']);
                    
                    foreach ($ag as $agendamento){
                        $ex_agenda = $this->agenda->excluir($agendamento['id_agenda']);
                    }
                    $me = $this->matriculaespera->excluir($espera[0]['id_matricula_espera']);
                    if(!$me){
                        throw new Exception("Falha ao excluir Espera!");
                    }
                    $vi = $this->visitante->excluir($espera[0]['id_visitante']);
                    if(!$vi){
                        throw new Exception("Falha ao excluir Visitante!");
                    }
                    if(!$this->matricula->listarPorAluno($id)){
                        $this->aluno->excluir($id);
                        $this->matriculaespera->save();
                        $this->_msg->sucesso("Excluido com sucesso!");
                        $this->_redirect->goToUrl("matriculaespera/visitante/id/$ida");
                    }  else {
                        $this->matriculaespera->save();
                        $this->_msg->sucesso("Excluido com sucesso!");
                        $this->_redirect->goToUrl("matriculaespera/turma/id/$ida");
                    }
                }  else {
                    throw new Exception("Falha ao excluir!");
                }
                
            } catch (Exception $ex) {
                $this->matricula->refazer();
                $this->_msg->erro($ex->getMessage());
            }
        }   
    }
    
    public function transferir(){
        $id = $this->getParam('id');
        $ida = $_SESSION['turma'];
        if($id){
            $turma = new TurmaModel($ida);
            $lista = $this->matricula->listarPorTurmaAtivos($ida);
            if (count($lista) == $turma->vagas){
                $this->_msg->erro("Não ha vagas disponíveis, confira a lista de turmas!");
                $this->_redirect->goToControllerAction("matriculaespera", 'index');
                die();
            }
            $checkMatri = $this->matricula->listarPorAluno($id);
            $checkEsp = $this->matriculaespera->listarPorAluno($id);
            if($checkMatri){
                $this->matricula->transacao();
                try {
                    $_POST['id_matricula'] = $checkMatri[0]['id_matricula'];
                    $_POST['id_m_aluno'] = $checkMatri[0]['id_m_aluno'];
                    $_POST['id_turma'] = $ida;
                    $_POST['id_matricula_status'] = $checkMatri[0]['id_matricula_status'];
                    $_POST['situacao'] = "A";
                    $_POST['id_usuario'] = $checkMatri[0]['id_usuario'];
                    $_POST['data'] = $checkMatri[0]['data_cadastro'];
                    
                    if($this->matricula->validar()){
                        throw new Exception("Falha ao transferir");
                    }  else {
                        $alter = $this->matricula->alterar();
                        if(!$alter){
                            throw new Exception("Falha ao transferir");
                        } else {
                            $this->matriculaespera->atualizaUnico(array("status" => 'T'),"id_matricula_espera=".$checkEsp[0]['id_matricula_espera']);
                            $this->_msg->sucesso("Operação Realizada com Sucesso!");
                            $this->matricula->save();
                            $this->_redirect->goToUrl("matriculaespera/turma/id/$ida");
                        }
                    }
                } catch (Exception $ex) {
                    $this->matricula->refazer();
                    $this->_msg->erro($ex->getMessage());
                    $this->_redirect->goToUrl("matriculaespera/turma/id/$ida");
                }
            } else {
                date_default_timezone_set('America/Belem');
                $date = date('Y-m-d H:i');
                $this->matricula->transacao();
                try {
                    $_POST['id_m_aluno'] = $checkEsp[0]['id_m_aluno'];
                    $_POST['id_turma'] = $checkEsp[0]['id_turma'];
                    $_POST['id_matricula_status'] = "3";
                    $_POST['situacao'] = "A";
                    $_POST['id_usuario'] = $_SESSION["userData"]["id_usuario"];
                    $_POST['data'] = $date;
                    $vm = $this->matricula->validar();
                    if($vm){
                        throw new Exception("Falha ao transferir");
                    } else {
                        $insert = $this->matricula->salvar();
                        if(!$insert){
                            throw new Exception("Falha ao transferir");
                        } else {
                            $this->matriculaespera->atualizaUnico(array("status" => 'T'),"id_matricula_espera=".$checkEsp[0]['id_matricula_espera']);
                            $this->_msg->sucesso("Operação Realizada com Sucesso!");
                            $this->matricula->save();
                            $this->_redirect->goToUrl("matriculaespera/visitante/id/$ida");
                        }
                    }
                } catch (Exception $ex) {
                    $this->matricula->refazer();
                    $this->_msg->erro($ex->getMessage());
                    $this->_redirect->goToUrl("matriculaespera/visitante/id/$ida");
                }
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
             $this->_redirect->goToControllerAction("matriculaespera", 'index');
        }
    }
    
    public function fichaespera(){
        $id = $this->getParam("id");
        $mat = new MatriculaesperaModel($id);
        $visitante = new VisitanteModel($mat->id_visitante);
        $maluno = new MalunoModel($mat->id_m_aluno);
        $turma = new TurmaModel($mat->id_turma);
        $serie = new SerieModel($turma->id_serie);
        date_default_timezone_set('America/Belem');
        $datetime = new DateTime();
        $data = $datetime->format('d/m/Y');
        $horario = $datetime->format('H:i:s');
        $saida = 
        '<html>
            <head>
                <title>Protesto</title>
            </head>
            <style type="text/css">
                table tr td {
                    padding:4px;
                    font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;
                    font-size: 1.2em;
                    font-style: normal;
                    font-variant: normal;
                    font-weight: 400;
                    line-height: 30px;
                }
                p {
                    font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;
                    font-size: 1.2em;
                    font-style: normal;
                    font-variant: normal;
                    font-weight: 400;
                    line-height: 30px;
                }
            </style>
            <body>
            <img width="140" src="/sistema/public/img/doc_logo.png" alt="" />
            <br />
            <br />
            <br />
            <table cellspacing="0" width="90%" border="1" style="margin:auto">
                <thead>
                    <tr>
                        <td colspan="4" align="center">PROCESSO DE MATRÍCULA <br /> - Ficha de Espera-</td>
                    </tr>
                </thead>
            </table>
            <br />
            <br />
            <br />
            <table cellspacing="0" width="90%" border="0" style="margin:auto">
            <tbody>
                <tr>
                    <td colspan="4"><strong>Responsável: </strong>'.UtilHelper::maiuscula($visitante->nome).'</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Aluno: </strong>'.UtilHelper::maiuscula($maluno->nome).'</td>
                </tr>
                <tr>
                    <td><strong>Série: </strong> '.$serie->descricao.'</td>
                    <td><strong>Turno: </strong> '.$turma->turno.'</td>
                    <td colspan="2" ><strong>Turma: </strong> '.$turma->descricao.'</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Email: </strong>'. $visitante->email.'</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Telefone: </strong>'. UtilHelper::formataTelefone($visitante->telefone).'</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Data: </strong> '.$data.'</td>
                    <td colspan="2"><strong>Horário: </strong> '.$horario.'</td>
                </tr>
            </tbody>
            </table>
            <br />
                <br />
                <br />
            <table width="90%" border="0" style="margin:auto">
                <tr>
                    <td align="justify" colspan="4">
                    <p>Senhor (a)</p>
                    <br />
                    <p>
                        Agradecemos sua visita em nossa Instituição de Ensino e seu interesse em matricular seu (sua) filho (a) em nossa Escola.
                        Entraremos em contato novamente, por e-mail, para maiores informações de vagas e período de matrícula que iniciará a partir do dia __/__/____.
                        Porém, conforme a demanda da Escola podemos entrar em contato antes do período previsto com o senhor (a). 
                    </p>
                    <br />
                    <br />
                    <p>
                        Núcleo de Matrícula
                    </p>
                    </td>
                </tr>
            </table>
            </body>
        </html>
        ';

        $arquivo = "ficha_de_espera.pdf";
        $mpdf = new mPDF();
        $mpdf->WriteHTML($saida);
        /*
         * F - salva o arquivo NO SERVIDOR
         * I - abre no navegador E NÃO SALVA
         * D - chama o prompt E SALVA NO CLIENTE
         */

        $mpdf->Output($arquivo, 'I');
        $this->_redirect->goToAction('index');
        $this->view();
    }
    
    public function relatoriovisitante(){
        $id = $this->getParam("id");
        $txt_turma = new TurmaModel($id);
        $serie = new SerieModel($txt_turma->id_serie);
        $view_sql = $this->matriculaespera->listarPorTurma($id);
        ob_start()
        ?>
        <html>
            <head>
                <title>Protesto</title>
                <link rel="stylesheet" href="/sistema/public/css/bootstrap.min.css">
                <link rel="stylesheet" href="/sistema/public/css/font-awesome.min.css">
                <style type="text/css">
                table.tabela1 {
                    border-top:1px solid #B7CAE6;
                    border-collapse: collapse;
                }
                table tr td{
                    padding: 2px;
                    font-size: 12px;
                    font-family: "Times New Roman", Times, serif;
                    border-bottom:1px solid #B7CAE6;
                }
                table.tabela1 tbody tr:nth-child(odd){
                    background-color: #DEEBF7;
                }
                .label{
                    border: none;
                }
            </style>
            </head>
            <body>
                <table class="tabela1" width="100%">
                    <tr>
                        <td colspan="4">
                            <img width="140" src="/sistema/public/img/doc_logo.png" alt=""/> 
                        </td>
                        <td colspan="7">
                            <span style="font-size: 20px; font-weight: bold"> Relatório de Visitante</span>
                        </td>
                    </tr>
                </table>
                <table class="tabela1" width="100%">
                    <thead>
                        <tr>
                            <th><i class="fa fa-arrows-v"></i> ID</th>
                            <th>Visitante</th>
                            <th>Nome do aluno</th>
                            <th>Usuário</th>
                            <th>Data e hora</th>
                            <th>Agendamentos</th>
                            <th style="text-align: center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php                                      
                    $cont = 0;
                    foreach ($view_sql as $sql){ 
                        // Filtra todas as matriculas em espera
                        // em que o status for diferente de "T";
                        if($sql['status'] != 'T'){
                            $status_nap = "";
                            $status_vp = "";
                            $status_ps = "";
                            $status = '<span class="label label-info"> Aguardando</span>';
                            $cont++;
                            $visi = new VisitanteModel($sql['id_visitante']);
                            // Filtra todas os visitantes
                            // em que o troca for diferente de "S";
                            if($visi->troca != "S"){
                                $agenda = new AgendaModel();
                                $checa_agenda_nap = $agenda->listarPorTipoeMatricula($sql['id_matricula_espera'], 'NAP');
                                $checa_agenda_vp = $agenda->listarPorTipoeMatricula($sql['id_matricula_espera'], 'VP');
                                $checa_agenda_ps = $agenda->listarPorTipoeMatricula($sql['id_matricula_espera'], 'PS');
                                
                                $data = new DateTime($checa_agenda_nap[0]['data_agendamento']);
                                $da = $data->format('d/m/Y');
                                $m_aluno = new MalunoModel($sql['id_m_aluno']);
                                $date = new DateTime($sql['data_cadastro']);
                                $usu = new UsuarioModel($sql['id_usuario']);
                                $pes = new PessoaModel($usu->id_pessoa);
                                $mts = new MatriculastatusModel($sql['id_matricula_status']);
                               
                                if($sql['status'] == "A" && count($checa_agenda_vp) == 0 && count($checa_agenda_nap) == 0){
                                    $status = '<span class="label label-info"> Aguardando</span>';
                                }
                                
                                if($checa_agenda_vp[0] && $checa_agenda_vp[0]['aprovado'] == "S"){
                                    $status_vp = '<span class="label label-success">VP <img width="8px" style="margin-right: 4px" src="/sistema/public/img/like.png" alt="" /></span>';
                                } elseif ($checa_agenda_vp[0] && $checa_agenda_vp[0]['aprovado'] == "N") {
                                    $status_vp = '<span class="label label-danger">VP <img width="8px" style="margin-right: 4px" src="/sistema/public/img/deslike.png" alt="" /></span>';
                                } elseif($checa_agenda_vp[0] && $checa_agenda_vp[0]['aprovado'] == null) {
                                    $status_vp = '<span class="label label-primary"> VP <img width="8px" style="margin-right: 4px" src="/sistema/public/img/clock.png" alt="" /></span>';
                                }
                                
                                if($checa_agenda_nap[0] && $checa_agenda_nap[0]['aprovado'] == "S"){
                                    $status_nap = '<span class="label label-success"> NAP <img width="8px" style="margin-right: 4px" src="/sistema/public/img/like.png" alt="" /></span>';
                                } elseif ($checa_agenda_nap[0] && $checa_agenda_nap[0]['aprovado'] == "N") {
                                    $status_nap = '<span class="label label-danger">NAP <img width="8px" style="margin-right: 4px" src="/sistema/public/img/deslike.png" alt="" /></span>';
                                } elseif($checa_agenda_nap[0] &&  $checa_agenda_nap[0]['aprovado'] == null) {
                                    $status_nap = '<span class="label label-primary">NAP <img width="8px" style="margin-right: 4px" src="/sistema/public/img/clock.png" alt="" /></span>';
                                }
                                
                                if($checa_agenda_ps[0] && $checa_agenda_ps[0]['aprovado'] == "S"){
                                    $status_ps = '<span class="label label-success"> PS <img width="8px" style="margin-right: 4px" src="/sistema/public/img/like.png" alt="" /></span>';
                                } elseif ($checa_agenda_ps[0] && $checa_agenda_ps[0]['aprovado'] == "N") {
                                    $status_ps = '<span class="label label-danger">PS <img width="8px" style="margin-right: 4px" src="/sistema/public/img/deslike.png" alt="" /></span>';
                                } elseif($checa_agenda_ps[0] &&  $checa_agenda_ps[0]['aprovado'] == null) {
                                    $status_ps = '<span class="label label-primary">PS <img width="8px"  style="margin-right: 4px" src="/sistema/public/img/clock.png" alt="" /></span>';
                                }
                                                                
                ?>
                    <tr>
                        <td><?php echo $cont; ?></td>
                        <td><?php echo $visi->nome; ?></td>
                        <td><?php echo $m_aluno->nome; ?></td>
                        <td><?php echo $pes->nome; ?></td>
                        <td><?php echo $date->format('d/m/Y H:i:s'); ?></td>
                        <td>
                            <span>
                            <?php echo $status_nap; ?> 
                            <?php echo $status_vp; ?>
                            <?php echo $status_ps; ?> 
                            </span>
                        </td>
                        <td  align="center">
                            <?php echo $status; ?> 
                        </td>
                    </tr>
                        <?php }}} ?>
                    </tbody>
                </table>
            </body>
        </html>
        <?php
        $saida = ob_get_contents();
        ob_end_clean();
        $arquivo = "ficha_de_espera.pdf";
        $mpdf = new mPDF('UTF-8', 'A4-L');
        $mpdf->WriteHTML($saida);
        /*
         * F - salva o arquivo NO SERVIDOR
         * I - abre no navegador E NÃO SALVA
         * D - chama o prompt E SALVA NO CLIENTE
         */

        $mpdf->Output($arquivo, 'I');
        $this->_redirect->goToAction('index');
        $this->view();
    }
    
    public function cancelarespera(){
        $id = $this->getParam('id');
        if($id){
            $checkEsp = $this->matriculaespera->buscar($id);
            
            if($checkEsp){
                $this->matriculaespera->atualizaUnico(array("status" => 'D'),"id_matricula_espera=".$checkEsp[0]['id_matricula_espera']);
                $this->_msg->sucesso("Operação Realizada com Sucesso!");
                $this->_redirect->goToUrl("matriculaespera/visitante/id/{$checkEsp[0]['id_turma']}");
            }
        }        
    }
    
    public function desfazercancelamento(){
        $id = $this->getParam('id');
        if($id){
            $checkEsp = $this->matriculaespera->buscar($id);
            if($checkEsp){
                $this->matriculaespera->atualizaUnico(array("status" => 'A'),"id_matricula_espera=".$checkEsp[0]['id_matricula_espera']);
                $this->_msg->sucesso("Operação Realizada com Sucesso!");
                $this->_redirect->goToUrl("matriculaespera/visitante/id/{$checkEsp[0]['id_turma']}");
            }
        }        
    }
}

