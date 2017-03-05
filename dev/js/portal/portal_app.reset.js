;(function(){
    var $self = $('#reset_pw_box');
    var $el = {
        form: $self.find('form'),
        input_npassw: $self.find('input[name="npassword"]'),
        input_rpassw: $self.find('input[name="rpassword"]')
    };
    $el.form.submit(function(e){
        e.preventDefault();
        var error_messages = [];

        // Validate password.
        if($el.input_npassw.val().length == 0){
            $el.input_npassw.addClass('error');
            error_messages.push('-New password cannot be empty.');
        }
        else if($el.input_npassw.val().length < 6){
            $el.input_npassw.addClass('error');
            error_messages.push('-Password is too short. Minimum of 6 characters.');
        }else{
            $el.input_npassw.removeClass('error');
        }
        if($el.input_rpassw.val().length == 0){
            $el.input_rpassw.addClass('error');
            error_messages.push('-Retype of password is required.');
        }else{
            $el.input_rpassw.removeClass('error');
            if($el.input_npassw.val() != $el.input_rpassw.val())
            {
                error_messages.push('Typed passwords did not match.');
                $el.input_npassw.addClass('error');
                $el.input_rpassw.addClass('error');
            }else{
                $el.input_npassw.removeClass('error');
                $el.input_rpassw.removeClass('error');
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
            url: site.base_url+"users/reset-pw",
            data: data,
            error: function(jqXHR,textStatus,errorThrown){
                idle();
                toastr["error"](textStatus, "Error "+jqXHR.status);
            },
            success: function(response){
                setTimeout(function(){
                    if(response.status == "ok"){
                        toastr['success'](response.message,"Success");
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