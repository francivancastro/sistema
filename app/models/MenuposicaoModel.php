<?php

class MenuposicaoModel extends Model{
    public $_tabela = 'menu_posicao';
    
    public function busca($id){
        return $this->ler('id_menu_posicao='.$id);
    }
    
    public function atualizarMenuposicao(array $dados, $id) {
        return $this->atualizar($dados, 'id_menu_posicao='.$id);
    }

        public function listarMenuposicao(){
        return $this->ler(null);
    }
    
    public function inserirMenuposicao(array $dados) {
        return $this->inserir($dados);
    }
    
    public function deletarMenuposicao($id) {  
        return $this->deletar('id_menu_posicao='.$id);
    }
}
