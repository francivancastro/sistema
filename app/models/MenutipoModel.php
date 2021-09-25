<?php

class MenutipoModel extends Model{
    public $_tabela = 'menu_tipo';
    
    public function busca($id){
        return $this->ler('id_menu_tipo='.$id);
    }
    
    public function atualizarMenutipo(array $dados, $id) {
        return $this->atualizar($dados, 'id_menu_tipo='.$id);
    }

        public function listarMenutipo(){
        return $this->ler(null);
    }
    
    public function inserirMenutipo(array $dados) {
        return $this->inserir($dados);
    }
    
    public function deletarMenutipo($id) {  
        return $this->deletar('id_menu_tipo='.$id);
    }
    
}
