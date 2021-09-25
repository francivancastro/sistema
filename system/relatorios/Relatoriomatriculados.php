<?php

class Relatoriomatriculados{
    
    public $dados;
    public $status;
    public function __construct($dados = null, $status = null) {
        
        if($dados){
            $this->dados = $dados;
        }
        if($status){
            $this->status = $status;
        }
        
    }

    public function gerarRelatorio(){
        ob_start();
        
        ?>
        <html>
            <head>
                <title>Relatório</title>
                <link rel="stylesheet" href="/sistema/public/css/bootstrap.min.css">
            </head>
            <style type="text/css">
                table.tabela1 {
                    border-top:1px solid #B7CAE6;
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
            
            <?php foreach ($this->dados as $turma) { ?>
                <table cellspacing="0" width="100%" border="0">
                <thead>
                    <tr>
                        <td style="width: 200px"><img width="150" src="/sistema/public/img/doc_logo.png" alt="" /></td>
                        <td align="" valign="bottom" ><span style="font-size: 18px">Relatório de Matriculados / Rematriculados</span></td>
                    </tr>
                    
                </thead>
            </table>
                <br />
                <div style="page-break-after: always">
            <table cellspacing="0" width="100%" border="0" class="">
                <thead>
                    <tr>
                        <th style="text-align: left">Turma: <?php echo $turma['descricao'];?></th>
                        <th style="text-align: right"></th>
                    </tr>
                </thead>
            </table>
                <br />
            <table cellspacing="0" width="100%" border="0" class="tabela1">
                <thead>
                    <tr>
                        <th style="text-align: left">Nº</th>
                        <th style="text-align: left">Nome</th>
                        <th style="text-align: left">Matrícula</th>
                        <th style="text-align: left">Situação</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                
                $mt = new MatriculaModel();
                $matriculas = $mt->listarPorTurma($turma['id_turma']);
                $contador = 0;
                $matriculados = array();
                    foreach ($matriculas as $matri){
                        $aluno = new MalunoModel($matri['id_m_aluno']);
                        $matri['id_aluno'] = $aluno->nome;
                        $matriculados[] = $matri;
                    }
                    
                    $matriculados = UtilHelper::ordena($matriculados, "id_aluno");
                    foreach ($matriculados as $matricula){
                        if($matricula['situacao'] == 'A'){
                        if($this->status){
                            if($matricula['id_matricula_status'] == $this->status){
                        $contador++;
                        $aluno = new MalunoModel($matricula['id_m_aluno']);
                        $status = new MatriculastatusModel($matricula['id_matricula_status']);
                ?>
                   <tr>
                       <td><?php echo $contador;?></td>
                       <td><?php echo $aluno->nome;?></td>
                       <td><?php echo $aluno->matricula;?></td>
                       <td><?php echo $status->descricao;?></td>
                   </tr>
                    <?php }} else {
                        if($matricula['id_matricula_status'] != "3"){
                            $contador++;
                        $aluno = new MalunoModel($matricula['id_m_aluno']);
                        $status = new MatriculastatusModel($matricula['id_matricula_status']);
                        
                        ?>
                    <tr>
                        <td><?php echo $contador;?></td>
                        <td><?php echo $aluno->nome;?></td>
                        <td><?php echo $aluno->matricula;?></td>
                        <td><?php echo $status->descricao;?></td>
                    </tr>
                    <?php }} ?>
                    
                    <?php }} ?>
                </tbody>
            </table>
                </div>
            <?php } ?>
            </body>
        </html>
        <?php
        $saida = ob_get_contents();
        ob_end_clean();
        
        $saida = mb_convert_encoding($saida,'UTF-8', 'UTF-8');
        $mpdf = new mPDF();
        $mpdf->WriteHTML($saida);
        $mpdf->AddPage();
        /*
         * F - salva o arquivo NO SERVIDOR
         * I - abre no navegador E NÃO SALVA
         * D - chama o prompt E SALVA NO CLIENTE
         */

        $mpdf->Output($arquivo, 'I');
    }
    
}

