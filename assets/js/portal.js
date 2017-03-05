;(function(){ var $self = $('#signin_box'); var $el = { form: $self.find('form'), input_email: $self.find('input[name="email"]'), input_passw: $self.find('input[name="password"]') }; $el.form.submit(function(e){ e.preventDefault(); var error_messages = []; var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; if($.trim($el.input_email.val()).length == 0){ $el.input_email.addClass('error'); error_messages.push('-Email is required.'); }else{ $el.input_email.removeClass('error'); if(!email_regex.test($el.input_email.val())){ $el.input_email.addClass('error'); error_messages.push('-Email is invalid.'); }else{ $el.input_email.removeClass('error'); } } if($el.input_passw.val().length == 0){ $el.input_passw.addClass('error'); error_messages.push('-Password is required.'); }else{ $el.input_passw.removeClass('error'); } if(error_messages.length > 0){ var error_messages = error_messages.join('<br>',error_messages); toastr['error'](error_messages,"Error"); }else{ var data = $el.form.serialize(); send(data); } }); function send(data){ busy(); $.ajax({ method: "POST", url: site.base_url+"users/sign-in", data: data, error: function(jqXHR,textStatus,errorThrown){ idle(); toastr["error"](textStatus, "Error "+jqXHR.status); }, success: function(response){ setTimeout(function(){ idle(); if(response.status == "ok"){ window.location = response.data; }else{ toastr['error'](response.message,"Error"); } },1000); } }); } function busy(){ $self.find('input').prop('disabled', true); $self.find('button').prop('disabled', true); $self.find('a').unbind('click').click(function(e){ return false; }); } function idle(){ $self.find('input').prop('disabled', false); $self.find('button').prop('disabled', false); $self.find('a').unbind('click').click(function(e){ return true; }); }
}())
;(function(){ var $self = $('#signup_box'); var $el = { form: $self.find('form'), input_fname: $self.find('input[name="fname"]'), input_lname: $self.find('input[name="lname"]'), input_email: $self.find('input[name="email"]'), input_passw: $self.find('input[name="password"]'), input_agree: $self.find('input[name="agree"]') }; $el.form.submit(function(e){ e.preventDefault(); var error_messages = []; var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; if($.trim($el.input_fname.val()).length < 2){ $el.input_fname.addClass('error'); error_messages.push('-First name is required.'); }else{ $el.input_fname.removeClass('error'); } if($.trim($el.input_lname.val()).length < 2){ $el.input_lname.addClass('error'); error_messages.push('-Last name is required.'); }else{ $el.input_lname.removeClass('error'); } if($.trim($el.input_email.val()).length == 0){ $el.input_email.addClass('error'); error_messages.push('-Email is required.'); }else{ $el.input_email.removeClass('error'); if(!email_regex.test($el.input_email.val())){ $el.input_email.addClass('error'); error_messages.push('-Email is invalid.'); }else{ $el.input_email.removeClass('error'); } } if($el.input_passw.val().length == 0){ $el.input_passw.addClass('error'); error_messages.push('-Password is required.'); }else{ if($el.input_passw.val().length < 6) { $el.input_passw.addClass('error'); error_messages.push('-Password is too short. Minimum of 6 required.'); }else{ $el.input_passw.removeClass('error'); } } if(error_messages.length > 0){ var error_messages = error_messages.join('<br>',error_messages); idle(); toastr['error'](error_messages,"Error"); }else{ if($el.input_agree.is(':checked')){ toastr['success']('No erros','Success'); var data = $el.form.serialize(); send(data); }else{ toastr['error']('You must agree to terms before you can proceed.','Error'); } } }); function send(data){ busy(); $.ajax({ method: "POST", url: site.base_url+"users/sign-up", data: data, error: function(jqXHR,textStatus,errorThrown){ toastr["error"](textStatus, "Error "+jqXHR.status); }, success: function(response){ setTimeout(function(){ idle(); if(response.status == "ok"){ }else{ toastr['error'](response.message,"Error"); } console.log(response) },1000); } }); } function busy(){ $self.find('input').prop('disabled', true); $self.find('button').prop('disabled', true); $self.find('a').unbind('click').click(function(e){ return false; }); } function idle(){ $self.find('input').prop('disabled', false); $self.find('button').prop('disabled', false); $self.find('a').unbind('click').click(function(e){ return true; }); }
}())
;(function(){ var $self = $('#forgot_pw_box'); var $el = { form: $self.find('form'), input_email: $self.find('input[name="email"]') }; $el.form.submit(function(e){ e.preventDefault(); var error_messages = []; var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; if($.trim($el.input_email.val()).length == 0){ $el.input_email.addClass('error'); error_messages.push('-Email is required.'); }else{ $el.input_email.removeClass('error'); if(!email_regex.test($el.input_email.val())){ $el.input_email.addClass('error'); error_messages.push('-Email is invalid.'); }else{ $el.input_email.removeClass('error'); } } if(error_messages.length > 0){ var error_messages = error_messages.join('<br>',error_messages); toastr['error'](error_messages,"Error"); }else{ var data = $el.form.serialize(); send(data); } }); function send(data){ busy(); $.ajax({ method: "POST", url: site.base_url+"users/forgot-pw", data: data, error: function(jqXHR,textStatus,errorThrown){ idle(); toastr["error"](textStatus, "Error "+jqXHR.status); }, success: function(response){ setTimeout(function(){ if(response.status == "ok"){ setTimeout(function(){window.location = response.data;},4000); toastr['success'](response.message,"Sent"); }else{ idle(); toastr['error'](response.message,"Error"); } },1000); } }); } function busy(){ $self.find('input').prop('disabled', true); $self.find('button').prop('disabled', true); $self.find('a').unbind('click').click(function(e){ return false; }); } function idle(){ $self.find('input').prop('disabled', false); $self.find('button').prop('disabled', false); $self.find('a').unbind('click').click(function(e){ return true; }); }
}())
;(function(){ var $self = $('#reset_pw_box'); var $el = { form: $self.find('form'), input_npassw: $self.find('input[name="npassword"]'), input_rpassw: $self.find('input[name="rpassword"]') }; $el.form.submit(function(e){ e.preventDefault(); var error_messages = []; if($el.input_npassw.val().length == 0){ $el.input_npassw.addClass('error'); error_messages.push('-New password cannot be empty.'); } else if($el.input_npassw.val().length < 6){ $el.input_npassw.addClass('error'); error_messages.push('-Password is too short. Minimum of 6 characters.'); }else{ $el.input_npassw.removeClass('error'); } if($el.input_rpassw.val().length == 0){ $el.input_rpassw.addClass('error'); error_messages.push('-Retype of password is required.'); }else{ $el.input_rpassw.removeClass('error'); if($el.input_npassw.val() != $el.input_rpassw.val()) { error_messages.push('Typed passwords did not match.'); $el.input_npassw.addClass('error'); $el.input_rpassw.addClass('error'); }else{ $el.input_npassw.removeClass('error'); $el.input_rpassw.removeClass('error'); } } if(error_messages.length > 0){ var error_messages = error_messages.join('<br>',error_messages); toastr['error'](error_messages,"Error"); }else{ var data = $el.form.serialize(); send(data); } }); function send(data){ busy(); $.ajax({ method: "POST", url: site.base_url+"users/reset-pw", data: data, error: function(jqXHR,textStatus,errorThrown){ idle(); toastr["error"](textStatus, "Error "+jqXHR.status); }, success: function(response){ setTimeout(function(){ if(response.status == "ok"){ toastr['success'](response.message,"Error"); setTimeout(function(){window.location = site.base_url+'account/signin';},4000); }else{ idle(); toastr['error'](response.message,"Error"); } },1000); } }); } function busy(){ $self.find('input').prop('disabled', true); $self.find('button').prop('disabled', true); $self.find('a').unbind('click').click(function(e){ return false; }); } function idle(){ $self.find('input').prop('disabled', false); $self.find('button').prop('disabled', false); $self.find('a').unbind('click').click(function(e){ return true; }); }
}())