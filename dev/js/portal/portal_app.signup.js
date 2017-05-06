;(function(){
    var $self = $('#signup_box');
    var $el = {
        form: $self.find('form'),
        input_fname: $self.find('input[name="fname"]'),
        input_lname: $self.find('input[name="lname"]'),
        input_email: $self.find('input[name="email"]'),
        input_passw: $self.find('input[name="password"]'),
        input_agree: $self.find('input[name="agree"]')
    };
    $el.form.submit(function(e){
        e.preventDefault();
        var error_messages = [];
        var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        // Validate first name.
        if($.trim($el.input_fname.val()).length < 2){
            $el.input_fname.addClass('error');
            error_messages.push('-First name is required.');
            
        }else{
            $el.input_fname.removeClass('error');
        }

        // Validate last name.
        if($.trim($el.input_lname.val()).length < 2){
            $el.input_lname.addClass('error');
            error_messages.push('-Last name is required.');
            
        }else{
            $el.input_lname.removeClass('error');
        }

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
            if($el.input_passw.val().length < 6)
            {
                $el.input_passw.addClass('error');
                error_messages.push('-Password is too short. Minimum of 6 required.');
            }else{
                $el.input_passw.removeClass('error');
            }
        }

        // Check for errors.
        if(error_messages.length > 0){
            var error_messages = error_messages.join('<br>',error_messages);
            idle();
            toastr['error'](error_messages,"Error");
        }else{
            // Enforce terms agreement.
            if($el.input_agree.is(':checked')){
                var data = $el.form.serialize();
                send(data);
            }else{
                toastr['error']('You must agree to terms before you can proceed.','Error');
            }
        }
    });
    function send(data){
        busy();
        $.ajax({
            method: "POST",
            url: site.base_url+"account/signup",
            data: data,
            error: function(jqXHR,textStatus,errorThrown){
                toastr["error"](textStatus, "Error "+jqXHR.status);
            },
            success: function(response){
                setTimeout(function(){
                    if(response.status == "ok"){
                        toastr['success'](response.message,"Check Your Email");
                        setTimeout(function(){window.location = site.base_url+'account/signin';},4000);
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