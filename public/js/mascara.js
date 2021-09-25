$(function() {
    
  $('.addTooltip').tooltip();
     
  $(".alert").fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
  $('.proximo').click(function(){
      var href = $(this).attr('href');
      
      if(href = 'aba1'){
        $('#ab1').removeClass('active');
        $('#ab2').addClass('active');
      }
      
  });  
      //jQuery UI sortable for the todo list
  $(".todo-list").sortable({
    placeholder: "sort-highlight",
    handle: ".handle",
    forcePlaceholderSize: true,
    zIndex: 999999
  });
/* Cria campo de Mascara em input */
    $( ".calendario" ).datepicker({
        language: 'pt-br',
        format: 'dd/mm/yyyy',
        autoclose: 'false',
    });
    
    /* Configuração para Real */
    $('.moeda').maskMoney({
    symbol : 'R$', /* Símbolo da moeda */
    decimal : ',', /* Separador de decimais */
    thousands : '.', /* Separador de milhares */
    });
    $('.timepicker').mask("99:99")
    $('.numero').mask("9999999999999")
    $('.cpf').mask("999.999.999-99");
    $('.cnpj').mask("99.999.999/9999-99");
    $('.ie').mask("999/9999999");
    $('.cep').mask("99.999-999");
    $('.telefone').mask("(99) 9999-9999");
    $('.celular').mask("(99) 99999-9999");
    $('.semestre').mask("9999.9");
    $('.data').mask("99/99/9999");
    
    $('.areapesquisa').hide();
    
    $(".pesquisar").click(function(){
        $(".areapesquisa").toggle('normal');
        $('.areapesquisa').show();
    });
   
   /* Buscar Endereço da empresa */
    function buscar_infoempresa(){
        var empresa = $('#load_empresa').val();
        if(empresa){
            var url = '/sistema/protesto/buscainfoempresa/id/'+empresa;
            $.ajax({
                type: "POST",
                url: url,
                data: {id:empresa},
                dataType: "html",
                success: function(data){
                   $("#load_infoempresa").html(data);
                }
            });
        }
    }
    $("#load_empresa").change(buscar_infoempresa);
   
    
    /* Busca bairro por municipio */
    
    function buscar_bairro(){
        var municipio = $('#municipio').val();
        
        if(municipio){
            var url = '/sistema/colaborador/buscarbairro/id/'+municipio;
            $.ajax({
                type: "POST",
                url: url,
                data: {id:municipio},
                dataType: "html",
                success: function(data){
                   $("#load_bairro").html(data);
                }
            });
        }
        
        
    }
    $("#municipio").change(buscar_bairro);
    
    
    
    /* Busca Curso por instituicao */
    
    function buscar_instituicao(){
        var instituicao = $('#instituicao').val();
        
        if(instituicao){
            var url = '/sistema/colaborador/buscarinstituicao/id/'+instituicao;
            $.ajax({
                type: "POST",
                url: url,
                data: {id:instituicao},
                dataType: "html",
                success: function(data){
                   $("#load_curso").html(data);
                }
            });
        }
        
        
    }
    $("#instituicao").change(buscar_instituicao);
    
    
    /* Buscar campos por formas de pagamento */
    function busca_campos(){
        var id = $('#pgTipo').val();
        if(id){
            var url = '/sistema/protesto/buscacampos/id/'+id;
            $.ajax({
                type: "POST",
                url: url,
                data: {id:id},
                dataType: "html",
                success: function(data){
                   $("#load_campos").html(data);
                }
            });
        }
    }
    $("#pgTipo").change(busca_campos);
   
    
    
     /* Seleciona Orientador */
    
    $("#modal_btn_salvar").click(function(event) {
        event.preventDefault();
        var radio_item = $(".modal_id_orientador:checked");
        if (radio_item && radio_item.length) {
            $("#id_orientador").val(radio_item.val());
            $("#show_id_orientador").val(radio_item.attr("title"));
            $("#myModal").modal("hide");
        } else {
            alert('não marcou ninguém!');
        }
    });    
});