<?php

class Colaborador extends Controller {

    private $colaborador, $colaborador_tipo, $instituicao, $curso, $contrato,$seguro,$ferias,
            $municipio, $bairro, $estado, $_redirect,$_msg, $pessoa, $pessoa_ref, $telefone,
            $telefone_tipo, $aluno, $dbancario, $endereco, $_session, $atuacao, $segurostatus,
            $segurotipo, $patrimonio;
   

    public function init() {
        $this->colaborador = new ColaboradorModel();
        $this->colaborador_tipo = new ColaboradortipoModel();
        $this->orientador = new OrientadorModel();
        $this->instituicao = new InstituicaoModel();
        $this->curso = new CursoModel();
        $this->contrato = new ContratoModel();
        $this->seguro = new SeguroModel();
        $this->segurostatus = new SegurostatusModel();
        $this->segurotipo = new SegurotipoModel();
        $this->ferias = new FeriasModel();
        $this->municipio = new MunicipioModel();
        $this->estado = new EstadoModel();
        $this->bairro = new BairroModel();
        $this->pessoa_ref = new PessoarefModel();
        $this->telefone = new TelefoneModel();
        $this->telefone_tipo = new TelefonetipoModel();
        $this->pessoa = new PessoaModel();
        $this->aluno = new AlunoModel();
        $this->dbancario = new DadosbancarioModel();
        $this->endereco = new EnderecoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_session = new SessionHelper();
        $this->atuacao = new AreaatuacaoModel();
        $this->patrimonio = new PatrimonioModel();
        
    }

