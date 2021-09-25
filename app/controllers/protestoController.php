<?php

class Protesto extends Controller {

    private $protesto,$responsavel, $empresa,
            $_redirect,$_msg, $pessoa, 
            $empre_resp, $protesto_tipo, $protesto_status, 
            $endereco, $taxas, $protesto_taxas, $_session,
            $arquivo, $negociacao, $cartaotipo, $pgtipo,
            $pagamento, $cartao, $transferencia, $dinheiro,
            $aluno, $ocorrencia;
   

    public function init() {
        parent::init();
        $this->protesto = new ProtestoModel();
        $this->empresa = new EmpresaModel();
        $this->empre_resp = new EmpresaresponsavelModel();
        $this->responsavel = new ResponsavelModel();
        $this->endereco = new EnderecoModel();
        $this->protesto_tipo = new ProtestotipoModel();
        $this->protesto_status = new ProtestostatusModel();
        $this->protesto_taxas = new ProtestotaxasModel();
        
        $this->pessoa = new PessoaModel();
        $this->taxas = new TaxasModel();
        $this->arquivo = new ArquivoModel();
        $this->negociacao = new NegociacaoModel();
        $this->pgtipo = new PagamentotipoModel();
        $this->cartaotipo = new CartaotipoModel();
        $this->pagamento = new PagamentoModel();
        $this->dinheiro = new DinheiroModel();
        $this->aluno = new AlunoModel();
        $this->ocorrencia = new ProtestoocorrenciaModel();
        $this->cartao = new CartaoModel();
        $this->transferencia = new TransferenciaModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_session =  new SessionHelper();
    }

    public function index_action() {
        $this->addNavegacao(array('Protesto' => "protesto/index"));
        $page = $this->getParam("page");
        if(!$page){
            $page = 1;
        }
        $this->_session->deletarSession("protesto");
        $list =  $this->protesto->listarPorPagina();
         if(!empty($_POST)){
             $caracter = array("'",'"',"-","*","_",',');
            $select = "SELECT * FROM responsavel a, protesto b, pessoa c WHERE a.id_responsavel = b.id_responsavel AND (c.id_pessoa = a.id_pessoa)";
            if($_POST['nome']){
                $nome = str_replace($caracter, "", $_POST['nome']);
                $andnome = " AND c.nome LIKE '%{$nome}%'";
            }
            
            $busca = $select.$andnome;
            
            $sql = $this->protesto->pesquisar($busca);
            
            if(!$sql){
                $this->_msg->informacao("Nenhum protesto Encontrado!");
            }
            $dados['cont'] = count($sql);
            $dados['sql'] = $sql;
            
        }  else {
            $total = count($list);
            $registro = 6;
            $numero = ceil($total/$registro);
            $inicio = $page - 1;
            $inicio = $inicio * $registro;
            //$this->mostrar($inicio);die();
            $sql = $this->protesto->listarPorPagina(null, $inicio.",".$registro);
            $dados['numero'] = $numero;
            $dados['totalreg'] = $total;
            $dados['sql'] = $sql;
        }
        
        $this->view($dados);
    }
    
    public function inserir(){
        
        $this->addNavegacao(array('Protesto' => "protesto/index", 'Cadastrar' => 'protesto/inserir'));
        if(!empty($_POST)){
            
            if($this->protesto->validar()){
                $dados["resp"] = $this->responsavel->listar();
                $dados["prot"] = $this->protesto_tipo->listar();
                $dados['erro'] = $this->protesto->validar();
                $dados['val'] = $_POST;
                $this->view($dados);
            } else {
                if($this->protesto->salvar()){
                    $this->_msg->sucesso("Portesto Salvo com Sucesso!");
                }  else {
                    $this->_msg->erro("Falha ao Salvar!");
                }
                $this->_redirect->goToController('protesto');
            }
            
        } else {
            $dados["resp"] = $this->responsavel->listar();
            $dados["prot"] = $this->protesto_tipo->listar();
            $this->view($dados);
        }
    }
    
