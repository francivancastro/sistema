<?php

class Relatorio extends Controller{
    
    public $relatorio, $_msg;


    public function init() {
        parent::init();
        $this->relatorio = new RelatorioModel();
        $this->_msg = new MsgHelper();
    }

    public function index_action(){
        $this->addNavegacao(array('Relatório' => "relatorio/index"));
        $this->view();
    }
    
    public function protestoinadimplencia(){
        $this->addNavegacao(array('Relatório' => "relatorio/index", "Inadimplência de Protesto" => "relatorio/protestoinadimplencia"));
        if(!empty($_POST)){
       
            $ano = ($_POST['ano'] != null ? "AND (a.ano_base = {$_POST['ano']})" : "");
            $empresa = ($_POST['id_empresa'] != null ? "AND (a.id_empresa = {$_POST['id_empresa']})" : "");
            $responsavel = ($_POST['responsavel'] != null ? "AND (d.nome LIKE '%{$_POST['responsavel']}%')" : "");
            $aluno = ($_POST['aluno'] != null ? "AND (e.nome LIKE '%{$_POST['aluno']}%')" : "");
            $status = ($_POST['id_protesto_status'] != null ? "AND (a.id_protesto_status = {$_POST['id_protesto_status']})" : "");
            if($_POST['data2']){
                $data2 = UtilHelper::formatUs($_POST['data2']);
            } else {
                $data2 = date('Y-m-d');
            }
            $novadata = UtilHelper::formatUs($_POST['data1']);
            $data = ($_POST['data1'] != null ? "AND (a.data_vencimento BETWEEN '{$novadata}' AND '{$data2}')" : "");
            
            
            
            $sql = "SELECT * FROM protesto a, responsavel b, aluno c, pessoa d, pessoa e
                    WHERE a.id_responsavel = b.id_responsavel
                    AND (a.id_aluno = c.id_aluno)
                    AND (c.id_pessoa = e.id_pessoa)
                    AND (a.id_protesto_status = 1)
                    AND (b.id_pessoa = d.id_pessoa) {$ano} {$empresa} {$responsavel} {$aluno} {$data} ORDER BY  a.id_empresa ASC, a.data_vencimento ASC";
            // $this->mostrar($sql);die();
            $result = $this->relatorio->pesquisar($sql);
            // $this->mostrar($result);die();
            if($result){
                $rel = new RelatorioInadiplenciaprotesto($result);
                $rel->gerarRelatorio(); 
            } else {
                $this->_msg->informacao("Nenhun registro encontrado!");
            }
            $this->view();
        } else {
            $this->view();
        }
    }
    
    
    public function protestoadimplente(){
        $this->addNavegacao(array('Relatório' => "relatorio/index", "Protesto Adimplente" => "relatorio/protestoadimplente"));
        if(!empty($_POST)){
       
            $ano = ($_POST['ano'] != null ? "AND (a.ano_base = {$_POST['ano']})" : "");
            $empresa = ($_POST['id_empresa'] != null ? "AND (a.id_empresa = {$_POST['id_empresa']})" : "");
            $responsavel = ($_POST['responsavel'] != null ? "AND (d.nome LIKE '%{$_POST['responsavel']}%')" : "");
            $aluno = ($_POST['aluno'] != null ? "AND (e.nome LIKE '%{$_POST['aluno']}%')" : "");
            $status = ($_POST['id_protesto_status'] != null ? "AND (a.id_protesto_status = {$_POST['id_protesto_status']})" : "");
            if($_POST['data2']){
                $data2 = UtilHelper::formatUs($_POST['data2']);
            } else {
                $data2 = date('Y-m-d');
            }
            $novadata = UtilHelper::formatUs($_POST['data1']);
            $data = ($_POST['data1'] != null ? "AND (a.data_vencimento BETWEEN '{$novadata}' AND '{$data2}')" : "");
            
            
            
            $sql = "SELECT * FROM protesto a, responsavel b, aluno c, pessoa d, pessoa e
                    WHERE a.id_responsavel = b.id_responsavel
                    AND (a.id_aluno = c.id_aluno)
                    AND (c.id_pessoa = e.id_pessoa)
                    AND (a.id_protesto_status = 2)
                    AND (b.id_pessoa = d.id_pessoa) {$ano} {$empresa} {$responsavel} {$aluno} {$data} ORDER BY  a.id_empresa ASC, a.data_vencimento ASC";
            // $this->mostrar($sql);die();
            $result = $this->relatorio->pesquisar($sql);
            // $this->mostrar($result);die();
            if($result){
                $rel = new Relatorioadimplente($result);
                $rel->gerarRelatorio(); 
            } else {
                $this->_msg->informacao("Nenhun registro encontrado!");
            }
            $this->view();
        } else {
            $this->view();
        }
    }
    
    
    
}