    public function index_action() {
        $page = $this->getParam("page");
        if(!$page){
            $page = 1;
        }
        $this->_session->deletarSession("colaborador");
        $list =  $this->colaborador->listarPorPagina();
        //$this->mostrar($sql);
        if(!empty($_POST)){
            $caracter = array("'",'"',"-","*","_",',');
            $select = "SELECT * FROM pessoa a, colaborador b WHERE a.id_pessoa = b.id_pessoa";
            if($_POST['nome']){
                $nome = str_replace($caracter, "", $_POST['nome']);
                $busca = $select." AND a.nome LIKE '%{$nome}%'";
            } else {
                $this->_msg->informacao("Você precisar digitar um nome!");
                $this->_redirect->goToAction('index');
            }
            $sql = $this->colaborador->pesquisar($busca);
            if(count($sql) == 0){
                $this->_msg->informacao("Nenhum Colaborador Encontrado!");
            }
            $dados['sql'] = $sql;
        } else {
            $total = count($list);
            $registro = 4;
            $numero = ceil($total/$registro);
            $inicio = $page - 1;
            $inicio = $inicio * $registro;
            //$this->mostrar($inicio);die();
            $sql = $this->colaborador->listarPorPagina(null, $inicio.",".$registro);
            $dados['numero'] = $numero;
            $dados['sql'] = $sql;
        }
        
        $this->view($dados);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $up = new UploadHelper();
            $up->setFile($_FILES["foto"]);
            $up->upload();
            $foto = $up->getCaminho();
            $pessoa = $this->pessoa->validar($foto);
            $end = $this->endereco->validar();
            $aluno = $this->aluno->validar();
            $dbancario = $this->dbancario->validar();
            $colaborador = $this->colaborador->validar();
            if($pessoa || $colaborador || $aluno || $dbancario || $end){
                $this->_redirect->goToControllerAction("colaborador", 'inserir');
            } else {
                $this->pessoa->salvar();
                $this->endereco->salvar();
                $this->pessoa_ref->salvar();
                $this->dbancario->salvar();
                $this->aluno->salvar();
                $this->colaborador->salvar();
                $this->_msg->sucesso("Colaborador Salvo com Sucesso!");
                $this->_redirect->goToController("colaborador");
            }
        } else {
            $dados["ori"] = $this->orientador->listar();
            $dados["mun"] = $this->municipio->listar();
            $dados["est"] = $this->estado->listar();
            $dados["bai"] = $this->bairro->listar();
            $dados["cur"] = $this->curso->listar();
            $dados["ftp"] = $this->colaborador_tipo->listar();
            $dados["fun"] = $this->colaborador->listar();
            $dados["ins"] = $this->instituicao->listar();
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->colaborador->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("colaborador");
            }
            if(!empty($_POST)){
                $col = new ColaboradorModel($id);
                $pes = new PessoaModel($col->id_pessoa);
                if($_FILES['foto'] && ($_FILES["foto"]["error"] == 4)){
                    $foto = $pes->foto;
                } else {
                    $up = new UploadHelper();
                    $up->setFile($_FILES["foto"]);
                    $foto = $up->getCaminho();
                }
                $pessoa = $this->pessoa->validar($foto);
                $end = $this->endereco->validar();
                $colaborador = $this->colaborador->validar();
                $dbancario = $this->dbancario->validar();
                $aluno = $this->aluno->validar();
                if($pessoa || $colaborador || $end || $dbancario || $aluno){
                    $this->_redirect->goToUrl("colaborador/editar/id/$id");
                } else {
                    if($_FILES['foto'] && ($_FILES["foto"]["error"] == 4)){
                        $foto = $pes->foto;
                    } else {
                        if(file_exists($_SERVER["DOCUMENT_ROOT"]. $pes->foto)){
                            unlink($_SERVER["DOCUMENT_ROOT"].$pes->foto);
                        }
                        $up->upload();
                    }
                    $this->pessoa->alterar();
                    $this->endereco->alterar();
                    $this->dbancario->alterar();
                    $this->aluno->alterar();
                    $this->colaborador->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("colaborador");
                }
                
            }  else {
                $dados["ori"] = $this->orientador->listar();
                $dados["mun"] = $this->municipio->listar();
                $dados["est"] = $this->estado->listar();
                $dados["bai"] = $this->bairro->listar();
                $dados["cur"] = $this->curso->listar();
                $dados["ftp"] = $this->colaborador_tipo->listar();
                $dados["fun"] = $this->colaborador->listar();
                $dados["ins"] = $this->instituicao->listar();
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("colaborador");
        }
    }
    
    public function excluir() {
        $id = $this->getParam('id');
        if ($id) {
            //$sql = $this->colaborador->buscar($id);
            $colaborador = $this->colaborador->validarExcluir($id);
            //$p_ref = $this->pessoa_ref->deletePorPessoa($sql[0]['id_pessoa']);
            //$pessoa =  $this->pessoa->excluir($sql[0]['id_pessoa']);
            if($colaborador){
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("colaborador");
            } else {
                $this->colaborador->excluir($id);
                $this->_msg->sucesso("Colaborador deletado com Sucesso!");
                $this->_redirect->goToController("colaborador");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("colaborador");
        }
    }
    
    public function visualizar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->colaborador->buscar($id);
            if($sql){
                $dados['sql'] = $sql[0];
                $this->view($dados); 
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("colaborador");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("colaborador");
        }
    }

    public function ferias() {
        if (!$this->_session->checkSession("colaborador")){
            $id = $this->getParam("id");
            if($id){
                $this->_session->createSession("colaborador", $id);
                $sql = $this->ferias->listarPorColaborador($id);
                $dados['sql'] = $sql;
                $this->view($dados);
            }  else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("ferias");
            }
        }  else {
            $id = $_SESSION["colaborador"];
            $sql = $this->ferias->listarPorColaborador($id);
            $dados['sql'] = $sql;
            $this->view($dados);
        }
    }
    
    public function feriasinserir(){
        if(!empty($_POST)){
            $validar = $this->ferias->validar();
            if($validar){
                $this->_redirect->goToControllerAction("colaborador", 'feriasinserir');
            } else {
                $this->ferias->salvar();
                $this->_msg->sucesso("Férias Salva com Sucesso!");
                $this->_redirect->goToControllerAction("colaborador", 'ferias');
            }
        } else {
            $this->view();
        }
    }
    
    public function feriaseditar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->ferias->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToControllerAction("colaborador", "ferias");
            }
            if(!empty($_POST)){
                $curso = $this->ferias->validar();
                if($curso){
                    $this->_redirect->goToUrl("colaborador/feriaseditar/id/$id");
                } else {
                    $this->ferias->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToControllerAction("colaborador", "ferias");
                }
                
            }  else {
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("colaborador", "ferias");
        }
    }
    
    public function feriasexcluir(){
        $id = $this->getParam('id');
        if ($id) {
            $ferias = $this->ferias->excluir($id);
            if($ferias){
                $this->_msg->sucesso("Ferias Deletada com Sucesso!");
                $this->_redirect->goToControllerAction("colaborador", "ferias");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToControllerAction("colaborador", "ferias");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("colaborador", "ferias");
        }
    }
    
    public function contrato(){
        if (!$this->_session->checkSession("colaborador")){
            $id = $this->getParam("id");
            if($id){
                $this->_session->createSession("colaborador", $id);
                $sql = $this->contrato->listarPorColaborador($id);
                $dados['sql'] = $sql;
                $this->view($dados);
            }  else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("colaborador");
            }
        }  else {
            $id = $_SESSION["colaborador"];
            $sql = $this->contrato->listarPorColaborador($id);
            $dados['sql'] = $sql;
            $this->view($dados);
        }
    }
    
    public function contratoinserir(){
        if(!empty($_POST)){
            $validar = $this->contrato->validar();
            if($validar){
                $this->_redirect->goToControllerAction("colaborador", 'contratoinserir');
            } else {
                $this->contrato->salvar();
                $this->_msg->sucesso("Contrato Salvo com Sucesso!");
                $this->_redirect->goToControllerAction("colaborador", 'contrato');
            }
        } else {
            $atc = $this->atuacao->listar();
            $dados["atc"] = $atc;
            $this->view($dados);
        }
    }
    
    public function contratoeditar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->contrato->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToControllerAction("colaborador", "contrato");
            }
            if(!empty($_POST)){
                $curso = $this->contrato->validar();
                if($curso){
                    $this->_redirect->goToUrl("colaborador/contratoeditar/id/$id");
                } else {
                    $this->contrato->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToControllerAction("colaborador", "contrato");
                }
                
            }  else {
                $atc = $this->atuacao->listar();
                $dados["atc"] = $atc;
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("colaborador", "contrato");
        }
    }
    
    public function contratoexcluir(){
        $id = $this->getParam('id');
        if ($id) {
            $ferias = $this->contrato->excluir($id);
            if($ferias){
                $this->_msg->sucesso("Contrato Deletado com Sucesso!");
                $this->_redirect->goToControllerAction("colaborador", "contrato");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToControllerAction("colaborador", "contrato");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("colaborador", "contrato");
        }
    }
    
    public function seguro(){
        if (!$this->_session->checkSession("colaborador")){
            $id = $this->getParam("id");
            if($id){
                $this->_session->createSession("colaborador", $id);
                $sql = $this->seguro->listarPorColaborador($id);
                $dados['sql'] = $sql;
                $this->view($dados);
            }  else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("colaborador");
            }
        }  else {
            $id = $_SESSION["colaborador"];
            $sql = $this->seguro->listarPorColaborador($id);
            $dados['sql'] = $sql;
            $this->view($dados);
        }
    }
    
    public function seguroinserir(){
        if(!empty($_POST)){
            $validar = $this->seguro->validar();
            if($validar){
                $this->_redirect->goToControllerAction("colaborador", 'seguroinserir');
            } else {
                $this->seguro->salvar();
                $this->_msg->sucesso("Contrato Salvo com Sucesso!");
                $this->_redirect->goToControllerAction("colaborador", 'seguro');
            }
        } else {
            $sst = $this->segurostatus->listar();
            $stp = $this->segurotipo->listar();
            $dados["sst"] = $sst;
            $dados["stp"] = $stp;
            $this->view($dados);
        }
    }
    
    public function seguroeditar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->seguro->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToControllerAction("colaborador", "seguro");
            }
            if(!empty($_POST)){
                $seguro = $this->seguro->validar();
                if($seguro){
                    $this->_redirect->goToUrl("colaborador/seguroeditar/id/$id");
                } else {
                    $this->seguro->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToControllerAction("colaborador", "seguro");
                }
                
            }  else {
                $sst = $this->  segurostatus->listar();
                $stp = $this->segurotipo->listar();
                $dados["sst"] = $sst;
                $dados["stp"] = $stp;
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("colaborador", "seguro");
        }
    }
    
    public function seguroexcluir(){
        $id = $this->getParam('id');
        if ($id) {
            $seguro = $this->seguro->excluir($id);
            if($seguro){
                $this->_msg->sucesso("Ferias Deletada com Sucesso!");
                $this->_redirect->goToControllerAction("colaborador", "ferias");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToControllerAction("colaborador", "ferias");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("colaborador", "ferias");
        }
    }
    
    public function patrimonio() {
        if (!$this->_session->checkSession("colaborador")){
            $id = $this->getParam("id");
            if($id){
                $this->_session->createSession("colaborador", $id);
                $sql = $this->patrimonio->listarPorColaborador($id);
                $dados['sql'] = $sql;
                $this->view($dados);
            }  else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("patrimonio");
            }
        }  else {
            $id = $_SESSION["colaborador"];
            $sql = $this->patrimonio->listarPorColaborador($id);
            $dados['sql'] = $sql;
            $this->view($dados);
        }
    }
    
    public function patrimonioinserir(){
        if(!empty($_POST)){
            $validar = $this->patrimonio->validar();
            if($validar){
                $this->_redirect->goToControllerAction("colaborador", 'patrimonioinserir');
            } else {
                $this->patrimonio->salvar();
                $this->_msg->sucesso("Patrimonio Salvo com Sucesso!");
                $this->_redirect->goToControllerAction("colaborador", 'patrimonio');
            }
        } else {
            $this->view();
        }
    }
    
    public function patrimonioeditar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->patrimonio->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToControllerAction("colaborador", "patrimonio");
            }
            if(!empty($_POST)){
                $curso = $this->patrimonio->validar();
                if($curso){
                    $this->_redirect->goToUrl("colaborador/patrimonioeditar/id/$id");
                } else {
                    $this->patrimonio->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToControllerAction("colaborador", "patrimonio");
                }
                
            }  else {
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("colaborador", "patrimonio");
        }
    }
    
    public function patrimonioexcluir(){
        $id = $this->getParam('id');
        if ($id) {
            $ferias = $this->patrimonio->excluir($id);
            if($ferias){
                $this->_msg->sucesso("Patrimonio Deletada com Sucesso!");
                $this->_redirect->goToControllerAction("colaborador", "patrimonio");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToControllerAction("colaborador", "patrimonio");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("colaborador", "patrimonio");
        }
    }
    
    public function buscarbairro(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->bairro->listarPorMunicipio($id);
            
            ?>
            <label>Cidades:</label>
            <select name="cidade" id="cidade">
              <?php foreach($sql as $bairro){
                echo "<option value='{$bairro["id_bairro"]}'>{$bairro["nome"]}</option>";
              }
            ?>
            </select>
            <?php
        }
        
    }
    
    public function buscarinstituicao(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->curso->listaPorInstituicao($id);
            
            ?>
            <label>Curso:</label>
            <select name="id_curso" id="curso">
              <?php foreach($sql as $curso){
                echo "<option value='{$curso["id_curso"]}'>{$curso["descricao"]}</option>";
              }
            ?>
            </select>
            <?php
        }
        
    }
    
    public function imprimir(){
        $id = $this->getParam("id");
        $sql = $this->colaborador->buscar($id);
        $pes = new PessoaModel($sql[0]['id_pessoa']);
        $txt_pai = $txt_mae = $txt_profissao = "";
        $txt_pis = $txt_cep = $txt_complemento = $data_cont_ini = $data_cont_fim = "";
        if($pes->pis){
            $txt_pis = $pes->pis;
        }
        if($pes->pai){
            $txt_pai = $pes->pai; 
        }
        if($pes->mae){
            $txt_mae = $pes->mae; 
        }
        $pessoa_ref = new PessoarefModel();
        $ref = $pessoa_ref->listarPorPessoa($sql[0]['id_pessoa']);
        $end = new EnderecoModel($ref[0]["id_endereco"]);
        
        if($end->cep){
           $txt_cep = $end->cep; 
        }
        if($end->complemento){
           $txt_complemento = $end->complemento; 
        }
        $ctp = new ColaboradortipoModel($sql[0]['id_colaborador_tipo']);
        $bai = new BairroModel($end->id_bairro);
        $mun = new MunicipioModel($bai->id_municipio);
        $est = new EstadoModel($mun->id_estado);
        $alu = new AlunoModel();
        $aluno = $alu->pegaAlunoPorPessoa($sql[0]['id_pessoa']);
        $curso = new CursoModel($aluno[0]['id_curso']);
        $ins = new InstituicaoModel($curso->id_instituicao);
        $tur = new TurnoModel($curso->id_turno);
        $contrato = new ContratoModel();
        $ctr = $contrato->listarPorColaborador($sql[0]['id_colaborador']);
        $atua = new AreaatuacaoModel($ctr[0]['id_area_atuacao']);
        $dados_banc = new DadosbancarioModel();
        $dbanc = $dados_banc->pegaDadosPorPessoa($sql[0]['id_pessoa']);
        $data_cont_ini = UtilHelper::formatBr($ctr[0]['data_inicio']);
        $data_cont_fim = UtilHelper::formatBr($ctr[0]['data_final']);
        $data = UtilHelper::formatBr($pes->data_nascimento);
        $data_exped = UtilHelper::formatBr($pes->data_expedicao);
        $saida = 
        '<html>
            <style type="text/css">
                table tr td {
                    padding:4px;
                }
                .titulo{
                    font-size:16px;
                }
            </style>
            <body>
            <center>
            <table cellspacing="0" width="100%">
            <thead>
                <tr>
                    <td colspan="2" align="center">FICHA DE REGISTRO DO '.$ctp->descricao.'</td>
                </tr>
                <hr />
            </thead>
                    <tr>
                        <td colspan="2"><div class="titulo"><b>1.<u> Dados Pessoais:</u></b></div></td>
                    </tr>
                    <tr>
                        <td align="right" width="30%"><strong>Nome:</strong></td>
                        <td>' .$pes->nome. '</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Data de Nascimento:</strong></td>
                        <td>'.$data.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Nome do Pai:</strong></td>
                        <td>'.$txt_pai.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Nome da Mãe:</strong></td>
                        <td>'.$txt_mae.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Nacionalidade:</strong></td>
                        <td>'.$pes->nacionalidade.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Naturalidade:</strong></td>
                        <td>'.$pes->naturalidade.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>CPF:</strong></td>
                        <td>'.UtilHelper::formataCPF($pes->cpf).'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Naturalidade:</strong></td>
                        <td>'.$pes->rg.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Órgão Expedidor:</strong></td>
                        <td>'.$pes->expedidor.' / <strong>Data Expedição:</strong> '.$data_exped.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>PIS:</strong></td>
                        <td>'.$txt_pis.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Estado Civil:</strong></td>
                        <td>'.$pes->estado_civil.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Email:</strong></td>
                        <td>'.$pes->email.'</td>
                    </tr>
                    <tr>
                        <td colspan="2"><div class="titulo"><b>2. <u>Endereço:</u></b></div></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    <hr />
                    <tr>
                        <td  align="right"><strong>Endereço:</strong></td>
                        <td>'.$end->endereco.' / <strong>Nº:</strong> '.$end->numero.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Bairro:</strong></td>
                        <td>'.$bai->nome.' / '.$mun->nome.' - '. $est->sigla.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>CEP:</strong></td>
                        <td>'.$txt_cep.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Complemento:</strong></td>
                        <td>'.$txt_complemento.'</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    <hr />
                    <tr>
                        <td colspan="2"><div class="titulo"><b>3. <u>Dados Bancarios:</u></b></div></td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Banco:</strong></td>
                        <td>'.$dbanc[0]["banco"].' -- <strong>Agencia:</strong> '.$dbanc[0]["agencia"].' -- <strong>Conta:</strong> '.$dbanc[0]["conta"].'</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    <hr />
                    <tr>
                        <td colspan="2"><div class="titulo"><b>4.<u> Dados Escolares:</u></b></div></td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Instituicao:</strong></td>
                        <td>'.$ins->nome.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Curso:</strong></td>
                        <td>'.$curso->descricao.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Turno:</strong></td>
                        <td>'.$tur->descricao.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Semestre:</strong></td>
                        <td>'.$aluno[0]["semestre"].'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Inicio do Curso:</strong></td>
                        <td>'.UtilHelper::formatBr($aluno[0]["data_inicio"]).'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Termino de Curso:</strong></td>
                        <td>'.UtilHelper::formatBr($aluno[0]["data_fim"]).'</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    <hr />
                    <tr>
                        <td colspan="2"><div class="titulo"><b>5.<u> Dados Relativos ao '.$ctp->descricao.':</u></b></div></td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Área de Atuação:</strong></td>
                        <td>'.$atua->descricao.'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Projeto Vinculado:</strong></td>
                        <td>'.$ctr[0]['projeto'].'</td>
                    </tr>
                    <tr>
                        <td  align="right"><strong>Periodo de Contato:</strong></td>
                        <td>'.$data_cont_ini.' à '.$data_cont_fim.'</td>
                    </tr>
                </table>
            </body>
        </html>
        ';

        $arquivo = "Exemplo03.pdf";
        $mpdf = new mPDF();
        $mpdf->WriteHTML($saida);
        /*
         * F - salva o arquivo NO SERVIDOR
         * I - abre no navegador E NÃO SALVA
         * D - chama o prompt E SALVA NO CLIENTE
         */

        $mpdf->Output($arquivo, 'I');
        $this->_msg->sucesso("PDF GERADO COM SUCESSO");
        $this->_redirect->goToAction('index');
        $this->view();
    }
    
}
