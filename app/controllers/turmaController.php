<?php

class Turma extends Controller {

    private $turma;
    private $aluno;
    private $serie;
    private $ano;
    private $matricula;
    private $matriculaespera;
    private $_redirect;
    private $_msg;
    private $_session;

    public function init() {
        $this->aluno = new MalunoModel();
        $this->turma = new TurmaModel();
        $this->serie = new SerieModel();
        $this->ano = new AnoModel();
        $this->matricula = new MatriculaModel();
        $this->matriculaespera = new MatriculaesperaModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_session = new SessionHelper();
    }

    public function index_action() {
        $this->_session->deletarSession("turma");
        if($this->_session->checkSession("anobase")){
            $ano = $_SESSION['anobase'];
            $anoletivo = new AnoletivoModel();
            $anolet = $anoletivo->listarPorAno($ano);
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
            $desabilitadas = array();
            foreach ($serie as $ser){
                $cont++;
                $array_turmas[$cont] =  $this->turma->listarPorSerieAtivos($ser['id_serie']);
                $array_desabilitadas[$cont] =  $this->turma->listarPorSerieDesabilitadas($ser['id_serie']);
                foreach ($array_turmas[$cont] as $key){
                    $turmas[] = $key;
                }
                
                foreach ($array_desabilitadas[$cont] as $value){
                    $desabilitadas[] = $value;
                }
            }
            
            $datas['sql'] = $turmas;
            $datas['desabilitadas'] = $desabilitadas;
            $this->view($datas);
        }  else {
            $this->_msg->informacao("Por favor, selecione o ano base em que deseja trabalhar!");
            $this->_redirect->goToControllerAction('turma', 'selecioneano');
        }
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $_POST['situacao'] = "A";
            $validar = $this->turma->validar();
            if($validar){
                $this->_redirect->goToControllerAction("turma", 'inserir');
            } else {
                $this->turma->salvar();
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToControllerAction("turma", 'inserir');
            }
        } else {
            $dados['ano'] = $this->ano->listar();
            $dados['turno'] = UtilHelper::listaTurno();
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->turma->buscar($id);
            if($sql){
                $anoletivo = new AnoletivoModel();
                $anolet = $anoletivo->listarPorAno($_SESSION['anobase']);
                $cont = -1;
                foreach ($anolet as $val){
                    $cont++;
                   $array[$cont] = $this->serie->listarPorAnoletivo($val['id_ano_letivo']);
                   foreach ($array[$cont] as $key){
                       $serie[] = $key;
                   }
                }
                
                $dados['sql'] = $sql[0];
                $dados['ano'] = $this->ano->listar();
                $dados['serie'] = $serie;
                $dados['turno'] = UtilHelper::listaTurno();
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("turma");
            }
            if(!empty($_POST)){
                $validar = $this->turma->validar();
                if($validar){
                    $this->_redirect->goToUrl("turma/editar/id/$id");
                } else {
                    $this->turma->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToUrl("turma/editar/id/$id");
                }
            } else {
              $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("turma");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->turma->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
                 $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("turma");
            }
            
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("turma");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->turma->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("turma");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("turma");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("turma");
        }
    }
    
    public function aluno(){
        if (!$this->_session->checkSession("turma")){
            $id = $this->getParam("id");
            if($id){
                $this->_session->createSession("turma", $id);
                $sql = $this->matricula->listarPorTurmaAtivos($id);
                $dados['sql'] = $sql;
                $dados['id'] = $id;
                $this->view($dados);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("turma");
            }
        } else {
            $id = $this->getParam("id");
            $sql = $this->matricula->listarPorTurmaAtivos($id);
            $dados['sql'] = $sql;
            $dados['id'] = $id;
            $this->view($dados);
        }
    }
    
    public function inseriraluno(){
        if ($this->_session->checkSession("anoletivo")){
            if(!empty($_POST)){
            $this->matricula->transacao();
                try {
                    $aluno = $this->aluno->validar();
                    if($aluno){
                        throw new Exception("Falha ao salvar Aluno");
                    }  else {
                        $this->aluno->salvar();
                        $_POST['id_m_aluno'] = $this->aluno->pegaUltimoId();

                        $validar = $this->matricula->validar();
                        if($validar){
                            throw new Exception("Falha ao salvar matrícula");
                        } else {
                            $this->matricula->salvar();
                        }
                    }
                    $this->matricula->save();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToControllerAction("matricula", 'index');
                } catch (Exception $ex) {
                    $this->matricula->refazer();
                    $this->_msg->erro($ex);
                    $this->_redirect->goToControllerAction("matricula", 'index');
                }
            } else {
                $this->_redirect->goToControllerAction("matricula", 'index');
            }
        } else {
            $this->_redirect->goToController("anoletivo");
        }
    }
    
    public function editaraluno(){
        $id = $this->getParam('id');
        if($id){
            $ida = $_SESSION['aluno'];
            $this->addNavegacao(array(
                'Turma' => "turma/index", 
                'Alunos' => "turma/aluno/id/".$ida, 
                'Editar Aluno' => "turma/editaraluno/id/$id",
                ));
            $sql = $this->matricula->buscar($id);
            $status = new MatriculastatusModel();
            if($sql){
                 $datas['sql'] = $sql[0];
                 $datas['status'] = $status->listar();
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("turma");
            }
            if(!empty($_POST)){
                $this->matricula->transacao();
                try {
                    $aluno = $this->aluno->validar();
                    if($aluno){
                        throw new Exception("Falha ao salvar Aluno");
                    }  else {
                        $this->aluno->alterar();
                        $validar = $this->matricula->validar();
                        if($validar){
                            throw new Exception("Falha ao salvar matrícula");
                        } else {
                            $this->matricula->alterar();
                        }
                    }
                    $this->matricula->save();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToUrl("turma/editaraluno/id/$id");
                } catch (Exception $ex) {
                    var_dump($ex->getMessage());die();
                    $this->matricula->refazer();
                    $this->_msg->erro($ex);
                    $this->_redirect->goToUrl("turma/editaraluno/id/$id");
                }
                
            }  else {
              $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("taxas");
        }
    }
    
    public function visualizaraluno(){
        $id = $this->getParam('id');
        if($id){
            $ida = $_SESSION['aluno'];
            $this->addNavegacao(array(
                'Turma' => "turma/index", 
                'Alunos' => "turma/aluno/id/".$ida, 
                'Visualizar' => "turma/visualizar/aluno/id/$id",
                ));
            $sql = $this->matricula->buscar($id);
            
            $esp = $this->matriculaespera->listarPorAluno($sql[0]['id_m_aluno']);
            if($sql){
                $datas['sql'] = $sql[0];
                $datas['esp'] = $esp[0];
                $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("turma");
            }
            
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("turma");
        }
    }
    
    public function excluiraluno() {
        $id = $this->getParam('id');
        $ida = $_SESSION['turma'];
        if($id){
            $this->matricula->transacao();
            try {
                $matricula =  $this->matricula->buscar($id);
                if($matricula){
                   $delMat = $this->matricula->excluir($matricula[0]['id_matricula']);
                   if($delMat){
                       $espera = new MatriculaesperaModel();
                       $chekEsp = $espera->listarPorAluno($matricula[0]['id_m_aluno']);
                       if($chekEsp){
                           $agenda =  new AgendaModel();
                           $checkAgenda = $agenda->listarPorMatricula($chekEsp[0]['id_matricula_espera']);
                           if($checkAgenda){
                               foreach ($checkAgenda as $ag){
                                   $agenda->excluir($ag['id_agenda']);
                               }
                           }
                           $espera->excluir($chekEsp[0]['id_matricula_espera']);
                       }
                       $this->aluno->excluir($matricula[0]['id_m_aluno']);
                   } else {
                       throw new Exception("Falha ao excluir!");
                   }
                } else {
                    throw new Exception("Falha ao excluir!");
                }
                $this->matricula->save();
                $this->_msg->sucesso("Excluido com sucesso!");
                $this->_redirect->goToUrl("turma/aluno/id/$ida");
            } catch (Exception $ex) {
                $this->matricula->refazer();
                $this->_msg->erro($ex->getMessage());
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToUrl("turma/aluno/id/$ida");
        }
    }
    
    public function buscarserie(){
        $id = $this->getParam('id');
        if($id){
            $anoletivo = new AnoletivoModel();
            $anolet = $anoletivo->listarPorAno($id);
            $cont = -1;
            foreach ($anolet as $val){
                $cont++;
               $array[$cont] = $this->serie->listarPorAnoletivo($val['id_ano_letivo']);
               foreach ($array[$cont] as $key){
                   $serie[] = $key;
               }
            }
            $sql = $serie;
            
            ?>
            <label>Serie:</label>
            <select name="id_serie" id="curso">
              <?php foreach($sql as $serie){
                echo "<option value='{$serie["id_serie"]}'>{$serie["descricao"]}</option>";
              }
            ?>
            </select>
            <?php
        }
        
    }
    
     public function selecioneano(){
        $ano = $this->getParam("ano");
        if($ano){
            $txt_ano = new AnoModel($ano);
            $this->_session->createSession("anobase", $ano);
            $this->_msg->sucesso("Ano <strong>$txt_ano->descricao</strong> foi selecionado!");
            $this->_redirect->goToController('turma');
        }  else {
            $this->_session->deletarSession('anobase');
            $ano = new AnoModel();
            $dados['ano'] = $ano->listar();
            $this->view($dados);
        }
    }
    
    public function devolver(){
        $id = $this->getParam('id');
        if($id){
            $matricula = $this->matricula->buscar($id);
            if($matricula){
                $checkEsp = $this->matriculaespera->listarPorPagina("id_m_aluno = {$matricula[0]['id_m_aluno']} AND (id_turma = {$matricula[0]['id_turma']})");
                
                if($checkEsp){
                    $this->matriculaespera->transacao();
                    try {
                        $alter = $this->matriculaespera->atualizaUnico(array("status" => 'A'),"id_matricula_espera=".$checkEsp[0]['id_matricula_espera']);
                        if(!$alter){
                            throw new Exception("Falha ao transferir");
                        } else {
                            $delMat = $this->matricula->excluir($id);
                            if(!$delMat){
                                throw new Exception("Falha ao transferir");
                            }
                            $this->matriculaespera->save();
                            $this->_msg->sucesso("Operação Realizada com Sucesso!");
                            $this->_redirect->goToUrl("turma/aluno/id/{$matricula[0]['id_turma']}");

                        }
                        
                    } catch (Exception $ex) {
                        $this->matriculaespera->refazer();
                        $this->_msg->erro($ex->getMessage());
                        $this->_redirect->goToUrl("turma/aluno/id/{$matricula[0]['id_turma']}");
                    }
                }
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
             $this->_redirect->goToControllerAction("matriculaespera", 'index');
        }
    }
    
    public function situacao(){
        $id = $this->getParam('id');
        if($id){
            $matricula = $this->matricula->buscar($id);
            if($matricula){
                $this->matricula->transacao();
                try {
                    $situacao = "A";
                    if($matricula[0]['situacao'] == "A"){
                        $situacao = "D";
                    }
                    
                    $alter = $this->matricula->atualizaUnico(array("situacao" => $situacao),"id_matricula=".$matricula[0]['id_matricula']);
                    if(!$alter){
                        throw new Exception("Falha ao remover!");
                    } else {
                        $this->_msg->sucesso("Operação Realizada com Sucesso!");
                        $this->_redirect->goToUrl("turma/aluno/id/{$matricula[0]['id_turma']}");

                    }
                    $this->matricula->save();
                } catch (Exception $ex) {
                    $this->matricula->refazer();
                    $this->_msg->erro($ex->getMessage());
                    $this->_redirect->goToUrl("turma/aluno/id/{$matricula[0]['id_turma']}");
                }
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("matricula", 'index');
        }
    }
    
    public function desativar(){
        $id = $this->getParam('id');
        if($id){
            $turma = $this->turma->buscar($id);
            if($turma){
                $this->turma->transacao();
                try {
                    $situacao = "A";
                    if($turma[0]['situacao'] == "A"){
                        $situacao = "D";
                    }
                    
                    $alter = $this->turma->atualizaUnico(array("situacao" => $situacao),"id_turma=".$turma[0]['id_turma']);
                    if(!$alter){
                        throw new Exception("Falha ao Salvar!");
                    } else {
                        $this->_msg->sucesso("Operação Realizada com Sucesso!");
                        $this->_redirect->goToControllerAction("turma", 'index');

                    }
                    $this->turma->save();
                } catch (Exception $ex) {
                    $this->turma->refazer();
                    $this->_msg->erro($ex->getMessage());
                   $this->_redirect->goToControllerAction("turma", 'index');
                }
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("turma", 'index');
        }
    }
    
    public function ativar(){
        $id = $this->getParam('id');
        if($id){
            $turma = $this->turma->buscar($id);
            if($turma){
                $this->turma->transacao();
                try {
                    $situacao = "D";
                    if($turma[0]['situacao'] == "D"){
                        $situacao = "A";
                    }
                    
                    $alter = $this->turma->atualizaUnico(array("situacao" => $situacao),"id_turma=".$turma[0]['id_turma']);
                    if(!$alter){
                        throw new Exception("Falha ao Salvar!");
                    } else {
                        $this->_msg->sucesso("Operação Realizada com Sucesso!");
                        $this->_redirect->goToControllerAction("turma", 'index');

                    }
                    $this->turma->save();
                } catch (Exception $ex) {
                    $this->turma->refazer();
                    $this->_msg->erro($ex->getMessage());
                   $this->_redirect->goToControllerAction("turma", 'index');
                }
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("turma", 'index');
        }
    }
    
    public function devolucao(){
        $id = $this->getParam('id');
        if($id){
            $matricula = $this->matricula->buscar($id);
            if($matricula){
                $this->matricula->transacao();
                try {
                    $situacao = "D";
                    if($matricula[0]['situacao'] == "D"){
                        $situacao = "A";
                    }
                    
                    $alter = $this->matricula->atualizaUnico(array("situacao" => $situacao),"id_matricula=".$matricula[0]['id_matricula']);
                    if(!$alter){
                        throw new Exception("Falha ao remover!");
                    } else {
                        $this->_msg->sucesso("Operação Realizada com Sucesso!");
                        $this->_redirect->goToController("turma");
                    }
                    $this->matricula->save();
                } catch (Exception $ex) {
                    $this->matricula->refazer();
                    $this->_msg->erro($ex->getMessage());
                    $this->_redirect->goToController("turma");
                }
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("turma", 'index');
        }
    }
}

