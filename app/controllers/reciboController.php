<?php

class Recibo extends Controller {
    
    private $protesto, $taxas, $recibo, $_redirect, $_msg;
    
    public function init() {
        parent::init();
        $this->protesto = new ProtestoModel();
        $this->taxas = new TaxasModel();
        $this->recibo = new ReciboModel();
        $this->_msg = new MsgHelper();
        $this->_redirect =  new RedirectorHelper();
    }
    
    public function index_action(){
        $this->addNavegacao(array('Recibo' => "recibo/index"));
        $page = $this->getParam("page");
        if(!$page){
            $page = 1;
        }
        $list =  $this->recibo->listarPorPagina();
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
            $sql = $this->recibo->pesquisar($busca);
            
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
        $sql = $this->recibo->listarPorPagina(null, $inicio.",".$registro);
        $dados['numero'] = $numero;
        $dados['sql'] = $sql;
        $this->view($dados);
    }
    
    public function inserir(){
        $this->addNavegacao(array('Recibo' => "recibo/index", "Inserir" => "recibo/inserir"));
        if(!empty($_POST)){
            $recibo = $this->recibo->validar();
            if($recibo){
                $this->_redirect->goToController("recibo");
            } else {
                $this->recibo->salvar();
                $this->_msg->sucesso("Recibo salvo com Sucesso!");
                $this->_redirect->goToControllerAction("recibo", "inserir");
            }
        }  else {
            $list = $this->protesto->listar();
            $txs = $this->taxas->listar();
            $dados['prot'] = $list;
            $dados['txs'] = $txs;
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        if($id){
            $this->addNavegacao(array('Recibo' => "recibo/index", 'Editar' => 'taxas/editar/id/'.$id));
            $sql = $this->recibo->buscar($id);
            if($sql){
                 $datas['sql'] = $sql[0];
            } else {
                $this->_msg->erro("Pagina não encontrada");
                $this->_redirect->goToController("recibo");
            }
            if(!empty($_POST)){;
                $validar = $this->recibo->validar();
                if($validar){
                    $this->_redirect->goToControllerAction("recibo", 'editar');
                }  else {
                    $this->recibo->alterar();
                    $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                    $this->_redirect->goToUrl("/recibo/editar/id/{$id}");
                }
                
            } else {
                $txs = $this->taxas->listar();
                $datas['txs'] = $txs;
                $this->view($datas);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("recibo");
        }
    }
    
    public function pesquisa(){
        $campo = $this->getParam('nome');
        $caracter = array("'",'"',"-","*","_",',');
        $sql = "SELECT * FROM protesto a, responsavel b, pessoa c
                WHERE a.id_responsavel = b.id_responsavel
                AND (b.id_pessoa = c.id_pessoa)";
        if(empty($campo)){
            $view_prot = $this->protesto->pesquisar($sql);
        }
        if($campo){
            $nome = str_replace($caracter, "", $campo);
            $busca = $sql."AND (c.nome LIKE '%{$nome}%') ORDER BY c.nome";
            $view_prot = $this->protesto->pesquisar($busca);
        }
        foreach ($view_prot as $prot){
            $respon = new ResponsavelModel($prot['id_responsavel']);
                $aluno = new AlunoModel($prot['id_aluno']);
                $p_resp = new PessoaModel($respon->id_pessoa);
                $a_resp = new PessoaModel($aluno->id_pessoa);
                $txt_emissao = UtilHelper::formatBr($prot['data_emissao']);
                $txt_vencimento = UtilHelper::formatBr($prot['data_vencimento']);
            echo '<tr>';
            echo "<td><input type='radio' value='{$prot['id_protesto']}' name='id_protesto' class='modal_id_protesto' title='$p_resp->nome'/></td>";
            echo "<td><span class='badge badge-inverse'>{$prot['id_protesto']}</span></td>";
            echo "<td>{$p_resp->nome}</td>";
            echo "<td>{$a_resp->nome}</td>";
            echo "<td>{$txt_emissao}</td>";
            echo "<td>{$txt_vencimento}</td>";
            echo '</tr>';
        }
    }
    
    public function imprimir(){
        $id = $this->getParam('id');
        if($id){
            $sql = $this->recibo->buscar($id);
            if($sql){
                $rel = new Relatoriorecibo($sql[0]);
                $rel->gerarRelatorio(); 
            } else {
                $this->_msg->informacao("Nenhun registro encontrado!");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("recibo");
        }
    }
    
    public function excluir() {
        $id = $this->getParam('id');
        if($id){
            if($this->recibo->excluir($id)){
                $this->_msg->sucesso("Operação Efetuada com Sucesso!");
                $this->_redirect->goToController("recibo");
            } else {
                $this->_msg->erro("Existe Registros vinculador a essa Funçao!");
                $this->_redirect->goToController("recibo");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("recibo");
        }
    }
}