    public function editar(){
        $id = $this->getParam('id');
        $this->addNavegacao(array('Protesto' => "protesto/index", 'Editar' => 'protesto/editar/id/'.$id));
        if($id){
            $this->_session->createSession("protesto", $id);
            if(!empty($_POST)){
                if($_POST['id_taxas'] && $_POST['valor']){
                    $c = -1;
                    $t_array = array();
                    foreach ($_POST['id_taxas'] as $tx){
                        $c++;
                        $taxas =  $tx;
                        $t_array[] = array('id_protesto' => $_POST['id_protesto'],'id_taxas' =>$taxas , 'valor' => $_POST['valor'][$c]);
                    }
                    foreach ($t_array as $tax){
                        if($this->protesto_taxas->validar($tax)){
                            $this->_redirect->goToControllerAction("protesto");
                        }  else {
                            $save = $this->protesto_taxas->salvar();
                        }
                    }
                    if($save){
                        $this->_msg->sucesso("Taxas adionadas ao protesto!");

                    }  else {
                        $this->_msg->erro("Falha ao adionar taxas ao protesto!");
                    }
                }
                if($this->protesto->validar()){
                    $this->_redirect->goToControllerAction("protesto");
                } else {
                    if($this->protesto->alterar()){
                        $this->_msg->sucesso("Portesto Alterado com Sucesso!");

                    }  else {
                        $this->_msg->erro("Falha ao Alterar!");
                    }
                    $this->_redirect->goToController("protesto");
                }
            } else {
                $sql = $this->protesto->buscar($id);
                $dados['sql'] = $sql[0];
                $dados["ptx"] = $this->protesto_taxas->listarPorProtesto($id);
                $dados["aln"] = $this->aluno->pegaAlunoPorResponsavel($id);
                $dados["anx"] = $this->arquivo->listarPorProtesto($id);
                $dados["taxa"] = $this->taxas->listar();
                $dados["empr"] = $this->empresa->listar();
                $dados["resp"] = $this->responsavel->listar();
                $dados["prot"] = $this->protesto_tipo->listar();
                $dados["sts"] = $this->protesto_status->listar();
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
            $this->addNavegacao(array('Protesto' => "protesto/index", 'Visualizar' => 'protesto/visualisar/id/'.$id));
            $sql = $this->protesto->buscar($id);
            $res = new ResponsavelModel($sql[0]['id_responsavel']);
            $pes = new PessoaModel($res->id_pessoa);
            $pf = new PessoarefModel();
            $id_end = $pf->listarPorPessoa($pes->id_pessoa);
            $end = new EnderecoModel($id_end[0]['id_endereco']);
            $bap = new BairroModel($end->id_bairro);
            $ef = new EmpresarefModel();
            $ee = $ef->listarPorEmpresa($sql[0]['id_empresa']);
            $ende = New EnderecoModel($ee[0]['id_endereco']);
            $bae = new BairroModel($ende->id_bairro);
            $emp = new EmpresaModel($sql[0]['id_empresa']);
            $pts = new ProtestostatusModel($sql[0]['id_protesto_status']);
            $ptt = new ProtestotipoModel($sql[0]['id_protesto_tipo']);
            $alu = new AlunoModel($sql[0]['id_aluno']);
            if($sql){
                $dados["neg"] = $this->negociacao->listarPorProtesto($id);
                $dados["propago"] = $this->pagamento->listarPorProtesto($id);
                $dados["ptx"] = $this->protesto_taxas->listarPorProtesto($id);
                $dados["anx"] = $this->arquivo->listarPorProtesto($id);
                $dados["aln"] = new PessoaModel($alu->id_pessoa);
                $dados['sql'] = $sql[0];
                $dados['pes'] = $pes;
                $dados['end'] = $end;
                $dados['ende'] = $ende;
                $dados['emp'] = $emp;
                $dados['pts'] = $pts;
                $dados['ptt'] = $ptt;
                $dados['bap'] = $bap;
                $dados['bae'] = $bae;
                $this->view($dados); 
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("protesto");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("protesto");
        }
    }

    public function excluir() {
        $id = $this->getParam('id');
        if ($id) {
            $this->protesto->transacao();
            try{
                $taxas = $this->protesto_taxas->listarPorProtesto($id);
                if($taxas){
                    foreach ($taxas as $tx){
                        $this->protesto_taxas->excluir($tx['id_protesto_taxas']);
                    }
                }
                $anexo = $this->arquivo->listarPorProtesto($id);
                if($anexo){
                    foreach ($anexo as $ar){
                        $this->arquivo->excluir($ar['id_arquivo']);
                    }
                }
                $negociacao = $this->negociacao->listarPorProtesto($id);
                if($negociacao){
                    foreach ($negociacao as $neg){
                        $this->negociacao->excluir($neg['id_negociacao']);
                    }
                }
                $pagamento = $this->pagamento->listarPorProtesto($id);
                if($pagamento){
                    foreach ($pagamento as $pag){
                        $dinh = $this->dinheiro->listarPorPagamento($pag['id_pagamento']);
                        foreach ($dinh as $di){
                            $this->dinheiro->excluir($di['id_dinheiro']);
                        }
                        $card = $this->cartao->listarPorPagamento($pag['id_pagamento']);
                        foreach ($card as $ca){
                            $this->cartao->excluir($ca['id_cartao']);
                        }
                        $trans = $this->transferencia->listarPorPagamento($pag['id_pagamento']);
                        foreach ($trans as $tr){
                            $this->transferencia->excluir($tr['id_transferencia']);
                        }
                        $this->pagamento->excluir($pag['id_pagamento']);
                    }
                }
                $this->protesto->excluir($id);
                $this->protesto->save();
                $this->_msg->sucesso("Protesto deletado com Sucesso!");
                $this->_redirect->goToController('protesto');
                
            } catch (Exception $ex) {
                $this->protesto->refazer();
                $this->_msg->erro($ex->getMessage());
                $this->_redirect->goToController('protesto');
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("protesto");
        }

    }
    
    public function acompanhamento(){
        $id = $this->getParam('id');
        $this->addNavegacao(array('Protesto' => "protesto/index", 'Acompanhamento' => 'protesto/acompanhamento/id/'.$id));
        if($id){
            $this->_session->createSession("protesto", $id);
            if(!empty($_POST)){
                if($_POST['id_taxas'] && $_POST['valor']){
                    $c = -1;
                    $t_array = array();
                    foreach ($_POST['id_taxas'] as $tx){
                        $c++;
                        $taxas =  $tx;
                        $t_array[] = array('id_protesto' => $_POST['id_protesto'],'id_taxas' =>$taxas , 'valor' => $_POST['valor'][$c]);
                    }
                    foreach ($t_array as $tax){
                        if($this->protesto_taxas->validar($tax)){
                            $this->_redirect->goToControllerAction("protesto");
                        }  else {
                            $save = $this->protesto_taxas->salvar();
                        }
                    }
                    if($save){
                        $this->_msg->sucesso("Taxas adionadas ao protesto!");
                    }  else {
                        $this->_msg->erro("Falha ao adionar taxas ao protesto!");
                    }
                }
                $this->_redirect->goToUrl("protesto/acompanhamento/id/$id");
            } else {
                $sql = $this->protesto->buscar($id);
                $dados['sql'] = $sql[0];
                $dados["pgtp"] = $this->pgtipo->listar($id);
                $dados["neg"] = $this->negociacao->listarPorProtesto($id);
                $dados["ptx"] = $this->protesto_taxas->listarPorProtesto($id);
                $dados["anx"] = $this->arquivo->listarPorProtesto($id);
                
                $dados["taxa"] = $this->taxas->listar();
                $dados["oco"] = $this->ocorrencia->listarPorProtesto($id);

                $dados["propago"] = $this->pagamento->listarPorProtesto($id);
                
                $dados["empr"] = $this->empresa->listar();
                $dados["resp"] = $this->responsavel->listar();
                $dados["prot"] = $this->protesto_tipo->listar();
                $dados["sts"] = $this->protesto_status->listar();
                $this->view($dados);
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("index");
        }
    }
    
    public function buscarempresa(){
        $id = (int) $this->getParam('id');
        
        if($id){
            $sql = $this->empre_resp->listarPorResponsavel($id);
            
            ?>
            <option value="0">=> Selecione <=</option>
              <?php foreach($sql as $empresa){
                  $emp = new EmpresaModel($empresa["id_empresa"]);
                echo "<option value='{$empresa["id_empresa"]}'>{$emp->empresa_nome}</option>";
              }
            ?>
            <?php
        }
        
    }
    
    public function buscainfoempresa(){
        
        $id = (int) $this->getParam('id');
        
        if($id){
            $sql = $this->empresa->buscar($id);
            ?>
            <dl class="dl-horizontal">
                <?php foreach($sql as $resp){ 
     
                    $empresa = new EmpresaModel($resp["id_empresa"]);
                    $emp_ref = New EmpresarefModel();
                   
                    $e = $emp_ref->listarPorEmpresa($empresa->id_empresa);
                    $end = new EnderecoModel($e[0]['id_endereco']);
                    $bai = new BairroModel($end->id_bairro);
                    $muni = new MunicipioModel($bai->id_municipio);
                    $esta = new EstadoModel($muni->id_estado);
                ?>
                <dt>CNPJ:</dt>
                <dd><?php echo UtilHelper::formataCNPJ($empresa->cnpj); ?></dd>
                <dt>Endereço:</dt>
                <dd><?php echo $end->endereco .' '.'<strong>Nº</strong> '.$end->numero. ', <strong>Bairro: </strong>'. $bai->nome; ?></dd>
                <dt>Telefones:</dt>
                <dd><?php echo UtilHelper::formataTelefone($empresa->telefone1).' / '.UtilHelper::formataTelefone($empresa->telefone2).' / '.UtilHelper::formataCelular($empresa->telefone3);?></dd>
                <dt>Municipio:</dt>
                <dd><?php echo $muni->nome .' - '. $esta->sigla. ', <strong> CEP: </strong>'. UtilHelper::formataCep($end->cep); ?></dd>
                <?php } ?>
            </dl>
            <?php
        }
        
    }
    
    public function buscaaluno(){
        
        $id = (int) $this->getParam('id');
        
        if($id){
            $sql = $this->aluno->pegaAlunoPorResponsavel($id);
            ?>
            <option value="0">=> Selecione <=</option>
              <?php foreach($sql as $aluno){
                  $pes = new PessoaModel($aluno["id_pessoa"]);
                echo "<option value='{$aluno["id_aluno"]}'>{$pes->nome}</option>";
              }
            ?>
            <?php
        }
        
    }
    
    
    
    
    public function imprimir(){
        $id = $this->getParam("id");
        $sql = $this->protesto->buscar($id);
        $res = new ResponsavelModel($sql[0]['id_responsavel']);
        $pes = new PessoaModel($res->id_pessoa);
        $pf = new PessoarefModel();
        $id_end = $pf->listarPorPessoa($pes->id_pessoa);
        $endpessoa = new EnderecoModel($id_end[0]['id_endereco']);
        $baipessoa = new BairroModel($endpessoa->id_bairro);
        $muni = new MunicipioModel($baipessoa->id_municipio);
        $esta = new EstadoModel($muni->id_estado);
        $protipo = new ProtestotipoModel($sql[0]['id_protesto_tipo']);
        
        $emp = new EmpresaModel($sql[0]['id_empresa']);
        $ef = new EmpresarefModel();
        $ee = $ef->listarPorEmpresa($sql[0]['id_empresa']);
        $ende = New EnderecoModel($ee[0]['id_endereco']);
        $bae = new BairroModel($ende->id_bairro);
        $munic = new MunicipioModel($bae->id_municipio);
        $estad = new EstadoModel($munic->id_estado);
        $alu = new AlunoModel($sql[0]['id_aluno']);
        $alunoPes = new PessoaModel($alu->id_pessoa);
        $complemento = "";
        if($endpessoa->complemento){
            $complemento = "<strong>Compl: </strong> $endpessoa->complemento";
        }
        $saida = 
        '<html>
            <head>
                <title>Protesto</title>
            </head>
            <style type="text/css">
                table tr td {
                    padding:4px;
                }
            </style>
            <body>
            <img width="140" src="/sistema/public/img/doc_logo.png" alt="" />
            <br />
            <br />
            <br />
            <table cellspacing="0" width="100%" border="0" class="">
            <thead>
                <tr>
                    <td colspan="4" align="center"><strong>AO CARTÓRIO DE PROTESTO DE MACAPÁ - AP</strong></td>
                </tr>
            </thead>
            <br />
            <br />
            <br />
            <tbody>
                <tr>
                    <td colspan="4"><p>Solicito o protesto do referido contrato a seguir transcrito:</p></td>
                </tr>
                <br />
                <br />
                <tr>
                    <td colspan="4"><strong>SACADO / DEVEDOR </strong></td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Nome: </strong>'.UtilHelper::maiuscula($pes->nome).'</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Aluno: </strong>'.UtilHelper::maiuscula($alunoPes->nome).'</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Endereco: </strong>'.UtilHelper::maiuscula($endpessoa->endereco).', '.$endpessoa->numero.'</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Bairro: </strong> '.UtilHelper::primeiraMaiuscula($baipessoa->nome).'</td>
                    <td colspan="2">'.$complemento.'</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Cidade: </strong>'.$muni->nome.'-'. $esta->sigla.'</td>
                    <td colspan="2"><strong>CEP: </strong>'.UtilHelper::formataCep($endpessoa->cep).'</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>CPF: </strong> '.UtilHelper::formataCPF($pes->cpf).'</td>
                    <td><strong>Espécie: </strong> '.UtilHelper::primeiraMaiuscula($protipo->descricao).'</td>
                    <td><strong>Nº: </strong> '.$sql[0]['numero'].'</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Valor do Protesto: </strong> '.UtilHelper::formatoReal($sql[0]['valor_protesto']).'</td>
                    <td colspan="2"><strong>Data da Emissão: </strong> '.UtilHelper::formatBr($sql[0]['data_emissao']).'</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Valor do contrato: </strong> '.UtilHelper::formatoReal($sql[0]['valor_contrato']).'</td>
                    <td colspan="2"><strong>Data de Vencimento: </strong> '.UtilHelper::formatBr($sql[0]['data_vencimento']).'</td>
                </tr>
                
            </tbody>
            </table>
            <br />
                <br />
                <br />
            <table cellspacing="0" width="100%" border="0">
                <tr>
                    <td colspan="4"><strong>DADOS DO CREDOR</strong></td>
                </tr>
                
                <tr>
                    <td colspan="2"><strong>Credor: </strong>  '.$emp->empresa_nome.'</td>
                    <td colspan="2"><strong>CNPJ: </strong> '.UtilHelper::formataCNPJ($emp->cnpj).'</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Endereço: </strong> '.UtilHelper::maiuscula($ende->endereco).', '.$ende->numero.'</td>
                    <td><strong>Bairro: </strong>  '.UtilHelper::primeiraMaiuscula($bae->nome).'</td>
                    <td><strong>Cidade: </strong>  '.$munic->nome.' - '.$estad->sigla.'</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>CEP: </strong>  '.UtilHelper::formataCep($ende->cep).' <strong> Telefones: </strong> '.UtilHelper::formataTelefone($emp->telefone1).' / '.UtilHelper::formataTelefone($emp->telefone2).' / '.UtilHelper::formataCelular($emp->telefone3).'</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Email: </strong> '.$emp->email.'</td>
                </tr>
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <tr>
                    <td colspan="4" align="right">Macapá, '.UtilHelper::formataDataBr(date('Y-m-d')).'</td>
                </tr>
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <tr>
                    <td colspan="4" align="center">__________________________________________</td>
                </tr>
                <tr>
                    <td colspan="4" align="center"><strong>Assinatura do Credor</strong></td>
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
    
    public function taxasexcluir() {
        
        $id = $this->getParam('id');
        $idp = $_SESSION["protesto"];
        if ($id) {
            $taxas = $this->protesto_taxas->excluir($id);
            if($taxas){
                $this->_msg->sucesso("Taxa deletada Sucesso!");
                $this->_redirect->goToUrl("protesto/acompanhamento/id/$idp");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("protesto");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("protesto");
        }

    }
    
    public function arquivoexcluir() {
        
        $id = $this->getParam('id');
        $idp = $_SESSION["protesto"];
        if ($id) {
            $taxas = $this->arquivo->excluir($id);
            if($taxas){
                $this->_msg->sucesso("Anexo deletado Sucesso!");
               $this->_redirect->goToUrl("protesto/editar/id/$idp");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("protesto");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("protesto");
        }

    }
    
    public function pagamentoexcluir() {
        
        $id = $this->getParam('id');
        $idp = $_SESSION["protesto"];
        if ($id) {
            
            try{
                $this->pagamento->transacao();
                $protesto = new ProtestoModel($idp);
                if($protesto->id_protesto_status == 2){
                    throw new Exception('Voce não pode excluir um protesto concluido');
                }  else {
                    
                
                    $dinh = $this->dinheiro->listarPorPagamento($id);
                    foreach ($dinh as $di){
                        $this->dinheiro->excluir($di['id_dinheiro']);
                    }
                    $card = $this->cartao->listarPorPagamento($id);
                    foreach ($card as $ca){
                        $this->cartao->excluir($ca['id_cartao']);
                    }
                    $trans = $this->transferencia->listarPorPagamento($id);
                    foreach ($trans as $tr){
                        $this->transferencia->excluir($tr['id_transferencia']);
                    }

                    $this->pagamento->excluir($id);
                    $this->pagamento->save();
                    $this->_msg->sucesso('Pagamento deletado com Sucesso');
                    $this->_redirect->goToUrl("protesto/acompanhamento/id/$idp");
                }
            } catch (Exception $ex) {
                $this->pagamento->refazer();
                $this->_msg->erro($ex->getMessage());
                $this->_redirect->goToUrl("protesto/acompanhamento/id/$idp");
            }
        }
    }
    
    public function anexos(){
        $idp = $_SESSION["protesto"];
        if(isset($_FILES)){
            $dados = array();
            $up = new UploadHelper();
            $up->setPath("/sistema/public/arquivos/");
            $up->setFile($_FILES["upl"]);
            $up->upload();
            $dados['nome'] = $up->getNovoNome();
            $dados['tipo'] = $up->getFileType();
            $dados['conteudo'] = $up->getBinario();
            $dados['caminho'] = $up->getCaminho();
            $dados['descricao'] = $up->getFileName();
            $dados['id_protesto'] = $_SESSION["protesto"];
            $arquivo = $this->arquivo->validar($dados);
            if($arquivo){
                $this->_redirect->goToControllerAction("protesto");
            } else {
                $save =  $this->arquivo->salvar();
                if($save){
                    $this->_msg->sucesso("Anexo salvo com Sucesso!");
                } else {
                    $this->_msg->erro("Falha ao salvar anexo!");
                }
            } 
        }
    }
    
    public function anexodownload (){
        $id = $this->getParam('id');
        if($id){
        $arquivo = $this->arquivo->buscar($id);
        $tipo = $arquivo[0]['tipo'];
        $conteudo = $arquivo[0]['conteudo'];
        $nome = $arquivo[0]['descricao'].
        header("Content-type: $tipo");
        header("Content-Disposition: attachment; filename=$nome");  
        print $conteudo; 

        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("protesto");
        }
    }
    
    public function negociacao(){
        $id = $this->getParam('id');
        if($id){
            if(!empty($_POST)){
                $c = -1;
                $array = array();
                foreach ($_POST['ano_base'] as $ano){
                    $c++;
                    
//// terminar falta corrigir erro NUll                    
//$multa = null;
//$juros = null;
//if($_POST['multa']){
//    $multa = $_POST['multa']
//}
//// terminar falta corrigir erro NUll                    
                    $array[] = array(
                        'id_protesto' => $id,
                        'ano_base' =>$ano ,
                        'parcela' => $_POST['parcela'][$c],
                        'valor_parcela' => $_POST['valor_parcela'][$c],
                        'multa' => $_POST['multa'][$c],
                        'juros' => $_POST['juros'][$c],
                        'desconto' => $_POST['desconto'][$c],
                    );
                }
                foreach ($array as $dados){
                    if($this->negociacao->validar($dados)){
                        $this->_redirect->goToControllerAction("protesto");
                    }  else {
                        $save = $this->negociacao->salvar();
                    }
                }
                if($save){
                    $this->_msg->sucesso("Negociação efetuada com Sucesso!");
                }  else {
                    $this->_msg->erro("Falha ao cadastrar negociação!");
                }
            }
            $this->_redirect->goToUrl("protesto/acompanhamento/id/$id");
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("protesto");
        }
    }
    
    public function negociacaoexcluir() {
        
        $id = $this->getParam('id');
        $idp = $_SESSION["protesto"];
        if ($id) {
            $taxas = $this->negociacao->excluir($id);
            if($taxas){
                $this->_msg->sucesso("Negociaçẽo de parcela deletada Sucesso!");
                $this->_redirect->goToUrl("protesto/acompanhamento/id/$idp");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("protesto");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("protesto");
        }

    }
    
     public function buscacampos(){
        $id = (int) $this->getParam('id');
        
        if($id){
            $sql = $this->pgtipo->buscar($id);
            if($sql[0]['chave'] == "TRANS"){
            ?>
                <div class="form-group">
                    <label>Banco do Cliente</label>
                        <select class="form-control" id="banCli">
                            <option>--> Selecione <--</option>
                        <?php
                            $bancos = UtilHelper::bancos();
                            foreach ($bancos as $banco){ ?>
                            <option value="<?php echo $banco;  ?>"><?php echo $banco; ?></option>
                        <?php } ?>
                        </select>
                </div>
                <div class="form-group">
                    <label>Agencia / Conta (Cliente)</label>
                    <div class="row">
                    <div class="col-xs-6">
                      <input type="text" id="tAgcli" class="form-control" placeholder="Agencia">
                    </div>
                    <div class="col-xs-6">
                      <input type="text" id="tConcli" class="form-control" placeholder="Conta">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <label>Banco da Empresa</label>
                        <select class="form-control" id="banEmp">
                            <option>--> Selecione <--</option>
                        <?php
                            $bancos = UtilHelper::bancos();
                            foreach ($bancos as $banco){ ?>
                            <option value="<?php echo $banco;  ?>"><?php echo $banco; ?></option>
                        <?php } ?>
                        </select>
                </div>
                <div class="form-group">
                    <label>Agencia / Conta (Empresa)</label>
                    <div class="row">
                    <div class="col-xs-6">
                      <input type="text" id="tAgemp" class="form-control" placeholder="Agencia">
                    </div>
                    <div class="col-xs-6">
                      <input type="text" id="tConemp" class="form-control" placeholder="Conta">
                    </div>
                  </div>
                </div>
            <?php
            }
            if($sql[0]['chave'] == "CART"){
                $view_ctp = $this->cartaotipo->listar();
                $meses = UtilHelper::meses();
                $bandeiras = UtilHelper::bandeiras();
            ?>
            <script type="text/javascript">
            $(function() {
                $('#cTipo').change(function(){
                    var opt =  $('#cTipo option:selected').val();
                     
                    if(opt == 2){
                        $('#cQtd').html('<option value="1">01</option>');
                    }

                 });
             });
            </script>
            
                <div class="form-group">
                    <label>Tipo de Cartão:</label>
                    <select class="form-control" id="cTipo">
                        <option>--> Selecione <--</option>
                    <?php
                        foreach ($view_ctp as $ctp){ ?>
                        <option value="<?php echo $ctp['id_cartao_tipo'];  ?>"><?php echo $ctp['descricao']; ?></option>
                    <?php } ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Bandeira:</label>
                    <select class="form-control" id="bandeira">
                        <option>--> Selecione <--</option>
                    <?php
                        foreach ($bandeiras as $band){ ?>
                        <option value="<?php echo $band  ?>"><?php echo $band ?></option>
                    <?php } ?>
                    </select>
                </div>
            <div class="form-group">
                    <label>Quantidade de Parcelas:</label>
                    <select class="form-control" id="cQtd">
                        <option>--> Selecione <--</option>
                    <?php
                        foreach ($meses as $key => $value){ ?>
                        <option value="<?php echo $key  ?>"><?php echo $key ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Codigo do Documento</label>
                    <div class="input-group">
                        <input type="text" id="cCodDoc" class="form-control" placeholder="">
                    </div>
                </div>
            <?php
            }
            
        }
        
    }
    
    public function pagamento(){
        $id = $this->getParam('id');
        if($id){
            if(!empty($_POST)){
                try{
                    $this->pagamento->transacao();
                    if($_POST['valor_cartao']){
                        $c = -1;
                        foreach ($_POST['valor_cartao'] as $vcartao){
                            $c++;
                            
                            $pagamento = array(
                               "id_pagamento_tipo" => '2',
                               "id_protesto" => $id,
                            );
                            $this->pagamento->validar($pagamento);
                            $this->pagamento->salvar();
                            $id_pg_cart[] = $this->pagamento->pegaUltimoId();
                            
                            $cartao = array(
                                'id_pagamento' => $id_pg_cart[$c],
                                'id_cartao_tipo' => $_POST['id_cartao_tipo'][$c],
                                'bandeira' => $_POST['bandeira'][$c],
                                'parcelas' => $_POST['parcelas'][$c],
                                'codigo' => $_POST['codigo'][$c],
                                'data_cartao' => $_POST['data_cartao'][$c],
                                'valor_cartao' => $vcartao,
                            );
                            $this->cartao->validar($cartao);
                            $this->cartao->salvar();
                        }
                    }
                    
                    if($_POST['valor_transferencia']){
                        $t = -1;
                        foreach ($_POST['valor_transferencia'] as $vtrans){
                            $t++;
                            $pagamento = array(
                               "id_pagamento_tipo" => '3',
                               "id_protesto" => $id,
                            );
                            $this->pagamento->validar($pagamento);
                            $this->pagamento->salvar();
                            $id_pg_tran[] = $this->pagamento->pegaUltimoId();
                            
                            $transferencia = array(
                                'id_pagamento' => $id_pg_tran[$t],
                                'banco_cliente' => $_POST['banco_cliente'][$t],
                                'agencia_cliente' => $_POST['agencia_cliente'][$t],
                                'conta_cliente' => $_POST['conta_cliente'][$t],
                                'banco_empresa' => $_POST['banco_empresa'][$t],
                                'agencia_empresa' => $_POST['agencia_empresa'][$t],
                                'conta_empresa' => $_POST['conta_empresa'][$t],
                                'data_transferencia' => $_POST['data_transferencia'][$t],
                                'valor_transferencia' => $vtrans,
                            );
                            $this->transferencia->validar($transferencia);
                            $this->transferencia->salvar();
                        }
                    }
                    
                    if($_POST['valor_dinheiro']){
                        $d = -1;
                        foreach ($_POST['valor_dinheiro'] as $vdin){
                            $d++;
                            $pagamento = array(
                               "id_pagamento_tipo" => '1',
                               "id_protesto" => $id,
                            );
                            $this->pagamento->validar($pagamento);
                            $this->pagamento->salvar();
                            $id_pg_din[] = $this->pagamento->pegaUltimoId();
                            
                            $dinheiro = array(
                                'id_pagamento' => $id_pg_din[$d],
                                'data_dinheiro' => $_POST['data_dinheiro'][$d],
                                'valor_dinheiro' => $vdin,
                            );
                            $this->dinheiro->validar($dinheiro);
                            $this->dinheiro->salvar();
                        }
                    }
                    $this->pagamento->save();
                    $protestopago = $this->pagamento->listarPorProtesto($id);
                    $taxa =  new ProtestotaxasModel();
                    $taxas = $taxa->listarPorProtesto($id);
                    $vProtesto = new ProtestoModel($id);
                    $negociacao = new NegociacaoModel();
                    $negociacoes = $negociacao->listarPorProtesto($id);
                    
                    foreach ($negociacoes as $neg){
                        $valTotalParcela += $neg['valor_parcela'];
                        $valTotalMulta += $neg['multa'];
                        $valTotalJuros += $neg['juros'];
                        $valTotalDesconto += $neg['desconto'];
                        
                        $valorNegociacao = $valTotalParcela  - $valTotalDesconto;
                       
                    }
                    
                    foreach ($taxas as $ptx){
                        $taxa = new TaxasModel($ptx['id_taxas']);
                        $valorTotalTaxa += $ptx['valor'];
                    }
                     
                    foreach ($protestopago as $propag){
                        $dinheiro = new DinheiroModel();
                        $cartao =  new CartaoModel();
                        $transferencia = new TransferenciaModel();
                        $dinh = $dinheiro->listarPorPagamento($propag['id_pagamento']);
                        $cart = $cartao->listarPorPagamento($propag['id_pagamento']);
                        $trans = $transferencia->listarPorPagamento($propag['id_pagamento']);
                        foreach ($dinh as $din){
                            $valordoDin += $din['valor'];
                        }
                        foreach ($cart as $car){
                            $valordoCart += $car['valor'];
                        }
                        foreach ($trans as $tra){
                            $valordoTrans += $tra['valor'];
                        }

                        $valorPago = $valordoDin + $valordoCart + $valordoTrans;
                    }
                    if (!$valorNegociacao){
                        $valorNegociacao = 0;
                    }
                    if (!$valorTotalTaxa){
                        $valorTotalTaxa = 0;
                    }
                    
                    $valorDeTudo = $valorTotalTaxa + $vProtesto->valor_protesto + $valorNegociacao;
                    
                    if($valorPago > $valorDeTudo || $valorPago == $valorDeTudo){
                        $this->protesto->atualizar(array('id_protesto_status' => 2), $id);
                    }
                       
                    $this->_msg->sucesso("Protesto pago com sucesso!");
                    $this->_redirect->goToUrl("protesto/acompanhamento/id/$id");

                } catch (Exception $ex) {
                    $this->pagamento->refazer();
                    $this->_msg->erro($ex);
                    $this->_redirect->goToUrl("protesto/acompanhamento/id/$id");
                }
            }
        }
    }
    
    public function editardados(){
        if(!empty($_POST)){
            $pessoa = $this->pessoa->validar($_POST['foto']);
            $endereco = $this->endereco->validar();
            if($pessoa || $endereco){
                $this->_redirect->goToControllerAction("protesto", "inserir");
            }  else {
                $pessoa_ref = new PessoarefModel();
                if(!$_POST['id_endereco']){
                    if($this->endereco->salvar()){
                        $pessoa_ref->salvarRef();
                    }
                } else {
                    $this->endereco->alterar();
                }
                $this->pessoa->alterar();
            }
        }
    }
    
    public function buscaendereco(){
        $id = (int) $this->getParam('id');
        
        if($id){
            $sql = $this->responsavel->buscar($id);
            
            ?>
            <dl class="dl-horizontal">
                <?php foreach($sql as $resp){ 
     
                    $pes = new PessoaModel($resp["id_pessoa"]);
                    $pes_ref = New PessoarefModel();
                   
                    $p = $pes_ref->listarPorPessoa($pes->id_pessoa);
                    $end = new EnderecoModel($p[0]['id_endereco']);
                    $bai = new BairroModel($end->id_bairro);
                    $bairros = $bai->listar();
                    $muni =  new MunicipioModel($bai->id_municipio);
                    $esta = new EstadoModel($muni->id_estado);
                ?>
                <dt>CPF:</dt>
                <dd><?php echo UtilHelper::formataCPF($pes->cpf); ?></dd>
                <dt>Email:</dt>
                <dd><?php echo $pes->email; ?></dd>
                <dt>Endereço:</dt>
                <dd><?php echo $end->endereco .' '.'<strong>Nº: </strong> '.$end->numero. ', <strong>Bairro: </strong>'. $bai->nome; ?></dd>
                <dt>Municipio:</dt>
                <dd><?php echo $muni->nome .' - '. $esta->sigla .', <strong> CEP: </strong> '. UtilHelper::formataCep($end->cep); ?></dd>
                <dt>Click p/ Editar:</dt>
                <dd><button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#editEnd" title="editar"><i class="fa fa-edit"></i></button></dd>
                
                <?php } ?>
            </dl>
            <div class="modal fade" id="editEnd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Informações</h4>
        <div id="mostraTotal" class=""></div>
      </div>
        <form  class="form-horizontal" action="" id="formdata" method="post">
            <div class="modal-body">
            <input type="hidden" name="id_endereco" value="<?php echo $end->id_endereco;?>" />
            <input type="hidden" name="id_pessoa" value="<?php echo $pes->id_pessoa; ?>" />
            <input type="hidden" name="sexo" value="<?php echo $pes->sexo; ?>" />
            <input type="hidden" name="nome" value="<?php echo $pes->nome; ?>" />
            <input type="hidden" name="rg" value="<?php echo $pes->rg; ?>" />
            <input type="hidden" name="id_estado" value="<?php echo $pes->id_estado; ?>" />
            <input type="hidden" name="foto" value="<?php echo $pes->foto; ?>" />
            <div class="form-group ">
                <label for="nome" class="col-sm-2 control-label">Nome:</label>
                <div class="col-sm-6">
                    <input type="text" id="nome" value="<?php echo $pes->nome; ?>" class="form-control" name="nome">
                </div>
            </div>
            <div class="form-group ">
                <label for="email" class="col-sm-2 control-label">CPF:</label>
                <div class="col-sm-6">
                    <input type="text" id="cpf" value="<?php echo UtilHelper::formataCPF($pes->cpf); ?>" class="form-control cpf" name="cpf" placeholder="___.___.___-__">
                </div>
            </div>
            <div class="form-group ">
                <label for="email" class="col-sm-2 control-label">Email:</label>
                <div class="col-sm-6">
                    <input type="text" id="email" value="<?php echo $pes->email; ?>" class="form-control" name="email" placeholder="Ex: email@email.com">
                </div>
            </div>

            <div class="form-group ">
                <label for="endereco" class="col-sm-2 control-label">Endereço:</label>
                <div class="col-sm-6">
                    <input type="text" id="endereco" value="<?php echo $end->endereco; ?>" class="form-control" name="endereco" >
                </div>
            </div>
            <div class="form-group ">
                <label for="numero"  class="col-sm-2 control-label">Número:</label>
                <div class="col-sm-6">
                    <input type="text" id="num" value="<?php echo $end->numero; ?>" class="form-control" name="numero" >
                </div>
            </div>
            <div class="form-group ">
                <label for="complemento" class="col-sm-2 control-label">Complemento:</label>
                <div class="col-sm-6">
                    <input type="text" id="complemento" value="<?php echo $end->complemento; ?>" class="form-control" name="complemento" >
                </div>
            </div>
            <div class="form-group ">
                <label for="cep" class="col-sm-2 control-label">CEP:</label>
                <div class="col-sm-6">
                    <input type="text" id="cep" value="<?php echo UtilHelper::formataCep($end->cep); ?>" class="form-control cep" name="cep" >
                </div>
            </div>
            
            <?php
                
                
                $municipios = $muni->listar();
            ?>   
            <div class="form-group">
                <label for="id_municipio" class="col-sm-2 control-label">Cidade / Bairro:</label>
                <div class="col-sm-3">
                    <select name="id_municipio" class="form-control" id="buscabairro">
                        <option>=> Selecione <=</option>
                    <?php foreach ($municipios as $mun){
                    $est = new EstadoModel($mun['id_estado']);
                        ?>
                        <option value="<?php echo $mun['id_municipio'];?>"><?php echo $mun['nome'] .' - '. $est->sigla; ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <select name="id_bairro" class="form-control" id="carregabairro">
                        
                        <option value="<?php echo $end->id_bairro;?>"><?php echo $bai->nome; ?></option>
                    
                    </select>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="enviaDados" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="/sistema/protesto/inserir"class="btn btn-danger"><i class="fa fa-close"></i></a>
            </div>
            
        </form>
        <script type="text/javascript">
            $('.cep').mask("99.999-999");
            $('.cpf').mask("999.999.999-99");
            $("#buscabairro").change(function (){
            var municipio = $(this).val();
            if(municipio){
                var url = '/sistema/colaborador/buscarbairro/id/'+municipio;
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {id:municipio},
                    dataType: "html",
                    success: function(data){
                       $("#carregabairro").html(data);
                    }
                });
            }
        });
        
          $("#enviaDados").click(function(){
            var email  = $('#email').val();
            var endereco  = $('#endereco').val();
            var cpf  = $('#cpf').val();
            var cep  = $('#cep').val();
            var num  = $('#num').val();
            var buscabairro  = $('#buscabairro option:selected').text();
            var carregabairro  = $('#carregabairro option:selected').text();
            
            var info = $('<dl class="dl-horizontal">\n\
                                <dt>CPF: </dt>\n\
                                <dd>'+ cpf +'</dd>\n\
                                <dt>Email: </dt>\n\
                                <dd>'+ email +'</dd>\n\
                                <dt>Endereco: </dt>\n\
                                <dd>'+ endereco +' <strong>Nº: </strong> '+ num +' <strong>Bairro: </strong>'+ carregabairro +'</dd>\n\
                                <dt>Municipio: </dt>\n\
                                <dd>'+ buscabairro +' <strong> CEP: </strong> '+ cep +'</dd>\n\
                            </dl>');
            
            $('#formdata').submit(function(){
                
                    var dados = $( this ).serialize();

                    $.ajax({
                            type: "POST",
                            url: "/sistema/protesto/editardados",
                            data: dados,
                            success: function( data )
                            {
                                $('#load_endereco').html(info);
                            }
                    });
                    $('.modal-backdrop').remove();
                    return false;
            });
        });
        
            
            
            

        </script>
    </div>
  </div>
</div>
            <?php
        }
        
    }
    
    public function pesquisa(){
        $campo = $this->getParam('nome');
        $caracter = array("'",'"',"-","*","_",',');
        $sql = "SELECT * FROM pessoa a, responsavel b WHERE a.id_pessoa = b.id_pessoa";
        if(empty($campo)){
            $view_pes = $this->pessoa->pesquisar($sql);
        }
        if($campo){
            $nome = str_replace($caracter, "", $campo);
            $busca = $sql." AND a.nome LIKE '%{$nome}%' ORDER BY nome";
            $view_pes = $this->pessoa->pesquisar($busca);
        }
        ?>
            <tr>
        <?php foreach ($view_pes as $pes){ 
            echo "<td><input type='radio' value='{$pes['id_responsavel']}' name='id_pessoa' class='modal_id_responsavel' title='{$pes['nome']}'/></td>";
            echo "<td><span class='badge badge-inverse'>{$pes['id_responsavel']}</span></td>";
            echo "<td>{$pes['nome']}</td>";
            echo "<td>".UtilHelper::formataCPF($pes['cpf'])."</td>";
            echo "<td>{$pes['rg']}</td>";

        ?>
            </tr>
        <?php 
        }
    }
    
    public function detalhes(){
        $id = $this->getParam('id');
        if($id){
            if(!empty($_POST)){
                if($this->ocorrencia->validar()){
                    $this->_redirect->goToUrl("protesto/acompanhamento/id/$id");
                } else {
                    $this->ocorrencia->salvar();
                    $this->_msg->sucesso("Detalhe salvo com sucesso!");
                    $this->_redirect->goToUrl("protesto/acompanhamento/id/$id");
                }
            } else {
                $this->view();
            }
        }
    }
    
    public function excluirdetalhes() {
        
        $id = $this->getParam('id');
        $idp = $_SESSION["protesto"];
        if ($id) {
            $taxas = $this->ocorrencia->excluir($id);
            if($taxas){
                $this->_msg->sucesso("Detalhes deletado Sucesso!");
                $this->_redirect->goToUrl("protesto/acompanhamento/id/$idp");
            } else {
                $this->_msg->erro("Falha ao Executar Operação!");
                $this->_redirect->goToController("protesto");
            }
        } else {
            $this->_msg->erro("Falha ao Executar Operação! - Parametros Invalidos");
            $this->_redirect->goToController("protesto");
        }

    }
    
    public function relatorio(){
        $id = $this->getParam('id');
        if ($id) {
            $sql = $this->protesto->buscar($id);
            if($sql){
                $rel = new Relatoriopagamento($sql[0]);
                $rel->gerarRelatorio();
            }
        }
    }
    
    public function cartaanuencia(){
        $id = $this->getParam('id');
        if ($id) {
            $sql = $this->protesto->buscar($id);
            if($sql){
                $rel = new Cartadeanuencia($sql[0]);
                $rel->gerarRelatorio();
            }
        }
    }
}

