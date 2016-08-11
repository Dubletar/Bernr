/**
 * 
 */
$( document ).ready(function() {
    
    // Get the form.
    var form = $( '#login-form' );

    // Get the messages div.
    var formMessages = $( '#form-messages' );

    $( form ).submit( function ( event ) {
        
        event.preventDefault();
        
        var formData = $( form ).serialize();
        
        $.ajax({
            
            type: 'POST',
            url: $(form).attr('action'),
            data: formData
            
        })
        .error(function ( response ) {
            
            $(document).html(response);
    
        })
        .done(function( response ) {
            
            if (response.data === true) {
                location.reload();
            } else {
                $( '#login-form' ).html( response );
            }
            
        });
        
    });
});