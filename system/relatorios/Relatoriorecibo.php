<?php

class Relatoriorecibo {
    
    public $dados;
    public function __construct($dados = null) {
        if($dados){
            $this->dados = $dados;
        }
    }

        public function gerarRelatorio(){
        ob_start();
        $protesto = new ProtestoModel($this->dados['id_protesto']);
        $respon = new ResponsavelModel($protesto->id_responsavel);
        $aluno = new AlunoModel($protesto->id_aluno);
        $p_resp = new PessoaModel($respon->id_pessoa);
        $a_pes = new PessoaModel($aluno->id_pessoa);
        $txt_emissao = UtilHelper::formatBr($protesto->data_emissao);
        $txt_vencimento = UtilHelper::formatBr($protesto->data_vencimento);    
        $txt_inclusao= UtilHelper::formataDataBr($this->dados['data_inclusao']);
        $txt_valor = UtilHelper::formatoReal($this->dados['valor']);
        $taxa = new TaxasModel($this->dados['id_taxas']);
        $txt_cpf = UtilHelper::formataCPF($p_resp->cpf);
        $txt_taxa = UtilHelper::minuscula($taxa->descricao);
        ?>
        <html>
            <head>
                <title>Protesto</title>
            </head>
            <style type="text/css">
                table tr td {
                    padding:4px;
                    font-size: 12px;
                    text-align: justify;
                    line-height: 30px;
                }
            </style>
            <body>
                
            <table cellspacing="0" width="100%" border="0" class="">
                <thead>
                    <tr>
                        <td align="center"><span style="font-size: 30px">RECIBO</span></td>
                    </tr>
                <br />
                <br />
                    <tr>
                        <td align="right"><strong><?php echo $txt_valor; ?></strong></td>
                    </tr>
                </thead>
                <br />
                <br />
                <tbody>
                       <tr>
                           <td>
                               Recebi do(a) senhor(a) <?php echo $p_resp->nome;?> CPF <?php echo $txt_cpf; ?>
                               o valor de  <?php echo $txt_valor; ?> referente a <?php echo $txt_taxa; ?> do Cartório
                               do protesto de contrato n° <?php echo $protesto->numero; ?> do(a) aluno(a) <?php echo $a_pes->nome; ?>
                               emitido em <?php echo $txt_emissao; ?> vencido em <?php echo $txt_vencimento; ?>.
                               
                           </td>
                       </tr>
                       <br />
                       <br />
                       <tr>
                           <td align="right">Macapá, <?php echo $txt_inclusao; ?></td>
                       </tr>
                       <br />
                       <br />
                       <br />
                       <tr>
                           <td align="center">_____________________________________</td>
                       </tr>
                       <tr>
                           <td align="center">Nilmara Gemaque</td>
                       </tr>
                </tbody>
            </table>
                <br />
                <br />
                <br />
            <div  style="border:1px dashed black;"></div>
            <br />
            <br />
            <br />
            <table cellspacing="0" width="100%" border="0" class="">
                <thead>
                    <tr>
                        <td align="center"><span style="font-size: 30px">RECIBO</span></td>
                    </tr>
                <br />
                <br />
                    <tr>
                        <td align="right"><strong><?php echo $txt_valor; ?></strong></td>
                    </tr>
                </thead>
                <br />
                <br />
                <tbody>
                       <tr>
                           <td>
                               Recebi do(a) senhor(a) <?php echo $p_resp->nome;?> CPF <?php echo $txt_cpf; ?>
                               o valor de  <?php echo $txt_valor; ?> referente a <?php echo $txt_taxa; ?> do Cartório
                               do protesto de contrato n° <?php echo $protesto->numero; ?> do(a) aluno(a) <?php echo $a_pes->nome; ?>
                               emitido em <?php echo $txt_emissao; ?> vencido em <?php echo $txt_vencimento; ?>.
                               
                           </td>
                       </tr>
                       <br />
                       <br />
                       <tr>
                           <td align="right">Macapá, <?php echo $txt_inclusao; ?></td>
                       </tr>
                       <br />
                       <br />
                       <br />
                       <tr>
                           <td align="center">_____________________________________</td>
                       </tr>
                       <tr>
                           <td align="center">Nilmara Gemaque</td>
                       </tr>
                </tbody>
            </table>
            </body>
        </html>
        <?php
        $saida = ob_get_contents();
        ob_end_clean();
        
        $saida = mb_convert_encoding($saida,'UTF-8', 'UTF-8');
        $mpdf = new mPDF();
        $mpdf->WriteHTML($saida);
        /*
         * F - salva o arquivo NO SERVIDOR
         * I - abre no navegador E NÃO SALVA
         * D - chama o prompt E SALVA NO CLIENTE
         */

        $mpdf->Output($arquivo, 'I');
    }
    
}

