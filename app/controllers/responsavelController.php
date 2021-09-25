<?php

class Responsavel extends Controller {

    private $responsavel,$municipio, $bairro, $estado,
            $_redirect,$_msg, $pessoa, $pessoa_ref, 
            $telefone, $telefone_tipo, $endereco,
            $aluno, $_session, $empresa, $empre_resp;
   

    public function init() {
        $this->responsavel = new ResponsavelModel();
        $this->municipio = new MunicipioModel();
        $this->estado = new EstadoModel();
        $this->bairro = new BairroModel();
        $this->endereco = new EnderecoModel();
        $this->pessoa_ref = new PessoarefModel();
        $this->telefone = new TelefoneModel();
        $this->telefone_tipo = new TelefonetipoModel();
        $this->pessoa = new PessoaModel();
        $this->aluno = new AlunoModel();
        $this->empresa = new EmpresaModel();
        $this->empre_resp = new EmpresaresponsavelModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_session = new SessionHelper();
    }

    public function index_action() {
        $this->_session->deletarSession('responsavel');
        $page = $this->getParam("page");
        if(!$page){
            $page = 1;
        }
        $list =  $this->responsavel->listarPorPagina();
         if(!empty($_POST)){
             $caracter = array("'",'"',"-","*","_",',');
            $select = "SELECT * FROM pessoa a, responsavel b WHERE a.id_pessoa = b.id_pessoa";
            if($_POST['nome']){
                $nome = str_replace($caracter, "", $_POST['nome']);
                $busca = $select." AND a.nome LIKE '%{$nome}%'";
            } else {
                $this->_msg->informacao("Você precisar digitar um nome!");
                $this->_redirect->goToAction('index');
            }
            $sql = $this->responsavel->pesquisar($busca);
            
            if(count($sql) == 0){
                $this->_msg->informacao("Nenhum responsável encontrado!");
            }
            $dados['cont'] = count($sql);
            $dados['sql'] = $sql;
            $this->view($dados);
        }
        $total = count($list);
        $registro = 10;
        $numero = ceil($total/$registro);
        $inicio = $page - 1;
        $inicio = $inicio * $registro;
        //$this->mostrar($inicio);die();
        $sql = $this->responsavel->listarPorPagina(null, $inicio.",".$registro);
        $dados['numero'] = $numero;
        $dados['sql'] = $sql;
        $this->view($dados);
    }
    
    public function inserir(){
        if(!empty($_POST)){
                $pessoa = $this->pessoa->validar(null);
                if($pessoa){
                    $this->_redirect->goToUrl("responsavel/inserir");
                } else {
                    $transacao = $this->pessoa->transacao();
                    try {
                        if($this->pessoa->salvar()){
                            if($this->responsavel->salvarNovo()){
                                
                                $id_empresa = $this->empresa->listar();
                                foreach ($id_empresa as $ide){
                                    $this->empre_resp->salvarNovo($ide['id_empresa']);
                                }
                            } else {
                                throw new Exception("Falha ao Salvar");
                            }
                        } else {
                            throw new Exception("Falha ao Salvar");
                        }
                        $this->pessoa->save();
                        $this->_msg->sucesso("Responsável cadastrado com sucesso!");
                        $this->_redirect->goToUrl("responsavel/inserir");
                    } catch (Exception $ex) {
                        $this->pessoa->refazer();
                        $this->_msg->erro($ex);
                        $this->_redirect->goToUrl("responsavel/inserir");
                    }
                    
                }
                
            } else {
                $this->view();
            }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->responsavel->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("responsavel");
            }
            if(!empty($_POST)){
                $pes = new PessoaModel($sql[0]['id_pessoa']);
                $foto = $pes->foto;
                $pessoa = $this->pessoa->validar($foto);
                if($pessoa){
                    $this->_redirect->goToUrl("responsavel/editar/id/$id");
                } else {
                    $this->pessoa->alterar();
                    $this->_msg->sucesso("Responsável alterado com sucesso");
                    $this->_redirect->goToUrl("responsavel/editar/id/$id");
                }
                
            }  else {
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("responsavel");
        }
    }
    
    public function visualizar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->responsavel->buscar($id);
            if($sql){
                $dados['sql'] = $sql[0];
                $this->view($dados); 
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("responsavel");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("responsavel");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if ($id){
            $responsavel = $this->responsavel->excluir($id);
            if($responsavel){
                $this->_msg->sucesso("responsavel deletado com Sucesso!");
                $this->_redirect->goToController("responsavel");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("responsavel");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("responsavel");
        }

    }
    
    public function aluno(){
        if (!$this->_session->checkSession("responsavel")){
            $id = $this->getParam("id");
            if($id){
                $this->_session->createSession("responsavel", $id);
                $sql = $this->aluno->pegaAlunoPorResponsavel($id);
                $dados['sql'] = $sql;
                $this->view($dados);
            }  else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("ferias");
            }
        }  else {
            $id = $_SESSION["responsavel"];
            $sql = $this->aluno->pegaAlunoPorResponsavel($id);
            $dados['sql'] = $sql;
            $this->view($dados);
        }
    }
    
    public function alunoinserir(){
        if(!empty($_POST)){
            $validar = $this->pessoa->validar();
            if($validar){
                $this->_redirect->goToControllerAction("responsavel", 'alunoinserir');
            } else {
                if($this->pessoa->salvar()){
                    if($this->aluno->validar()){
                        $this->_redirect->goToControllerAction("responsavel", 'alunoinserir');
                    }  else {
                        $this->aluno->salvarNovo();
                        $this->_msg->sucesso("Aluno cadastrado com sucesso!");
                        $this->_redirect->goToControllerAction("responsavel", 'alunoinserir');
                    }
                } else {
                    $this->_msg->erro("Falha ao salvar!");
                    $this->_redirect->goToControllerAction("responsavel", 'alunoinserir');
                }
                
            }
        } else {
            $this->view();
        }
    }

        public function alunoeditar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->aluno->buscar($id);
            
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToControllerAction("protesto", "aluno");
            }
            if(!empty($_POST)){
                $pessoa = $this->pessoa->validar();
                if($pessoa){
                    $this->_redirect->goToUrl("responsavel/alunoeditar/id/$id");
                } else {
                    $this->pessoa->alterar();
                    $this->_msg->sucesso("Aluno alterado com sucesso");
                    $this->_redirect->goToUrl("responsavel/alunoeditar/id/$id");
                }
                
            }  else {
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToControllerAction("protesto", "aluno");
        }
    }
    
    
}
