<?php

class Usuario extends Controller {

    private $_redirect, $_msg, $usuario, $_upload, $pessoa, $grupo, $usuariogrupo;
   

    public function init() {
        $this->pessoa = new PessoaModel();
        $this->usuario =  new UsuarioModel();
        $this->grupo =  new GrupoModel();
        $this->usuariogrupo = new UsuariogrupoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_upload = new UploadHelper();
    }

    public function index_action() {
        $this->addNavegacao(array('Usuários' => "usuario/index"));
        $page = $this->getParam("page");
        if(!$page){
            $page = 1;
        }
        $list =  $this->usuario->listarPorPagina();
        //$this->mostrar($sql);
        if(!empty($_POST)){
            $caracter = array("'",'"',"-","*","_",',');
            $select = "SELECT * FROM pessoa a, usuario b WHERE a.id_pessoa = b.id_pessoa";
            if($_POST['nome']){
                $nome = str_replace($caracter, "", $_POST['nome']);
                $busca = $select." AND a.nome LIKE '%{$nome}%'";
            } else {
                $this->_msg->informacao("Você precisar digitar um nome!");
                $this->_redirect->goToAction('index');
            }
            $sql = $this->usuario->pesquisar($busca);
            if(count($sql) == 0){
                $this->_msg->informacao("Nenhum Usuario Encontrado!");
            }
            $dados['sql'] = $sql;
        } else {
            $total = count($list);
            $registro = 4;
            $numero = ceil($total/$registro);
            $inicio = $page - 1;
            $inicio = $inicio * $registro;
            //$this->mostrar($inicio);die();
            $sql = $this->usuario->listarPorPagina(null, $inicio.",".$registro);
            $dados['numero'] = $numero;
            $dados['sql'] = $sql;
        }
        $this->view($dados);
    }
    
    public function inserir(){
        $this->addNavegacao(array('Usuários' => "Usuário/index", 'Cadastrar' => 'protesto/inserir'));
        if(!empty($_POST)){
            $this->usuario->transacao();
            try {
                $pessoa = $this->pessoa->validar();
                if($pessoa){
                    throw new Exception("Falha ao salvar pessoa!");
                } else {
                    $sp = $this->pessoa->salvar();
                    if($sp){
                        $_POST['id_pessoa'] = $this->pessoa->pegaUltimoId();
                        $usuario = $this->usuario->validar();
                        if($usuario){
                            throw new Exception("Falha ao salvar usuário!");
                        } else {
                            $this->usuario->salvar();
                            $this->_msg->sucesso("Usuário Cadastrado com Sucesso!");
                            
                        }
                    }
                    $this->usuario->save();
                    $this->_redirect->goToControllerAction("usuario", 'inserir');
                }
                
                
            } catch (Exception $ex) {
                $this->usuario->refazer();
                $this->_msg->erro($ex->getMessage());
                $this->_redirect->goToController("usuario");
            }
            
        } else {
            $dados["pes"] = $this->pessoa->listar();
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $this->addNavegacao(array('Usuários' => "usuario/index", 'Editar' => 'usuario/edidar/id/'.$id));
            $sql = $this->usuario->buscar($id);
            if($sql){
                 $dados['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("usuario");
            }
            if(!empty($_POST)){
                $usuario = $this->usuario->validar();
                if($usuario){
                    $this->_redirect->goToUrl("usuario/editar/id/$id");
                } else {
                    $this->usuario->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToController("usuario");
                }
                
            }  else {
                $dados["pes"] = $this->pessoa->listar();
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("usuario");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if ($id) {
            $usuario = $this->usuario->excluir($id);
            if($usuario){
                $this->_msg->sucesso("Usuário deletado com Sucesso!");
                $this->_redirect->goToController("usuario");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("usuario");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("usuario");
        }

    }
    
    public function bloquear(){
        $id = $this->getParam('id');
        $usu = new UsuarioModel($id);
        if ($id) {
            if($usu->habilitado == "S"){
                $this->usuario->atualizar(array("habilitado" => "N"), $id);
                $this->_msg->sucesso("Usuario Bloqueado com Sucesso!");
                $this->_redirect->goToController("usuario");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("usuario");
       }
    }
    
    public function desbloquear(){
        $id = $this->getParam('id');
        $usu = new UsuarioModel($id);
        if ($id) {
            if($usu->habilitado == "N"){
                $this->usuario->atualizar(array("habilitado" => "S"), $id);
                $this->_msg->sucesso("Usuario Desbloqueado com Sucesso!");
                $this->_redirect->goToController("usuario");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("usuario");
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
    
    public function pesquisa(){
        $campo = $this->getParam('nome');
        $caracter = array("'",'"',"-","*","_",',');
        $sql = "SELECT * FROM pessoa ";
        if(empty($campo)){
            $view_pes = $this->pessoa->pesquisar($sql);
        }
        if($campo){
            $nome = str_replace($caracter, "", $campo);
            $busca = $sql."WHERE nome LIKE '%{$nome}%' ORDER BY nome";
            $view_pes = $this->pessoa->pesquisar($busca);
        }
        ?>
            <tr>
        <?php foreach ($view_pes as $pes){ 
            echo "<td><input type='radio' value='{$pes['id_pessoa']}' name='id_pessoa' class='modal_id_orientador' title='{$pes['nome']}'/></td>";
            echo "<td><span class='badge badge-inverse'>{$pes['id_pessoa']}</span></td>";
            echo "<td>{$pes['nome']}</td>";
            echo "<td>{$pes['cpf']}</td>";
            echo "<td>{$pes['rg']}</td>";

        ?>
            </tr>
        <?php 
        }
    }
    
    
    public function grupo(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->usuariogrupo->listarPorUsuario($id);
            $gp = $this->grupo->listar();
            $datas['gp'] = $gp;
            $this->addNavegacao(array(
                'Controller' => "controle/index",
                'Actions' => "controle/action/id/$id"
                ));
            //$this->mostrar($this->ftipo);die();
            if(!empty($_POST)){
                $grupos = $_POST['id_grupo'];
                foreach ($gp as $chave => $valor){
                    if (!in_array($valor["id_grupo"], $grupos)) {
                        $id_gp = $valor["id_grupo"];
                        $this->usuariogrupo->excluirPorGrupo($id_gp, $id);
                    }
                }
                foreach ($grupos as $grp) {
                    $array = array(
                        'id_usuario' => $id,
                        'id_grupo' => $grp
                    );
                    $validar = $this->usuariogrupo->validar($array);
                    if($validar){
                       $this->_redirect->goToUrl("usuario/grupo/id/$id"); 
                    }  else {
                        $this->usuariogrupo->salvar();
                        $this->_msg->sucesso("Salvo com Sucesso!"); 
                    }
                }
                
            }
           
            $ctl = new ControllerModel($id);
            $datas['ctl'] = $ctl;
            $datas['id'] = $id;
            $datas['sql'] = $sql;
            $this->view($datas);
        }
        
    }
    
}
