<?php

class Matricula extends Controller {

    private $matricula;
    private $aluno;
    private $turma;
    private $serie;
    private $status;
    private $_redirect;
    private $_msg;
    private $_session;

    public function init() {
        $this->matricula = new MatriculaModel();
        $this->turma = new TurmaModel();
        $this->aluno = new MalunoModel();
        $this->serie = new SerieModel();
        $this->status = new MatriculastatusModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_session = new SessionHelper();
    }

    public function index_action() {
        if($this->_session->checkSession("anobase")){
            $anoselecionado = $_SESSION['anobase'];
            $txt_ano = new AnoModel($anoselecionado);
            $this->addNavegacao(array("Matrícula $txt_ano->descricao" => "matricula/index"));
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

                $sql = $this->matricula->pesquisar($busca);

                if(!$sql){
                    $this->_msg->informacao("Nada Encontrado!");
                }
                $dados['cont'] = count($sql);
                $dados['turma'] = $sql;
                $dados['sql'] = $this->matricula->listar();
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

                $sql = $this->matricula->listar();
                $dados['turma'] = $turmas;
                $dados['sql'] = $sql;
                $this->view($dados);
            }
            
        }  else {
            $this->_msg->informacao("Por favor, selecione o ano base em que deseja trabalhar!");
            $this->_redirect->goToControllerAction('matricula', 'selecioneano');
        }
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $this->matricula->transacao();
            try {
                $turma = new TurmaModel($_POST['id_turma']);
                $lista = $this->matricula->listarPorTurmaAtivos($_POST['id_turma']);
                if (count($lista) == $turma->vagas){
                    throw new Exception("Não ha vagas disponíveis, confira a lista de turmas!");
                }
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
                $this->_msg->erro($ex->getMessage());
                $this->_redirect->goToControllerAction("matricula", 'index');
            }
        } else {
            $this->_redirect->goToControllerAction("matricula", 'index');
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->matricula->buscar($id);
            if($sql){
                $dados['sql'] = $sql[0];
                $dados['turma'] = $this->turma->listar();
                $dados['status'] = $this->status->listar();
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("matricula");
            }
            if(!empty($_POST)){
                $validar = $this->matricula->validar();
                if($validar){
                    $this->_redirect->goToUrl("matricula/editar/id/$id");
                } else {
                    $this->matricula->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToUrl("matricula/editar/id/$id");
                }
            } else {
              $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("matricula");
        }
    }
    
    public function visualizar() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->matricula->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
                 $this->view($datas);
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("matricula");
            }
            
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("matricula");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->matricula->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("matricula");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("matricula");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("matricula");
        }
    }
    
    public function selecioneano(){
        $ano = $this->getParam("ano");
        if($ano){
            $txt_ano = new AnoModel($ano);
            $this->_session->createSession("anobase", $ano);
            $this->_msg->sucesso("Ano <strong>$txt_ano->descricao</strong> foi selecionado!");
            $this->_redirect->goToController('matricula');
        }  else {
            $this->_session->deletarSession('anobase');
            $ano = new AnoModel();
            $dados['ano'] = $ano->listar();
            $this->view($dados);
        }
    }
    
    public function matricular(){
        if(!empty($_POST)){
            date_default_timezone_set('America/Belem');
            $date = date('Y-m-d H:i');
            if(isset($_POST['matricular']) && $_POST['id_matricula']){
                $matri = $this->matricula->buscar($_POST['id_matricula']);
                $_POST['id_m_aluno'] = $matri[0]['id_m_aluno'];
                $_POST['id_turma'] = $matri[0]['id_turma'];
                $_POST['id_matricula_status'] = "1";
                $_POST['situacao'] = "A";
                $_POST['id_usuario'] = $_SESSION["userData"]["id_usuario"];
                $_POST['data'] = $date;
                if($_POST['matricula']){
                    $alu = $this->aluno->validar();
                    if($alu){
                        $this->_redirect->goToControllerAction("matricula", 'index');
                    }  else {
                        $this->aluno->alterar();
                    }
                }
                $validar = $this->matricula->validar();
                if($validar){
                    $this->_redirect->goToController('matricula');
                } else {
                    $this->matricula->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                }
            }
            
            if(isset($_POST['rematricular']) && $_POST['id_matricula']){
                $matri = $this->matricula->buscar($_POST['id_matricula']);
                $_POST['id_m_aluno'] = $matri[0]['id_m_aluno'];
                $_POST['id_turma'] = $matri[0]['id_turma'];
                $_POST['id_matricula_status'] = "2";
                $_POST['situacao'] = "A";
                $_POST['id_usuario'] = $_SESSION["userData"]["id_usuario"];
                $_POST['data'] = $date;
                if($_POST['matricula']){
                    $alu = $this->aluno->validar();
                    if($alu){
                        $this->_redirect->goToControllerAction("matricula", 'index');
                    }  else {
                        $this->aluno->alterar();
                    }
                }
                $validar = $this->matricula->validar();
                if($validar){
                    $this->_redirect->goToController('matricula');
                } else {
                    $this->matricula->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                }
            } 
            $this->_redirect->goToController('matricula');
        }  else {
            $this->_redirect->goToController("matricula");
        }
    }
    
    public function buscarmatricula(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->matricula->buscar($id);
            
            ?>

        <input type="hidden" name="id_matricula" value="<?php echo $id; ?>" />
        <input type="hidden" name="id_m_aluno" value="<?php echo $sql[0]['id_m_aluno']; ?>" />
        <input type="hidden" name="id_usuario" value="<?php echo $sql[0]['id_usuario']; ?>" />
        <input type="hidden" name="id_matricula_status" value="<?php echo $sql[0]['id_matricula_status']; ?>"/>
        <input type="hidden" name="data" value="<?php echo $sql[0]['data_cadastro']; ?>" />
            
            <?php
        }
        
    }
    
    public function editartroca(){
        $id = $_POST['id_matricula'];
        if($id){
            $sql = $this->matricula->buscar($id);
            if($sql){
                $dados['sql'] = $sql[0];
                $dados['turma'] = $this->turma->listar();
                $dados['status'] = $this->status->listar();
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("matricula");
            }
            if(!empty($_POST)){
                $_POST['situacao'] = "A";
                $validar = $this->matricula->validar();
                if($validar){
                    $this->_redirect->goToUrl("matricula");
                } else {
                    $this->matricula->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToUrl("matricula");
                }
            } else {
              $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("matricula");
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
                        $this->_redirect->goToController("matricula");
                    }
                    $this->matricula->save();
                } catch (Exception $ex) {
                    $this->matricula->refazer();
                    $this->_msg->erro($ex->getMessage());
                    $this->_redirect->goToController("matricula");
                }
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("matricula", 'index');
        }
    }
    
}

