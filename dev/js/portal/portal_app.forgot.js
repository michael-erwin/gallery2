;(function(){
    var $self = $('#forgot_pw_box');
    var $el = {
        form: $self.find('form'),
        input_email: $self.find('input[name="email"]')
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
            url: site.base_url+"users/forgot-pw",
            data: data,
            error: function(jqXHR,textStatus,errorThrown){
                idle();
                toastr["error"](textStatus, "Error "+jqXHR.status);
            },
            success: function(response){
                setTimeout(function(){
                    if(response.status == "ok"){
                        setTimeout(function(){window.location = response.data;},4000);
                        toastr['success'](response.message,"Sent");
                    }else{
                        idle();
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