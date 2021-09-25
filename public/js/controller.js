$(function(){
    /* Gerenciamento de Action */
    
    var scntDiv = $('#ulAction');
    var alerta = $('#alerta');
    var i = $('#ulAction li').size() + 1;
    
    $('#addAction').click(function() {
        var action = $('#action').val();
        var descricao = $('#descricao').val();
        if(action){
            if(descricao){
                $('<li>\n\
                    <span class="handle ui-sortable-handle">\n\
                        <i class="fa fa-ellipsis-v"></i>\n\
                        <i class="fa fa-ellipsis-v"></i>\n\
                    </span>\n\
                    <input type="hidden" value="'+ descricao +'" name="descricao[]" />\n\
                    <input type="hidden" value="'+ action +'" name="action[]" />\n\
                    <input type="checkbox" value="">\n\
                    <span class="text">'+ action +'</span>\n\
                    <small>'+ descricao +'</small>\n\
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
                $('#action').val('');
                $('#descricao').val('');
            return false;
            
            }else{
                $('<div class="alert alert-warning alert-dismissible" role="alert"> \n\
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                    <span aria-hidden="true">×</span></button> \n\
                    <strong>Opa!</strong> Por favor preencha Descricão! \n\
                </div>').appendTo(alerta).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
            }
        } else {
            $('<div class="alert alert-warning alert-dismissible" role="alert"> \n\
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                    <span aria-hidden="true">×</span></button> \n\
                    <strong>Opa!</strong> Por favor preencha Action! \n\
                </div>').appendTo(alerta).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
        }
    });
    
});