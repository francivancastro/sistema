<?php

class Relatoriopagamento {
    
    public $dados;
    public function __construct($dados = null) {
        if($dados){
            $this->dados = $dados;
        }
    }

        public function gerarRelatorio(){
        ob_start();
        $res = new ResponsavelModel($this->dados['id_responsavel']);
            $pes = new PessoaModel($res->id_pessoa);
            $pf = new PessoarefModel();
            $id_end = $pf->listarPorPessoa($pes->id_pessoa);
            $end = new EnderecoModel($id_end[0]['id_endereco']);
            $bap = new BairroModel($end->id_bairro);
            $ef = new EmpresarefModel();
            $ee = $ef->listarPorEmpresa($this->dados['id_empresa']);
            $ende = New EnderecoModel($ee[0]['id_endereco']);
            $bae = new BairroModel($ende->id_bairro);
            $emp = new EmpresaModel($this->dados['id_empresa']);
            $pts = new ProtestostatusModel($this->dados['id_protesto_status']);
            $ptt = new ProtestotipoModel($this->dados['id_protesto_tipo']);
            $alu = new AlunoModel($this->dados['id_aluno']);
            $p_alu = new PessoaModel($alu->id_pessoa);
            $pt_tx = new ProtestotaxasModel();
            $ptx  = $pt_tx->listarPorProtesto($this->dados['id_protesto']);
            $nego = new NegociacaoModel();
            $negoc = $nego->listarPorProtesto($this->dados['id_protesto']);
            $protesto_pago = new PagamentoModel();
            $protesto_pg = $protesto_pago->listarPorProtesto($this->dados['id_protesto']);
            $ocorrencia  = new ProtestoocorrenciaModel();
            $view_oco = $ocorrencia->listarPorProtesto($this->dados['id_protesto']);
        ?>
        <html>
            <head>
                <title>Protesto</title>
                <link rel="stylesheet" href="/sistema/public/css/bootstrap.min.css">
            </head>
            <style type="text/css">
                table.tabela1 {
                    border:1px solid #B7CAE6;
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
            </style>
            <body>
                <table class="tabela1" width="100%">
                    <thead>
                        <tr style="background-color: #DEEBF7;">
                            <td colspan="4">
                                <img width="140" src="/sistema/public/img/doc_logo.png" alt=""/> 
                            </td>
                            <td colspan="7">
                                <span style="font-size: 20px; font-weight: bold">Relatório de Pagamento</span>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" style="background-color: #fff">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="11">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3">Aluno: <?php echo $p_alu->nome;?></td>
                            <td colspan="5">Responsavel: <?php echo $pes->nome;?></td>
                            <td colspan="3">Empresa: <?php echo $emp->empresa_nome;?></td>
                        </tr>
                        <tr>
                            <td colspan="11">&nbsp;</td>
                        </tr>
                         <tr>
                            <td style="text-align: center; font-weight: bold">Tipo</td>
                            <td style="text-align: center; font-weight: bold">Base</td>
                            <td style="text-align: center; font-weight: bold">Parcela</td>
                            <td style="text-align: center; font-weight: bold">Valor da parcela</td>
                            <td style="text-align: center; font-weight: bold">Multa</td>
                            <td style="text-align: center; font-weight: bold">Juros</td>
                            <td style="text-align: center; font-weight: bold">Desconto</td>
                            <td style="text-align: center; font-weight: bold">Taxa do Protesto</td>
                            <td style="text-align: center; font-weight: bold">Taxa do Edital</td>
                            <td style="text-align: center; font-weight: bold">Taxa de Cancelamento</td>
                            <td colspan="2" style="text-align: center; border-left:1px solid #B7CAE6; font-weight: bold">Total</td>
                        </tr>
                        <tr>
                            <?php
                                $txt_valtaxa_prot = " -- ";
                                $txt_valtaxa_edital = " -- ";
                                $txt_valtaxa_cancelamento = " -- ";
                                
                                foreach ($ptx as $tx){
                                    if($tx && $tx['id_taxas'] == '1'){
                                        $txt_valtaxa_prot = UtilHelper::formatoReal($tx['valor']);
                                    }
                                    if($tx && $tx['id_taxas'] == '2'){
                                        $txt_valtaxa_edital = UtilHelper::formatoReal($tx['valor']);
                                    }
                                    if($tx && $tx['id_taxas'] == '3'){
                                        $txt_valtaxa_cancelamento = UtilHelper::formatoReal($tx['valor']);
                                    }
                                    
                                    $vt += $tx['valor'];
                                }
                                $valorPagar = $this->dados['valor_protesto'] + $vt;
                                $data  = new DateTime($this->dados['data_vencimento']);
                                $txt_mes_marcela = '';
                                $messes = UtilHelper::meses();
                                foreach ($messes as $nMes => $txtMes){ 
                                    if($nMes == $data->format("m")){
                                        $txt_mes_marcela = $nMes.' / '.$txtMes;
                                    }
                                }
                                
                            ?>
                            <td style="text-align: center">Protesto</td>
                            <td style="text-align: center"><?php echo $this->dados['ano_base'];?></td>
                            <td style="text-align: center"><?php echo $txt_mes_marcela;?></td>
                            <td style="text-align: center"><?php echo UtilHelper::formatoReal($this->dados['valor_protesto']);?></td>
                            <td style="text-align: center"> -- </td>
                            <td style="text-align: center"> -- </td>
                            <td style="text-align: center"> -- </td>
                            <td style="text-align: center"><?php echo $txt_valtaxa_prot;?></td>
                            <td style="text-align: center"><?php echo $txt_valtaxa_edital;?></td>
                            <td style="text-align: center"><?php echo $txt_valtaxa_cancelamento;?></td>
                            <td colspan="2" style="text-align: center;  border-left:1px solid #B7CAE6;"><?php echo UtilHelper::formatoReal($valorPagar); ?></td>
                        </tr>
                        <?php foreach ($negoc as $neg){
                            $txt_desconto = UtilHelper::formatoReal($neg['desconto']);
                            $txt_multa = UtilHelper::formatoReal($neg['multa']);
                            $txt_juros = UtilHelper::formatoReal($neg['juros']);
                            $valTotalParcela += $neg['valor_parcela'];
                            $valTotalMulta += $neg['multa'];
                            $valTotalJuros += $neg['juros'];
                            $valTotalDesconto += $neg['desconto'];
                            
                            $ptotal = $neg['valor_parcela'] + $neg['multa'] + $neg['juros'] - $neg['desconto'];
                             
                            $tn += $ptotal;
                            
                            if($neg['desconto'] == 0){
                                $txt_desconto = "--";
                            }
                            if($neg['multa'] == 0){
                                $txt_multa = "--";
                            }
                            if($neg['juros'] == 0){
                                $txt_juros = "--";
                            }
                            $txt_mes = '';
                            $messes = UtilHelper::meses();
                            foreach ($messes as $nMes => $txtMes){ 
                                if($nMes == $neg['parcela']){
                                    $txt_mes = $nMes.' - '.$txtMes;
                                }
                            }
                        ?>
                        <tr>
                            <td style="text-align: center">Negociacão</td>
                            <td style="text-align: center"><?php echo $neg['ano_base']; ?></td>
                            <td style="text-align: center"><?php echo $txt_mes; ?></td>
                            <td style="text-align: center"><?php echo UtilHelper::formatoReal($neg['valor_parcela']); ?></td>
                            <td style="text-align: center"><?php echo $txt_multa;?></td>
                            <td style="text-align: center"><?php echo $txt_juros;?></td>
                            <td style="text-align: center"><?php echo $txt_desconto; ?></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" style="text-align: center;  border-left:1px solid #B7CAE6;"><?php echo UtilHelper::formatoReal($ptotal); ?></td>
                        </tr>
                        <?php }
                            $txt_tmulta = "--";
                            $txt_tjuros = "--";
                            $txt_tdesc = "--";
                            $txt_td = "--";
                            $vtp = $valTotalParcela + $this->dados['valor_protesto'];
                            $td = $tn + $valorPagar;
                            $txt_td = UtilHelper::formatoReal($td);
                            $txt_tvp = UtilHelper::formatoReal($vtp);
                            if($valTotalMulta){
                                $txt_tmulta = UtilHelper::formatoReal($valTotalMulta);
                            }
                            if($valTotalJuros){
                                $txt_tjuros = UtilHelper::formatoReal($valTotalJuros);
                            }
                            if($valTotalDesconto){
                                $txt_tdesc = UtilHelper::formatoReal($valTotalDesconto);
                            }
                            
                        ?>
                        <tr>
                            <td colspan="10">&nbsp;</td>
                            <td colspan="1" style="text-align: center; border-left:1px solid #B7CAE6; font-weight: bold">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3"  style="text-align: right; font-weight: bold">Subtotais</td>
                            <td style="text-align: center"><?php echo $txt_tvp; ?></td>
                            <td style="text-align: center"><?php echo $txt_tmulta; ?></td>
                            <td style="text-align: center"><?php echo $txt_tjuros; ?></td>
                            <td style="text-align: center"><?php echo $txt_tdesc; ?></td>
                            <td style="text-align: center"><?php echo $txt_valtaxa_prot; ?></td>
                            <td style="text-align: center"><?php echo $txt_valtaxa_edital; ?></td>
                            <td style="text-align: center"><?php echo $txt_valtaxa_cancelamento; ?></td>
                            <td colspan=""  style="text-align: center; border-left:1px solid #B7CAE6; font-weight: bold"><?php echo $txt_td; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: bold">Valor Pago</td>
                            <td colspan="7"></td>
                            <td colspan="1" style="text-align: center;  border-left:1px solid #B7CAE6; font-weight: bold"><?php echo $txt_td; ?></td>
                        </tr>
                        <tr>
                            <td colspan="11">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="11" style="font-weight: bold">Descrição de Negociação</td>
                        </tr>
                        <?php foreach ($view_oco as $oco){ ?>
                        <tr>
                            <td style="text-align: center"><?php echo $oco['titulo'];?></td>
                            <td style="text-align: center"><?php echo $oco['texto']; ?></td>
                            <td style="text-align: center"><?php echo UtilHelper::formatBr($oco['data'])?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="11">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold">Forma de Pagamento</td>
                            <td style="text-align: center; font-weight: bold">Data</td>
                            <td style="text-align: center; font-weight: bold">Valor</td>
                            <td style="text-align: center; font-weight: bold">Tipo</td>
                            <td style="text-align: center; font-weight: bold">Bandeira</td>
                            <td style="text-align: center; font-weight: bold">No Doc</td>
                            <td style="text-align: center; font-weight: bold">Qtd Parcela</td>
                            <td style="text-align: center; font-weight: bold">Banco Cliente</td>
                            <td style="text-align: center; font-weight: bold">Ag / Conta</td>
                            <td style="text-align: center; font-weight: bold">Banco Empresa</td>
                            <td style="text-align: center; font-weight: bold">Ag / Conta</td>
                        </tr>
<?php if($protesto_pg){ ?>  
                        <?php 
                        foreach ($protesto_pg as $propag){
                            $dinheiro = new DinheiroModel();
                            $pgtipo = new PagamentotipoModel($propag['id_pagamento_tipo']);
                            $dinh = $dinheiro->listarPorPagamento($propag['id_pagamento']);

                            foreach ($dinh as $di){
                                $totalDinheiro += $di['valor'];
                        ?>
                                <tr>
                                    <td style="text-align: center"><?php echo $pgtipo->descricao; ?></td>
                                    <td style="text-align: center"><?php echo UtilHelper::formatBr($di['data_dinheiro']) ?></td>
                                    <td style="text-align: center"><?php echo UtilHelper::formatoReal($di['valor']) ?></td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                </tr>
                        <?php }} ?>
        <?php foreach ($protesto_pg as $propag){
            $cartao = new CartaoModel();
            $pgtipo = new PagamentotipoModel($propag['id_pagamento_tipo']);
            $cart = $cartao->listarPorPagamento($propag['id_pagamento']);
        
            foreach ($cart as $ca){
                $ctp = new CartaotipoModel($ca['id_cartao_tipo']);
                $parcela = $ca['valor'] / $ca['parcelas'];
                $totalCartao += $ca['valor'];
        ?>
                                <tr>
                                    <td style="text-align: center"><?php echo $pgtipo->descricao; ?></td>
                                    <td style="text-align: center"><?php echo UtilHelper::formatBr($ca['data_cartao']); ?></td>
                                    <td style="text-align: center"><?php echo UtilHelper::formatoReal($ca['valor']) ?></td>
                                    <td style="text-align: center"><?php echo $ctp->descricao ?></td>
                                    <td style="text-align: center"><?php echo $ca['bandeira'] ?></td>
                                    <td style="text-align: center"><?php echo $ca['codigo']; ?></td>
                                    <td style="text-align: center"><?php echo $ca['parcelas']; ?></td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    
                                </tr>
        <?php } } ?>
        <?php foreach ($protesto_pg as $propag){
            $transferencia = new TransferenciaModel();
            $pgtipo = new PagamentotipoModel($propag['id_pagamento_tipo']);
            $trans = $transferencia->listarPorPagamento($propag['id_pagamento']);
        
            foreach ($trans as $ta){
                $totalTrans += $ta['valor'];
        ?>
                                <tr>
                                    <td style="text-align: center"><?php echo $pgtipo->descricao; ?></td>
                                    <td style="text-align: center"><?php echo UtilHelper::formatBr($ta['data_transferencia']) ?></td>
                                    <td style="text-align: center"><?php echo UtilHelper::formatoReal($ta['valor']) ?></td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"> -- </td>
                                    <td style="text-align: center"><?php echo $ta['banco_cliente'] ?></td>
                                    <td style="text-align: center"><?php echo $ta['agencia_cliente']." / ". $ta['conta_cliente'] ?></td>
                                    <td style="text-align: center"><?php echo $ta['banco_empresa'] ?></td>
                                    <td style="text-align: center"><?php echo $ta['agencia_empresa']." / ". $ta['conta_empresa'] ?></td>
                                    
                                </tr>
        <?php } } ?>
                        <?php } ?>   
                    </tbody>
                </table>
                
                
            </body>
        </html>
        <?php
        $saida = ob_get_contents();
        ob_end_clean();
        
        $saida = mb_convert_encoding($saida,'UTF-8', 'UTF-8');
        $arquivo = "Exemplo03.pdf";
        $mpdf = new mPDF('UTF-8', 'A4-L');
        $mpdf->WriteHTML($saida);
        /*
         * F - salva o arquivo NO SERVIDOR
         * I - abre no navegador E NÃO SALVA
         * D - chama o prompt E SALVA NO CLIENTE
         */

        $mpdf->Output($arquivo, 'I');
    }
    
}

