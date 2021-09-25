<?php

class Relatorioadimplente {
    
    public $dados;
    public function __construct($dados = null) {
        if($dados){
            $this->dados = $dados;
        }
    }

        public function gerarRelatorio(){
        ob_start();
        ?>
        <html>
            <head>
                <title>Protesto</title>
            </head>
            <style type="text/css">
                table tr td {
                    padding:4px;
                    font-size: 12px;
                }
            </style>
            <body>
                
            <table cellspacing="0" width="100%" border="0" class="">
            <thead>
                <tr>
                    <td><img width="150" src="/sistema/public/img/doc_logo.png" alt="" /></td>
                    <td align="center" valign="bottom" ><span style="font-size: 30px">Relatório de Adimplêntes</span></td>
                </tr>
            </thead>
            </table>
                <hr />
            <table cellspacing="0" width="100%" border="1">
                <thead>
                    <tr>
                        <th>Ano Base</th>
                        <th>Empresa</th>
                        <th>Aluno</th>
                        <th>Responsável</th>
                        <th>Vencimento</th>
                        <th>Valor Protestado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->dados as $dados){ 
                    $total = count($this->dados);
                    $alu = new AlunoModel($dados['id_aluno']);
                    $resp = new ResponsavelModel($dados['id_responsavel']);
                    $p_alu = new PessoaModel($alu->id_pessoa);
                    $p_res = new PessoaModel($resp->id_pessoa);
                    $emp = new EmpresaModel($dados['id_empresa']);
                    $valorTprotestado += $dados['valor_protesto'];
                    ?>
                    <tr>
                        <td align="center" ><?php echo $dados['ano_base'];?></td>
                        <td><?php echo $emp->empresa_nome; ?></td>
                        <td><?php echo $p_alu->nome;?></td>
                        <td><?php echo $p_res->nome;?></td>
                        <td align="center"><?php echo UtilHelper::formatBr($dados['data_vencimento']);?></td>
                        <td align="center"><?php echo UtilHelper::formatoReal($dados['valor_protesto']);?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
                <br />
                <table cellspacing="0" width="50%" border="1" align="right">
                    <tr>
                        <td>Quantidade: <?php echo $total; ?> </td>
                        <td align="center">Valor Total Protestado</td>
                        <td align="center"><strong><?php echo UtilHelper::formatoReal($valorTprotestado);?></strong></td>
                    </tr>
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

