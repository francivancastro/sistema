<?php

class PaginadorHelper{
    
    public function paginacao($view_numero){
   
            $sis = new System();
    ?>
                <ul class="pagination pagination-sm no-margin pull-right">
    <?php
        $paginaAtual = $sis->getParam('page');
        if(!$paginaAtual){
            $paginaAtual = 1;
        }
        $avan = $paginaAtual +1;
        $volt = $paginaAtual -1;
        
        if($avan > $view_numero){
            $avan = $view_numero;
        }
        if($volt < 1){
            $volt = 1;
        }
        if($paginaAtual > 1){
    ?>
            <li><a href="/sistema/<?php echo $sis->_controller; ?>/<?php echo $sis->_action; ?>/page/<?php echo $volt; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
    <?php
        }
        
        for($i = $paginaAtual - 5, $limiteDeLinks = $i + 9; $i <= $limiteDeLinks; $i++){
            if($i < 1){
                $i = 1;
                $limiteDeLinks = 10;
            }
            if($limiteDeLinks > $view_numero){
                $limiteDeLinks = $view_numero;
                $i = $limiteDeLinks - 9;
            }
            if($i < 1){
                $i = 1;
                $limiteDeLinks = $view_numero;
            }
            $active = "";
            if($paginaAtual == $i){
                $active = "active";
            }
            echo   "<li class='$active' ><a href='/sistema/{$sis->_controller}/{$sis->_action}/page/$i'>$i</a></li>";
        }

        if($paginaAtual < $view_numero){
    ?>
                    <li><a href="/sistema/<?php echo $sis->_controller; ?>/<?php echo $sis->_action; ?>/page/<?php echo $avan; ?>"><span aria-hidden="true" aria-label="Next">&raquo;</span></a></li>
    <?php } ?>        
                </ul>
            
    <?php } 
}

