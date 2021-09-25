<?php

class Fichamento extends Controller {

    private $aluno;
    private $matricula;
    private $ano;
    private $_redirect;
    private $_msg;

    public function init() {
        $this->ano = new AnoModel();
        $this->matriculaespera = new MatriculaesperaModel();
        $this->matricula= new MatriculaModel();
        $this->aluno = new MalunoModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
    }

    public function index_action() {
        $ano = $this->ano->listar();
        
        if(!empty($_POST)){
            
            $matricula = array();
            foreach ($_POST['id_turma_de'] as $k => $t){
                $alunos = $this->matricula->listarPorTurma($t);
                
                foreach ($alunos as $al){
                    if($al['id_matricula_status'] != 3){
                        $matricula[$k][] = $al;
                    }
                }
            }
            date_default_timezone_set('America/Belem');
            $data = new DateTime();
            $this->matricula->transacao();
            try {
                foreach ($matricula as $keyturma => $valueturma){
                    foreach ($valueturma as $turma){
                        $_POST['id_m_aluno'] = $turma['id_m_aluno'];
                        $_POST['id_turma'] = $_POST['id_turma_para'][$keyturma];
                        $_POST['id_matricula_status'] = '3';
                        $_POST['data'] = $data->format('Y-m-d H:i:s');
                        $_POST['id_usuario'] = '4';
                        $_POST['situacao'] = 'A';
                        if(!$this->matricula->validar($_POST)){
                            $this->matricula->salvar();
                        } else {
                            throw new Exception("Falha ao salvar!");
                        }
                     }
                }
                $this->matricula->save();
                $this->_msg->sucesso("Fichamento Realizado com Sucesso!");
                $this->_redirect->goToController('fichamento');
            } catch (Exception $ex) {
                $this->matricula->refazer();
                $this->_msg->erro($ex->getMessage());
                $this->_redirect->goToController('fichamento');
            }
        }
        
        $datas['ano'] = $ano;
        $this->view($datas);
    }
    
    
    

    public function turmasde() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->matricula->pesquisar("SELECT e.id_turma, e.descricao FROM ano a, ano_letivo b, segmento c, serie d, turma e
            WHERE (e.id_serie = d.id_serie)
            AND (d.id_ano_letivo = b.id_ano_letivo)
            AND (b.id_segmento= c.id_segmento)
            AND (b.id_ano = a.id_ano)
            AND (e.situacao = 'A')
            AND (a.id_ano = {$id})");
            foreach ($sql as $turma){
                $alunos = $this->matricula->listarPorTurmaAtivos($turma['id_turma']);
            ?>
            <script type="text/javascript">
            $(document).ready(function(){
                $('.remove').click(function (){
                    $(this).parents('li').remove();
                    return false;
                });
            });
            </script>
            <li class="" style="">
                <span class="handle ui-sortable-handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </span>
                <input type="hidden" name="id_turma_de[]" value="<?= $turma['id_turma']; ?>"/>
                <span class="text"><?= $turma['descricao']; ?></span>
                <small class="label label-danger" style="width: 20px"><?= count($alunos); ?></small>
                <div class="tools">
                    <i class="fa fa-trash-o remove"></i>
                </div>
            </li>
            <?php
            }
        }
    }
    public function turmaspara() {
        $id = $this->getParam('id');
        if($id){
            $sql = $this->matricula->pesquisar("SELECT e.id_turma, e.descricao FROM ano a, ano_letivo b, segmento c, serie d, turma e
            WHERE (e.id_serie = d.id_serie)
            AND (d.id_ano_letivo = b.id_ano_letivo)
            AND (b.id_segmento= c.id_segmento)
            AND (b.id_ano = a.id_ano)
            AND (e.situacao = 'A')
            AND (a.id_ano = {$id})");
            foreach ($sql as $turma){
            ?>
            <script type="text/javascript">
            $(document).ready(function(){
                $('.remove').click(function (){
                    $(this).parents('li').remove();
                    return false;
                });
            });
            </script>
            <li class="" style="">
                <span class="handle ui-sortable-handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </span>
                <input type="hidden" name="id_turma_para[]" value="<?= $turma['id_turma']; ?>"/>
                <span class="text"><?= $turma['descricao']; ?></span>
                <div class="tools">
                    <i class="fa fa-trash-o remove"></i>
                </div>
            </li>
            <?php
            }
        }
    }
    
}