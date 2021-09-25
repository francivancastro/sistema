$(function() {

   
    function numeroParaMoeda(n, c, d, t){
        c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    }
    
    
    /* Gerenciamento de taxas */
    
    var scntDiv = $('#ulTaxas');
    var alerta = $('#alerta');
    var i = $('#ulTaxas li').size() + 1;
    
    $('#addTaxa').click(function() {
        var idtaxa = $('#taxa').val();
        var valorTaxa = $('#valTaxa').val();
        var nomeTaxa = $("#taxa option:selected").text();
        if(idtaxa){
            if(valorTaxa){
                $('<li>\n\
                    <span class="handle ui-sortable-handle">\n\
                        <i class="fa fa-ellipsis-v"></i>\n\
                        <i class="fa fa-ellipsis-v"></i>\n\
                    </span>\n\
                    <input type="hidden" value="'+ valorTaxa +'" name="valor[]" />\n\
                    <input type="hidden" value="'+ idtaxa +'" name="id_taxas[]" />\n\
                    <input type="checkbox" value="">\n\
                    <span class="text">'+ nomeTaxa +'</span>\n\
                    <small>R$ '+ valorTaxa +'</small>\n\
                    <div class="tools">\n\
                      <a href="#" id="remove'+i+'"><i class="fa fa-trash-o"></i></a>\n\
                    </div>\n\
                </li>'
                ).appendTo(scntDiv);
                $('#remove'+i).click(function (){
                    $(this).parents('li').remove();
                    return false;
                });
                i++;
                $('#valTaxa').val('');
                $("#taxa").prop('selectedIndex', 0);
            return false;

            }else{
                $('<div class="alert alert-warning alert-dismissible" role="alert"> \n\
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                    <span aria-hidden="true">×</span></button> \n\
                    <strong>Opa!</strong> Não Preencheu valor! \n\
                </div>').appendTo(alerta).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
            }
        } else {
            $('<div class="alert alert-warning alert-dismissible" role="alert"> \n\
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                    <span aria-hidden="true">×</span></button> \n\
                    <strong>Opa!</strong> Não Selecionou Taxa! \n\
                </div>').appendTo(alerta).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
        }
    });
    
    /* Gerenciamento de negociação */
    
    var negociacaoDiv = $('#tbnegociacao');
    var msg = $('#msg');
    var x = $('#negociacao tr').size() + 1;
    
    var tParcela = 0;
    var tMulta = 0;
    var tJuros = 0;
    var tDesconto = 0;
    var tNegociado = 0;
    
    $('#addNegociacao').click(function() {
        
        var ano = $("#ano option:selected").val();
        var nParcela = $("#nParcela option:selected").val();
        var txtParcela = $('#nParcela option:selected').text();
        var valParcela = $('#valParcela').val();
        var valMulta = $('#valMulta').val();
        var valJuros = $('#valJuros').val();
        var desconto = $('#desconto').val();
        
        if(!desconto){
            desconto = "0,00";
        }
        
        if(!valMulta){
            valMulta = "0,00";
        }
        
        if(!valJuros){
            valJuros = "0,00";
        }
        var floatParcela = parseFloat(valParcela.replace('.','').replace(',','.'));
        var floatMulta = parseFloat(valMulta.replace('.','').replace(',','.'));
        var floatJuros = parseFloat(valJuros.replace('.','').replace(',','.'));
        var floatDesconto = parseFloat(desconto.replace('.','').replace(',','.'));
        
        var txt_total_negociado = floatParcela + floatMulta + floatJuros - floatDesconto;
        
        if(!ano || !nParcela || !valParcela){
            $('<div class="alert alert-warning alert-dismissible" role="alert"> \n\
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                    <span aria-hidden="true">×</span></button> \n\
                    <strong>Opa!</strong> Voce precisa preencher todos os campos! \n\
            </div>').appendTo(msg).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
            
        } else {
            
            tParcela += floatParcela;
            tMulta += floatMulta;
            tJuros += floatJuros;
            tDesconto += floatDesconto;
            tNegociado += txt_total_negociado;
        
            $('#mostraNegociacao').html('<tr>\n\
                                            <td><strong>Total: </strong></td>\n\
                                            <td></td>\n\
                                            <td><strong>'+ numeroParaMoeda(tParcela) +'</strong></td>\n\
                                            <td><strong>'+ numeroParaMoeda(tMulta) +'</strong></td>\n\
                                            <td><strong>'+ numeroParaMoeda(tJuros) +'</strong></td>\n\
                                            <td><strong>'+ numeroParaMoeda(tDesconto) +'</strong></td>\n\
                                            <td><strong>'+ numeroParaMoeda(tNegociado) +'</strong></td>\n\
                                        </tr>');
            
            
            $('<tr>\n\
                    <td><input name="ano_base[]" type="hidden" value="'+ ano +'" name="" /> '+ ano +'</td>>\n\
                    <td><input name="parcela[]" type="hidden" value="'+ nParcela +'" name="" /> '+ txtParcela +'</td>>\n\
                    <td><input name="valor_parcela[]" type="hidden" value="'+ valParcela +'" name="" /> '+ valParcela +'</td>>\n\
                    <td><input name="multa[]" type="hidden" value="'+ valMulta +'" name="" /> '+ valMulta +'</td>>\n\
                    <td><input name="juros[]" type="hidden" value="'+ valJuros +'" name="" /> '+ valJuros +'</td>>\n\
                    <td><input name="desconto[]" type="hidden" value="'+ desconto +'" name="" /> '+ desconto +'</td>>\n\
                    <td>'+ numeroParaMoeda(txt_total_negociado) +'</td>>\n\
                    <td><a href="#" id="negociacaoremove'+ x +'"><i class="fa fa-trash-o"></i></a></td>>\n\
            </tr>').appendTo(negociacaoDiv);
            
            $('#negociacaoremove'+x).click(function (){
                
                tParcela -= floatParcela;
                tMulta -= floatMulta;
                tJuros -= floatJuros;
                tDesconto -= floatDesconto;
                
                if(tParcela == 0 && tMulta == 0  && tJuros == 0 && tDesconto == 0){
                    tParcela = 0;
                    tMulta = 0;
                    tJuros = 0;
                    tDesconto = 0;
                }
                
                $('#mostraNegociacao').html('<tr>\n\
                                            <td></td>\n\
                                            <td></td>\n\
                                            <td>'+ numeroParaMoeda(tParcela) +'</td>\n\
                                            <td>'+ numeroParaMoeda(tMulta) +'</td>\n\
                                            <td>'+ numeroParaMoeda(tJuros) +'</td>\n\
                                            <td>'+ numeroParaMoeda(tDesconto) +'</td>\n\
                                            <td>'+ numeroParaMoeda(tNegociado) +'</td>\n\
                                        </tr>');
                
                $(this).parents('tr').remove();
                return false;
            });
            
            x++;
                $("#ano").prop('selectedIndex', 0);
                $("#nParcela").prop('selectedIndex', 0);
                $("#valParcela").val('');
                $("#valMulta").val('');
                $("#valJuros").val('');
                $("#desconto").val('');
            return false;

        }
    });

/*
 * *
 * * ADICIONAR PAGAMENTO
 * *
 * *
 * */

    $('#pgTipo').change(function (){
        
        var dinheiroDiv = $('#dinheiroDiv');
        var cartaoDiv = $('#cartaoDiv');
        var transferenciaDiv = $('#transferenciaDiv');
        var erro = $('#erro');
        var d = $('#dinheiroDiv div').size() + 1;
        var c = $('#cartaoDiv div').size() + 1;
        var t = $('#transferenciaDiv div').size() + 1;
        
        var valorTotal = $('#valorTotal').val();
        var vtotal = parseFloat(valorTotal.replace('.','').replace(',','.'));
        var count = 0;
        
        $('#addPagamento').click(function (){
            var dataPag = $('#dataPag').val();
            var pgTipo = $('#pgTipo option:selected').val();
            
            var cTname = $("#cTipo option:selected").text();
            
            var cTipo = $("#cTipo option:selected").val();
            
            var bandeira = $("#bandeira option:selected").val();
            
            var cQtd = $("#cQtd option:selected").val();
            var cCodDoc = $('#cCodDoc').val();
            
            var tAgcli = $('#tAgcli').val();
            var tConcli = $('#tConcli').val();
            var tAgemp = $('#tAgemp').val();
            var tConemp = $('#tConemp').val();
            var banEmp = $('#banEmp').val();
            var banCli = $('#banCli').val();
            
            var vpago = $('#vpago').val();
            var vpagoNovo = parseFloat(vpago.replace('.','').replace(',','.'));
            count += vpagoNovo;
            vtotal -= vpagoNovo;
            if (pgTipo == 1){
                if(!vpago){
                    $('<div class="alert alert-warning alert-dismissible" role="alert"> \n\
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                            <span aria-hidden="true">×</span></button> \n\
                            <strong>Opa!</strong> Voce precisa preencher todos os campos! \n\
                    </div>').appendTo(erro).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                } else {
                    $('#mostrafalta').html('<label>Falta:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(vtotal) +'" disabled=""/>\n\
                                    </div>');
                    $('#mostraSoma').html('<label>Total de Pagamento:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(count) +'" disabled=""/>\n\
                                    </div>');
                    $('<div class="well">\n\
                        <i id="removDin'+ d +'" class="fa fa-close text-red pull-right"></i>\n\
                        <strong> Valor Pago: </strong>'+ vpago +'<br />\n\
                        <input type="hidden" name="valor_dinheiro[]" value="'+ vpago +'"/>\n\
                        <strong> Data: </strong>'+ dataPag +'<br />\n\
                        <input type="hidden" name="data_dinheiro[]" value="'+ dataPag +'"/>\n\
                        </div>').appendTo(dinheiroDiv);

                    $('#removDin'+d).click(function (){
                        $(this).parents('.well').remove();
                        count -= vpagoNovo;
                        vtotal += vpagoNovo;
                        if(count < 0){
                           count = 0; 
                        }
                        $('#mostrafalta').html('<label>Falta:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(vtotal) +'" disabled=""/>\n\
                                    </div>');
                        $('#mostraSoma').html('<label>Total de Pagamento:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(count) +'" disabled=""/>\n\
                                    </div>');
                        return false;
                    });
                    
                    d++;
                    $('#vpago').val('');
                    $('#dataPag').val('');
                    return false;
                }
            }
        
            if (pgTipo == 2){
                if(!cTipo || !cQtd || !cCodDoc){
                    $('<div class="alert alert-warning alert-dismissible" role="alert"> \n\
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                            <span aria-hidden="true">×</span></button> \n\
                            <strong>Opa!</strong> Voce precisa preencher todos os campos! \n\
                    </div>').appendTo(erro).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                } else {
                    $('#mostrafalta').html('<label>Falta:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(vtotal) +'" disabled=""/>\n\
                                    </div>');
                   $('#mostraSoma').html('<label>Total de Pagamento:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(count) +'" disabled=""/>\n\
                                    </div>');
                    $('<div class="well">\n\
                        <i id="removCar'+ c +'" class="fa fa-close text-red pull-right"></i>\n\
                        <strong>Tipo: </strong>'+ cTname +'<br />\n\
                        <input type="hidden" name="id_cartao_tipo[]" value="'+ cTipo +'"/>\n\
                        <strong>Bandeira: </strong>'+ bandeira +'<br />\n\
                        <input type="hidden" name="bandeira[]" value="'+ bandeira +'"/>\n\
                        <strong>Qtd. Parcela: </strong>'+ cQtd +'<br />\n\
                        <input type="hidden" name="parcelas[]" value="'+ cQtd +'"/>\n\
                        <strong>Nº Documento: </strong>'+ cCodDoc +'<br />\n\
                        <input type="hidden" name="codigo[]" value="'+ cCodDoc +'" />\n\
                        <strong>Valor Pago: </strong>'+ vpago +'<br />\n\
                        <input type="hidden" name="valor_cartao[]" value="'+ vpago +'"/>\n\
                        <strong> Data: </strong>'+ dataPag +'<br />\n\
                        <input type="hidden" name="data_cartao[]" value="'+ dataPag +'"/>\n\
                        </div>').appendTo(cartaoDiv);

                    $('#removCar'+c).click(function (){
                        count -= vpagoNovo;
                        vtotal += vpagoNovo;
                        if(count < 0){
                           count = 0; 
                        }
                        $('#mostrafalta').html('<label>Falta:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(vtotal) +'" disabled=""/>\n\
                                    </div>');
                        $('#mostraSoma').html('<label>Total de Pagamento:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(count) +'" disabled=""/>\n\
                                    </div>');
                        $(this).parents('.well').remove();
                         return false;
                    });

                    c++;

                $("#cTipo").prop('selectedIndex', 0);
                    $("#cQtd").prop('selectedIndex', 0);
                    $('#cCodDoc').val('');
                    $('#vpago').val('');
                    $('#dataPag').val('');
                    $('#bandeira').prop('selectedIndex', 0);
                    return false;
                }
            }
            
            if(pgTipo == 3){
                if(!tAgcli || !tConcli || !tAgemp || !tConemp){
                    $('<div class="alert alert-warning alert-dismissible" role="alert"> \n\
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                            <span aria-hidden="true">×</span></button> \n\
                            <strong>Opa!</strong> Voce precisa preencher todos os campos! \n\
                    </div>').appendTo(erro).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                } else {
                    $('#mostrafalta').html('<label>Falta:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(vtotal) +'" disabled=""/>\n\
                                    </div>');
                    $('#mostraSoma').html('<label>Total de Pagamento:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(count) +'" disabled=""/>\n\
                                    </div>');
                    $('<div class="well">\n\
                        <i id="removTra'+ t +'" class="fa fa-close text-red pull-right"></i>\n\
                        <strong>Banco (Cli): </strong> '+ banCli +' <br />\n\
                        <strong>Agencia / Conta (Cli): </strong><br />\n\
                        '+ tAgcli +' / '+ tConcli +'<br />\n\
                        <input type="hidden" name="banco_cliente[]" value="'+ banCli +'"/>\n\
                        <input type="hidden" name="agencia_cliente[]" value="'+ tAgcli +'"/>\n\
                        <input type="hidden" name="conta_cliente[]" value="'+ tConcli +'"/>\n\
                        <strong>Banco (Emp): </strong> '+ banEmp +' <br />\n\
                        <strong>Agencia / Conta (Emp): </strong><br />\n\
                        '+ tAgemp +' / '+ tConemp +'<br />\n\
                        <input type="hidden" name="banco_empresa[]" value="'+ banEmp +'"/>\n\
                        <input type="hidden" name="agencia_empresa[]" value="'+ tAgemp +'"/>\n\
                        <input type="hidden" name="conta_empresa[]" value="'+ tConemp +'"/>\n\
                        <strong>Valor Pago: </strong>'+ vpago +'<br />\n\
                        <input type="hidden" name="valor_transferencia[]" value="'+ vpago +'"/>\n\
                        <strong> Data: </strong>'+ dataPag +'<br />\n\
                        <input type="hidden" name="data_transferencia[]" value="'+ dataPag +'"/>\n\
                        </div>').appendTo(transferenciaDiv);

                    $('#removTra'+t).click(function (){
                        count -= vpagoNovo;
                        if(count < 0){
                           count = 0; 
                        }
                        $('#mostrafalta').html('<label>Falta:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(vtotal) +'" disabled=""/>\n\
                                    </div>');
                        $('#mostraSoma').html('<label>Total de Pagamento:</label>\n\
                                    <div class="input-group" >\n\
                                        <span class="input-group-addon">R$</span>\n\
                                        <input type="text" class="form-control" value="'+ numeroParaMoeda(count) +'" disabled=""/>\n\
                                    </div>');
                        $(this).parents('.well').remove();
                        return false;
                    });

                    t++;
                    $('#tAgcli').val('');
                    $('#tConcli').val('');
                    $('#tAgemp').val('');
                    $('#tConemp').val('');
                    $('#banEmp').val('');
                    $('#banCli').val('');
                    $('#vpago').val('');
                    $('#dataPag').val('');
                    return false;
                }
            }
            
       });
    });
    
    /*
     * INCLUIR RESPONSÁVEL AO PROTESTO 
     */
    
    $("#nome").keyup(function() {
                var nome = $('#nome').val();
                var url = '/sistema/protesto/pesquisa/nome/'+nome;
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {nome:nome},
                    dataType: "html",
                    success: function(data){
                       $("#load_pessoas").html(data);
                    }
                });
        });
        $("#btn_limpa").click(function(){
            $('#nome').val('');
            var nome = ' ';
            var url = '/sistema/protesto/pesquisa/nome/'+nome;
           $.ajax({
                type: "POST",
                url: url,
                data: {nome:nome},
                dataType: "html",
                success: function(data){
                   $("#load_pessoas").html(data);
                }
            });
        });
        
         /* Seleciona Responsavel */
    
    $("#modal_btn_salvar_responsavel").click(function(event) {
        event.preventDefault();
        var radio_item = $(".modal_id_responsavel:checked");
        if (radio_item && radio_item.length) {
            $("#id_responsavel").val(radio_item.val());
            $("#nome_responsavel").val(radio_item.attr("title"));
            $("#myModal").modal("hide");
            
            var responsavel = radio_item.val();
            if(responsavel){
                var url = '/sistema/protesto/buscaendereco/id/'+responsavel;
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {id:responsavel},
                    dataType: "html",
                    success: function(data){
                       $("#load_endereco").html(data);
                    }
                });
                
                var url2 = '/sistema/protesto/buscarempresa/id/'+responsavel;
                $.ajax({
                    type: "POST",
                    url: url2,
                    data: {id:responsavel},
                    dataType: "html",
                    success: function(data){
                       $("#load_empresa").html(data);
                    }
                });
                
                var url3 = '/sistema/protesto/buscaaluno/id/'+responsavel;
                $.ajax({
                    type: "POST",
                    url: url3,
                    data: {id:responsavel},
                    dataType: "html",
                    success: function(data){
                       $("#load_aluno").html(data);
                    }
                });
                    return false;
            }
            
        } else {
            alert('não marcou ninguém!');
        }
    }); 
});