<?php

class Orientador extends Controller {

    private $orientador,$municipio, $bairro, $estado,
            $_redirect,$_msg, $pessoa, $pessoa_ref, 
            $telefone, $telefone_tipo, $endereco;
   

    public function init() {
        $this->orientador = new OrientadorModel();
        $this->municipio = new MunicipioModel();
        $this->estado = new EstadoModel();
        $this->bairro = new BairroModel();
        $this->endereco = new EnderecoModel();
        $this->pessoa_ref = new PessoarefModel();
        $this->telefone = new TelefoneModel();
        $this->telefone_tipo = new TelefonetipoModel();
        $this->pessoa = new PessoaModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        $page = $this->getParam("page");
        if(!$page){
            $page = 1;
        }
        $list =  $this->orientador->listarPorPagina();
         if(!empty($_POST)){
             $caracter = array("'",'"',"-","*","_",',');
            $select = "SELECT * FROM pessoa a, orientador b WHERE a.id_pessoa = b.id_pessoa";
            if($_POST['nome']){
                $nome = str_replace($caracter, "", $_POST['nome']);
                $busca = $select." AND a.nome LIKE '%{$nome}%'";
            } else {
                $this->_msg->informacao("Você precisar digitar um nome!");
                $this->_redirect->goToAction('index');
            }
            $sql = $this->orientador->pesquisar($busca);
            if(count($sql) == 0){
                $this->_msg->informacao("Nenhum Colaborador Encontrado!");
            }
            $dados['cont'] = count($sql);
            $dados['sql'] = $sql;
        }
        $total = count($list);
        $registro = 4;
        $numero = ceil($total/$registro);
        $inicio = $page - 1;
        $inicio = $inicio * $registro;
        //$this->mostrar($inicio);die();
        $sql = $this->orientador->listarPorPagina(null, $inicio.",".$registro);
        $dados['numero'] = $numero;
        $dados['sql'] = $sql;
        $this->view($dados);
    }
    
    public function inserir(){
        if(!empty($_POST)){
            $up = new UploadHelper();
            $up->setFile($_FILES["foto"]);
            if($up->upload()){
                $foto = $up->getCaminho();
                $pessoa = $this->pessoa->validar($foto);
                $orientador = $this->orientador->validar();
                $endereco = $this->endereco->validar();
                if($pessoa || $orientador || $endereco){
                    $this->_redirect->goToControllerAction("orientador", 'inserir');
                } else {
                    $this->pessoa->salvar();
                    $this->endereco->salvar();
                    $this->pessoa_ref->salvar();
                    $this->orientador->salvar();
                    $this->_msg->sucesso("Orientador Salvo com Sucesso!");
                    $this->_redirect->goToController("orientador");
                }
            }
        } else {
            $dados["mun"] = $this->municipio->listar();
            $dados["est"] = $this->estado->listar();
            $dados["bai"] = $this->bairro->listar();
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->orientador->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("orientador");
            }
            if(!empty($_POST)){
                $ori = new OrientadorModel($id);
                $pes = new PessoaModel($ori->id_pessoa);
                if($_FILES['foto'] && ($_FILES["foto"]["error"] == 4)){
                    $foto = $pes->foto;
                } else {
                    $up = new UploadHelper();
                    $up->setFile($_FILES["foto"]);
                    $foto = $up->getCaminho();
                }
                $pessoa = $this->pessoa->validar($foto);
                $end = $this->endereco->validar();
                $orientador = $this->orientador->validar();
                if($orientador || $pessoa || $end){
                    $this->_redirect->goToUrl("orientador/editar/id/$id");
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
                    $this->orientador->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("orientador");
                }
                
            }  else {
                $dados["pes"] = $this->pessoa->listar();
                $dados["mun"] = $this->municipio->listar();
                $dados["est"] = $this->estado->listar();
                $dados["bai"] = $this->bairro->listar();
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("orientador");
        }
    }
    
    public function visualizar(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->orientador->buscar($id);
            if($sql){
                $dados['sql'] = $sql[0];
                $this->view($dados); 
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("orientador");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("orientador");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if ($id) {
            $orientador = $this->orientador->excluir($id);
            if($orientador){
                $this->_msg->sucesso("Orientador deletado com Sucesso!");
                $this->_redirect->goToController("orientador");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("orientador");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("orientador");
        }

    }
}
