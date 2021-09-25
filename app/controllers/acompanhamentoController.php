<?php

class Acompanhamento extends Controller {

    private $matricula;
    private $matriculaespera;
    private $aluno;
    private $turma;
    private $serie;
    private $status;
    private $_redirect;
    private $_msg;
    private $_session;

    public function init() {
        $this->matricula = new MatriculaModel();
        $this->matriculaespera = new MatriculaesperaModel();
        $this->turma = new TurmaModel();
        $this->aluno = new MalunoModel();
        $this->serie = new SerieModel();
        $this->status = new MatriculastatusModel();
        $this->_redirect = new RedirectorHelper();
        $this->_msg = new MsgHelper();
        $this->_session = new SessionHelper();
    }

    public function index_action() {
        if($this->_session->checkSession("anobase")){
            $anoselecionado = $_SESSION['anobase'];
            $txt_ano = new AnoModel($anoselecionado);
            $this->addNavegacao(array("Acompanhamento $txt_ano->descricao" => "acompanhamento/index"));
            
            $anoletivo = new AnoletivoModel();
            $anolet = $anoletivo->listarPorAno($anoselecionado);

            $ct = -1;
            foreach ($anolet as $val){
                $ct++;
               $array[$ct] = $this->serie->listarPorAnoletivo($val['id_ano_letivo']);
               foreach ($array[$ct] as $key){
                   $array_serie[] = $key;
               }
            }

            $serie = array();

            if($array_serie){
                $serie = $array_serie;
            }

            $cont = -1;
            
            foreach ($serie as $ser){
                $cont++;
                $array_turmas[$cont] =  $this->turma->listarPorSerieAtivos($ser['id_serie']);
                foreach ($array_turmas[$cont] as $key){
                    
                    $vmat = $this->matricula->listarPorTurmaAtivos($key['id_turma']);
                    $vesp = $this->matriculaespera->listarPorTurma($key['id_turma']);
                    
                    if($vmat){
                        foreach ($vmat as $vmt){
                            $tudo[]  = $vmt;
                        }
                    }
                    if($vesp){
                        foreach ($vesp as $vsp){
                            $tudo[]  = $vsp;
                        }
                    }
                    
                    $turmas[] = $key;
                }
            }
            
            function cd($a, $b) {
                return strcmp($a["data_cadastro"], $b["data_cadastro"]);
            }
            
            
            uasort($tudo, "cd");
            $dados['tudo'] = array_reverse($tudo);
            $dados['turma'] = $turmas;
            $this->view($dados);
            
        }  else {
            $this->_msg->informacao("Por favor, selecione o ano base em que deseja trabalhar!");
            $this->_redirect->goToControllerAction('acompanhamento', 'selecioneano');
        }
    }
    
    public function selecioneano(){
        $ano = $this->getParam("ano");
        if($ano){
            $txt_ano = new AnoModel($ano);
            $this->_session->createSession("anobase", $ano);
            $this->_msg->sucesso("Ano <strong>$txt_ano->descricao</strong> foi selecionado!");
            $this->_redirect->goToController('acompanhamento');
        }  else {
            $this->_session->deletarSession('anobase');
            $ano = new AnoModel();
            $dados['ano'] = $ano->listar();
            $this->view($dados);
        }
    }
    
    public function timeline(){
        date_default_timezone_set('America/Belem');
        $data_atual = date('d-M-Y');
        $sql = $this->matricula->listar();
        $esp = $this->matriculaespera->listar();

        function cd($a, $b) {
            return strcmp($a["data_cadastro"], $b["data_cadastro"]);
        }

        $tudo = array_merge($sql, $esp);

        uasort($tudo, "cd");
        $view_tudo = array_reverse($tudo);
        ?>
        <ul class="timeline">
            <li class="time-label">
                <span class="bg-teal">
                    <?php echo $data_atual; ?>
                </span>
            </li>
            <?php foreach ($view_tudo as $sql){
                $turm = new TurmaModel($sql['id_turma']); 
                $msts = new MatriculastatusModel($sql['id_matricula_status']);
                $aluno = new MalunoModel($sql['id_m_aluno']);
                $usu = new UsuarioModel($sql['id_usuario']);
                $pes = new PessoaModel($usu->id_pessoa);
                $txt_status = "";
                $txt_icon = "";
                $txt_descricao = "";
                
                if($msts->chave == "MT"){
                    $txt_descricao = "Matricula";
                    $txt_icon = "fa-user-plus";
                    $bg_color = "bg-green";
                }
                if($msts->chave == "RMT"){
                    $txt_descricao = "Rematrícula";
                    $txt_icon = "fa-handshake-o";
                    $bg_color = "bg-aqua";
                }
                if($msts->chave == "RV"){
                    $txt_descricao = "Reserva";
                    $txt_icon = "fa-calendar";
                    $bg_color = "bg-yellow";
                }
                
                if($sql['status'] == "A"){
                    $txt_descricao = "Inclusão na lista de espera";
                    $txt_icon = "fa-spinner fa-pulse fa-fw";
                    $bg_color = "bg-red";
                }
                if($sql['status'] == "T"){
                    $txt_descricao = "Transferência";
                    $txt_icon = "fa-refresh";
                    $bg_color = "bg-purple";
                }
                
                
                $data = new DateTime($sql['data_cadastro']);
                $data2 = new DateTime($view_tudo[$cont]['data_cadastro']);
                $data_queveio = $data->format("Y-m-d");
                $data_queveio_hora = $data->format("H:i:s");
                
                $data_ultima = $data2->format("Y-m-d");
            ?>
            <?php if($data_ultima != $data_queveio){
                $nd = new DateTime($sql['data_cadastro']);
                ?>
            <li class="time-label">
                <span class="bg-teal">
                    <?php echo $nd->format("d - M - Y"); ?>
                </span>
            </li>
            <?php } ?>
            
            <li>
                <i class="fa <?php echo $txt_icon; ?> <?php echo $bg_color; ?>"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> <?php echo $data_queveio_hora; ?></span>
                    <h3 class="timeline-header"><a href="#"><?php echo UtilHelper::formataNome($pes->nome); ?></a> realizou uma <span class="badge <?php echo $bg_color; ?>"><?php echo $txt_descricao; ?></span> </h3>
                    <div class="timeline-body">
                       Para o aluno(a) <?php echo $aluno->nome; ?> na turma <?php echo $turm->descricao ?>
                    </div>
                </div>
              
            </li>
            <?php } ?>
            <li>
              <i class="fa fa-clock-o bg-gray"></i>
            </li>
          </ul>    
        <?php
    }
}

