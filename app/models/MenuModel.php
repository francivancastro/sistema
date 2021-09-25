<?php

class MenuModel extends Model{
    public $_tabela = 'menu';
    
    public function busca($id){
        return $this->ler('id_menu='.$id);
    }
    
    public function atualizarMenu(array $dados, $id) {
        return $this->atualizar($dados, 'id_menu='.$id);
    }

        public function listarMenu(){
        return $this->ler(null);
    }
    
    public function inserirMenu(array $dados) {
        return $this->inserir($dados);
    }
    
    public function deletarMenu($id) {  
        return $this->deletar('id_menu='.$id);
    }
}
