<?php

class NavegacaoHelper {
    
    protected $_layout = "layout";
    protected $breadcrumbs = "";


    protected function setLayout($nome){
        $this->_layout = $nome;
    }
    
    protected function getLayout(){
        return $this->_layout;
    }
    
    public function breadcrumbs(){
        
        ob_start();
?>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="#">Produtos</a></li>
            <li class="active">Index</li>
        </ol>
<?php
        $this->breadcrumbs = ob_get_contents();
        ob_end_clean();
        require_once( LAYOUT."/". $this->_layout . '.phtml' );
    }
}
