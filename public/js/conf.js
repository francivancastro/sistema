$(document).ready(function(){
   // $(":file").filestyle(); 
   $( '.swipebox' ).swipebox();
  
   var ul = $('#upload ul');

    $('#addAnexos').click(function(){
        // Simulate a click on the file input button
        // to show the file browser dialog
        $('#drop').parent().find('input').click();
    });

    // Initialize the jQuery File Upload plugin
    $('#upload').fileupload({

        // This element will accept file drag/drop uploading
        dropZone: $('#drop'),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {

            var tpl = $('<li class="working"><input type="text" value="0" data-width="20" data-height="20"'+
                ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><span class="text"></span><div class="tools"><i id="up-anexo" class="fa fa-upload text-success"></i> <i id="rm-anexo" class="fa fa-trash-o"></i></div></li>');

            // Append the file name and file size
            tpl.find('span').text(data.files[0].name)
                         .append('<small class="label label-info">' + formatFileSize(data.files[0].size) + '</small>');

            // Add the HTML to the UL element
            data.context = tpl.appendTo(ul);
            
            // Initialize the knob plugin
            tpl.find('input').knob();

            // Remove Anexo da lista
            tpl.find('#rm-anexo').click(function(){

                if(tpl.hasClass('working')){
                    jqXHR.abort();
                }

                tpl.fadeOut(function(){
                    tpl.remove();
                });

            });
            
            // Envia Anexo da lista
            tpl.find('#up-anexo').click(function(){
                
                if(tpl.hasClass('working')){
                    var jqXHR = data.submit();
                    
                    if(jqXHR){
                        $('<div class="alert alert-success alert-dismissible" role="alert"> \n\
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                            <span aria-hidden="true">×</span></button> \n\
                            <strong><i class="fa fa-thumbs-o-up"></i></strong> Anexo adcionado com Sucesso! \n\
                        </div>').appendTo("#alert");
                    } else {
                        $('<div class="alert alert-warning alert-dismissible" role="alert"> \n\
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n\
                            <span aria-hidden="true">×</span></button> \n\
                            <strong>Opa!</strong> Falha ao Enviar Anexo! \n\
                        </div>').appendTo("#alert");
                    }
                }

            });

            // 
        },

        progress: function(e, data){

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();

            if(progress == 100){
                data.context.removeClass('working');
            }
        },

        fail:function(e, data){
            // Something has gone wrong!
            data.context.addClass('error');
        }

    });


    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    // Helper function that formats the file sizes
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }
});

