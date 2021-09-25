<?php

class AdminModel extends Model {
    
    public function listarDados(){
        return $this->ler(null);
    }
    
    public function deletarDados($id) {  
        return $this->deletar(' id='.$id);
    }
}
