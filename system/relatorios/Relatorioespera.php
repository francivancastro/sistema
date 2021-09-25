<?php

class Relatorioespera {

    public $dados;

    public function __construct($dados = null) {
        if ($dados) {
            $this->dados = $dados;
        }
    }

    public function gerarRelatorio() {
        ?>
        <html>
            <head>
                <title>Relatório</title>
                <link rel="stylesheet" href="/sistema/public/css/bootstrap.min.css">
            </head>

            <table cellspacing="0" width="100%" border="1" class="tabela1">
                <thead>
                    <tr>
                        <th style="text-align: left">Visitante</th>
                        <th style="text-align: left">Parentesco</th>
                        <th style="text-align: left">Cliente</th>
                        <th style="text-align: left">Aluno</th>
                        <th style="text-align: left">Data Nascimento</th>
                        <th style="text-align: left">Turma</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->dados as $turma) { ?>


                        <?php
                        $mt = new MatriculaesperaModel();
                        $espera = $mt->listarPorTurma($turma['id_turma']);
                        $contador = 0;
                        $listaespera = array();
                        foreach ($espera as $espe) {
                            $visi = new VisitanteModel($espe['id_visitante']);
                            $espe['cliente'] = $visi->cliente;
                            $listaespera[] = $espe;
                        }

//                    function nome($a, $b) {
//                        return strcmp($a["cliente"], $b["cliente"]);
//                    }
//                    usort($listaespera, "nome");
                        foreach ($listaespera as $esp) {
                            if ($esp['status'] == "A") {
                                $aluno = new MalunoModel($esp['id_m_aluno']);
                                $visi = new VisitanteModel($esp['id_visitante']);
                                $txt_cliente = "";
                                if ($visi->cliente == "S") {
                                    $txt_cliente = "Sim";
                                }
                                if ($visi->cliente == "N") {
                                    $txt_cliente = "Não";
                                }
                                ?>
                                <tr>
                                    <td><?php echo $visi->nome; ?></td>
                                    <td><?php
                                        if ($visi->grau_parentesco == "Pai") {
                                            echo 1;
                                            } elseif ($visi->grau_parentesco == "Mãe") {
                                            echo 2;
                                            } elseif ($visi->grau_parentesco == 'Avô(ó)') {
                                            echo 8;
                                            } elseif ($visi->grau_parentesco = "Irmão(ã)") {
                                            echo 6;
                                            } elseif ($visi->grau_parentesco == "Tio(a)") {
                                            echo 12;
                                        } else {
                                            echo $visi->grau_parentesco;
                                        }
                                        ?></td>
                                    <td><?php echo $txt_cliente; ?></td>
                                    <td><?php echo $aluno->nome; ?></td>
                                    <td><?php echo $aluno->data_nascimento; ?></td>
                                    <td><?php echo $turma['descricao']; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>

                    <?php } ?>
                </tbody>
            </table>
        </html>
        <?php
        exit();
    }

}
