<?php

class LayoutHelper {
    
    protected $_layout = "layout";
    protected $_content = "";
    protected $breadcrumbs = "";
    protected $nav = array();


    protected function setLayout($nome){
        $this->_layout = $nome;
    }
    
    protected function getLayout(){
        return $this->_layout;
    }
    
    
    public function view($controller, $action, $vars) {
        if(is_array($vars) && count($vars) > 0){
            extract( $vars, EXTR_PREFIX_ALL, 'view' );
        }
        ob_start();
        require_once( VIEWS. $controller."/". $action . '.phtml' );
        $this->_content = ob_get_contents();
        ob_end_clean();
        $this->breadcrumbs();
        require_once( LAYOUT."/". $this->_layout . '.phtml' );
    }
    
    public function breadcrumbs(){
        $array = $this->getNav();
        $it = end($array);
        $ident = array_keys($array);
?>      
        <?php if($ident){?>
        <h1><?php echo $ident[0] ?> <small><?php echo $ident[1] ?></small> </h1>
        <?php } else{ ?>
        <h1>Painel Principal</h1>
        <?php }  ?>

        <ol class="breadcrumb">
            <li><a href="/sistema/index/index"><i class="fa fa-home"></i> Home</a></li>
            <?php foreach ($array as $item => $url){ ?>
            <li class="<?php ($url == $it) ? print 'active' : print''; ?>">
                <?php if($url == $it){ echo $item;} else { ?>
                <a href="/sistema/<?php echo $url?>"><?php echo $item; ?></a>
                <?php } ?>
            </li>
            <?php } ?>
            
        </ol>
<?php
        $this->breadcrumbs = ob_get_contents();
        ob_end_clean();
    }
    
    public function setNav(Array $items = array()){
        return $this->nav = $items;
    }
    
    public function getNav(){
        return $this->nav;
    }
}
