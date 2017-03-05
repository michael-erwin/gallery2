;(function(){
    var $self = $('#signin_box');
    var $el = {
        form: $self.find('form'),
        input_email: $self.find('input[name="email"]'),
        input_passw: $self.find('input[name="password"]')
    };
    $el.form.submit(function(e){
        e.preventDefault();
        var error_messages = [];
        var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        // Validate email.
        if($.trim($el.input_email.val()).length == 0){
            $el.input_email.addClass('error');
            error_messages.push('-Email is required.');
            
        }else{
            $el.input_email.removeClass('error');
            if(!email_regex.test($el.input_email.val())){
                $el.input_email.addClass('error');
                error_messages.push('-Email is invalid.');
            }else{
                $el.input_email.removeClass('error');
            }
        }

        // Validate password.
        if($el.input_passw.val().length == 0){
            $el.input_passw.addClass('error');
            error_messages.push('-Password is required.');
        }else{
            $el.input_passw.removeClass('error');
        }

        // Check for errors.
        if(error_messages.length > 0){
            var error_messages = error_messages.join('<br>',error_messages);
            toastr['error'](error_messages,"Error");
        }else{
            var data = $el.form.serialize();
            send(data);
        }
    });
    function send(data){
        busy();
        $.ajax({
            method: "POST",
            url: site.base_url+"users/sign-in",
            data: data,
            error: function(jqXHR,textStatus,errorThrown){
                idle();
                toastr["error"](textStatus, "Error "+jqXHR.status);
            },
            success: function(response){
                setTimeout(function(){
                    idle();
                    if(response.status == "ok"){
                        window.location = response.data;
                    }else{
                        toastr['error'](response.message,"Error");
                    }
                },1000);
            }
        });
    }
    function busy(){
        $self.find('input').prop('disabled', true);
        $self.find('button').prop('disabled', true);
        $self.find('a').unbind('click').click(function(e){
            return false;
        });
    }
    function idle(){
        $self.find('input').prop('disabled', false);
        $self.find('button').prop('disabled', false);
        $self.find('a').unbind('click').click(function(e){
            return true;
        });
    }
}())