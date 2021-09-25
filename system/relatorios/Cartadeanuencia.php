<?php

class Cartadeanuencia {
    
    public $dados;
    public function __construct($dados = null) {
        if($dados){
            $this->dados = $dados;
        }
    }

        public function gerarRelatorio(){
        ob_start();
        $protesto = new ProtestoModel($this->dados['id_protesto']);
        
        $emp = new EmpresaModel($this->dados['id_empresa']);
        $ef = new EmpresarefModel();
        $ee = $ef->listarPorEmpresa($this->dados['id_empresa']);
        $ende = New EnderecoModel($ee[0]['id_endereco']);
        $bae = new BairroModel($ende->id_bairro);
        $munic = new MunicipioModel($bae->id_municipio);
        $estad = new EstadoModel($munic->id_estado);
        
        $respon = new ResponsavelModel($protesto->id_responsavel);
        $aluno = new AlunoModel($protesto->id_aluno);
        $p_resp = new PessoaModel($respon->id_pessoa);
        $a_pes = new PessoaModel($aluno->id_pessoa);
        $txt_emissao = UtilHelper::formatBr($protesto->data_emissao);
        $txt_vencimento = UtilHelper::formatBr($protesto->data_vencimento);    
        $txt_impri= UtilHelper::formataDataBr(date("Y-m-d"));
        $txt_valor = UtilHelper::formatoReal($this->dados['valor_protesto']);
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
                    font-size: 18px;
                    text-align: justify;
                    line-height: 35px;
                }
            </style>
            <body>
             <table cellspacing="0" width="100%" border="0" class="">
            <thead>
                <tr>
                    <td><img width="150" src="/sistema/public/img/doc_logo.png" alt="" /></td>
                   
                </tr>
            </thead>
            </table>   
            <table cellspacing="0" width="100%" border="0" class="">
                <thead>
                    <tr>
                        <td align="center"><span style="font-size: 18px; font-weight: bold">CARTA DE ANUÊNCIA</span></td>
                    </tr>
                </thead>
                <br />
                <br />
                <tbody>
                       <tr>
                           <td>
<?php echo $emp->empresa_nome;?> situada na <?php echo $ende->endereco;?>, nº <?php echo $ende->numero;?> Bairro
do <?php echo $bae->nome;?> CEP <?php echo UtilHelper::formataCep($ende->cep);?>, CNPJ <?php echo UtilHelper::formataCNPJ($emp->cnpj);?>, por seu representante
legal abaixo identificado e assinado, declara para todos os fins e efeitos
de direito que o título de crédito de responsabilidade de <?php echo $p_resp->nome; ?>,
CPF <?php echo $txt_cpf; ?>, sob nº de contrato <?php echo $protesto->numero; ?> aluno(a) <?php echo $a_pes->nome; ?> 
emitido em <?php echo $txt_emissao; ?>, vencido <?php echo $txt_vencimento ?>, no valor de <?php echo $txt_valor; ?> 
objeto de protesto junto ao Tabelião de Notas e Protesto deste cartório,
está devidamente quitado.
                           </td>
                       </tr>
                       <br />
                       <br />
                       <tr>
                           <td align="right">Macapá, <?php echo $txt_impri; ?></td>
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
                       <tr>
                           <td align="center">CPF 796.562.932-15</td>
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

