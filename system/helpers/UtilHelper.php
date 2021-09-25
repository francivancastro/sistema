<?php

class UtilHelper {
    
    public static function maiuscula($string){
        return strtoupper($string);
    }
    
    public static function minuscula($string){
        return strtolower($string);
    }
    
    public static function primeiraMaiuscula($str){
        return ucfirst($str);
    }
    
    public static function formataCNPJ($cnpj){
        if($cnpj){
            $formatado  = substr( $cnpj,  0,  2 ) . '.';
            $formatado .= substr( $cnpj,  2,  3 ) . '.';
            $formatado .= substr( $cnpj,  5,  3 ) . '/';
            $formatado .= substr( $cnpj,  8,  4 ) . '-';
            $formatado .= substr( $cnpj, 12, 14 ) . '';
            return $formatado;
        }
        return false;
    }
    public static function formataCPF($cpf){
        if($cpf){
            $formatado  = substr( $cpf, 0, 3 ) . '.';
            $formatado .= substr( $cpf, 3, 3 ) . '.';
            $formatado .= substr( $cpf, 6, 3 ) . '-';
            $formatado .= substr( $cpf, 9, 2 ) . '';
            return $formatado;
        }
        return false;
    }
    
    public static function formataIE($ie){
        if($ie){
            $formatado  = substr( $ie, 0, 3 ) . '/';
            $formatado .= substr( $ie, 3, 7 ) . '';
            return $formatado;
        }
        return false;
    }
    
    public static function formatBr($date){
        $date = explode('-', $date);
        $date = $date[2].'/'.$date[1].'/'.$date[0];
        return $date;
    }
    
    public static function formatUs($date){
        $date = explode('/', $date);
        $date = $date[2].'-'.$date[1].'-'.$date[0]; 
        return $date;
    }
    
    public static function formataCep($cep){
        if($cep){
            $formatado  = substr( $cep, 0, 2 ) . '.';
            $formatado .= substr( $cep, 2, 3 ) . '-';
            $formatado .= substr( $cep, 5, 3 ) . '';
            return $formatado;
        }
        return false;
    }
    
    public static function formataNome($nome){
        if($nome && is_string($nome)){
            $words = explode(" ", $nome);
            $exibeprimeira = $words[0];
            $exibeultima = $words[count($words)-1];
            $abrevia = $exibeprimeira .' '. $exibeultima;
            return $abrevia;
        }
        return false;
    }
    
     public static function formataTelefone($fone){
        if($fone){
            $formatado  = substr( $fone, 0, 0 ) . '(';
            $formatado .= substr( $fone, 0, 2 ) . ')';
            $formatado .= substr( $fone, 2, 4 ) . '-';
            $formatado .= substr( $fone, 6, 4 ) . '';
            return $formatado;
        }
        return false;
    }
    
    public static function formataCelular($fone){
        if($fone){
            $formatado  = substr( $fone, 0, 0 ) . '(';
            $formatado .= substr( $fone, 0, 2 ) . ')';
            $formatado .= substr( $fone, 2, 5 ) . '-';
            $formatado .= substr( $fone, 7, 4 ) . '';
            return $formatado;
        }
        return false;
    }
    
    public static function formatoReal($valor) {
        if($valor){
            $formatado = 'R$ '.number_format($valor, 2, ',', '.');
            return $formatado;
        }
        return false;
    }
    public static function formatoRs($valor) {
        if($valor){
            $formatado = number_format($valor, 2, ',', '.');
            return $formatado;
        }
        return false;
    }
    
    public static function formataDataBr($date){
        if($date){
            $date = explode('-', $date);
            $mes = self::meses(); 
            $date = $date[2].' de '.$mes[$date[1]].' de '.$date[0];
            return $date;
        }
        return false;
    }
    
    public static function meses (){
        return array(
            '01' => 'Janeiro', 
            '02' => 'Fevereiro', 
            '03' => 'Março', 
            '04' => 'Abril', 
            '05' => 'Maio', 
            '06' => 'Junho',
            '07' => 'Julho', 
            '08' => 'Agosto', 
            '09' => 'Setembro',
            '10' => 'Outubro', 
            '11' => 'Novembro', 
            '12' => 'Dezembro'
        );
    }
    
    public static function anos(){
        return array(
            '2008',
            '2009',
            '2010',
            '2011',
            '2012',
            '2013',
            '2014',
            '2015',
            '2016',
            '2017',
            '2018',
            '2019',
            '2020',
        );
    }
    
    public static function bandeiras(){
        return array(
            'Visa Electron',
            'MasterCard',
            'Elo',
            'Amex',
        );
    }
    
    public static function bancos(){
        return array(
            'Banco do Brasil',
            'Bradesco',
            'Banco da Amazonia',
            'Caixa Economica Federal',
            'Itau',
            'Santander',
        );
    }
    
    public static function limitartexto($texto, $limite){
        $texto = substr($texto, 0, strrpos(substr($texto, 0, $limite), ' ')) . '...';
        return $texto;
    }
    
    public static function listaTurno (){
        return array(
            'Manhã',
            'Tarde',
        );
    }
    
    public static function listaParentes (){
        return array(
            'Pai',
            'Mãe',
            'Irmão(ã)',
            'Padrasto',
            'Madrasta',
            'Padrinho',
            'Madrinha',
            'Tio(a)',
            'Avô(ó)',
        );
    }
    
    public static function listaEstados (){
        return array(
            "AC"=>"Acre",
            "AL"=>"Alagoas",
            "AM"=>"Amazonas",
            "AP"=>"Amapá",
            "BA"=>"Bahia",
            "CE"=>"Ceará",
            "DF"=>"Distrito Federal",
            "ES"=>"Espírito Santo",
            "GO"=>"Goiás",
            "MA"=>"Maranhão",
            "MT"=>"Mato Grosso",
            "MS"=>"Mato Grosso do Sul",
            "MG"=>"Minas Gerais",
            "PA"=>"Pará",
            "PB"=>"Paraíba",
            "PR"=>"Paraná",
            "PE"=>"Pernambuco",
            "PI"=>"Piauí",
            "RJ"=>"Rio de Janeiro",
            "RN"=>"Rio Grande do Norte",
            "RO"=>"Rondônia",
            "RS"=>"Rio Grande do Sul",
            "RR"=>"Roraima",
            "SC"=>"Santa Catarina",
            "SE"=>"Sergipe",
            "SP"=>"São Paulo",
            "TO"=>"Tocantins"
            );
    }
    
    public static function listaConheceu (){
        return array(
            'TV',
            'Jornal',
            'Internet',
            'Outdoor',
            'Indicação de Terceiro',
            'Outros',
        );
    }
    
    public static function ordena($array, $on, $order=SORT_ASC){
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}
    
}