admin_app.user_editor =
{
    self: '#modal_user_editor',
    config: {
        
    },
    objects: {
        'form': null,
        'modal_title_sub': null,
        'input_fname': null,
        'input_lname': null,
        'input_email': null,
        'input_password': null,
        'input_role': null,
        'input_status': null,
        'input_verify': null,
        'toggle_pass': null,
        'buttons': null
    },
    data: {
        mode: null,
        role_list: [],
        item: {
            'id': null,
            'email': "",
            'first_name': "",
            'last_name': "",
            'status': "",
            "role_id": ""
        },
        last_signature: null
    },
    init: function() {
        this.self = $(this.self);
        this.objects.form = this.self.find('form');
        this.objects.modal_title_sub = this.self.find('h4.modal-title span');
        this.objects.input_id = this.self.find('input[name="id"]');
        this.objects.input_fname = this.self.find('input[name="fname"]');
        this.objects.input_lname = this.self.find('input[name="lname"]');
        this.objects.input_email = this.self.find('input[name="email"]');
        this.objects.input_password = this.self.find('input[name="password"]');
        this.objects.input_role = this.self.find('select[name="role"]');
        this.objects.input_status = this.self.find('input[name="status"]');
        this.objects.input_verify = this.self.find('input[name="verify_email"]');
        this.objects.toggle_pass = this.self.find('[data-id="password_toggle"]');
        this.objects.buttons = this.self.find('button');

        // Modal setup.
        this.self.modal({backdrop: 'static'}).modal('hide');

        // Reset settings.
        this.data.mode = null;

        // Event bindings.
        this.objects.form.unbind().on('submit',this.save.bind(this));
        this.objects.toggle_pass.unbind().on('click',this.togglePass.bind(this));
        this.objects.input_verify.unbind().on('click',this.toggleVerify.bind(this));

        // Initialize events.
        this.getData();
    },
    render: function() {
        // Clear previous error markings.
        this.self.find('.error').removeClass('error');
        // Build permission table entries.
        var list_options = "";
        if(this.data.role_list.length > 0) {
            for (var i = 0; i < this.data.role_list.length; i++) {
                var role = this.data.role_list[i];
                list_options += '<option value="'+role.id+'">'+role.name+'</option>';
            }
        }
        else {
            list_options += '<option>None</option>';
        };
        this.objects.input_role.html(list_options);
        // Display behavior.
        if(this.data.mode == "add") {
            this.objects.modal_title_sub = this.self.find('h4.modal-title span');
            this.objects.modal_title_sub.text("New");
            this.objects.input_id.val(null).prop('disabled',true);
            this.objects.toggle_pass.prop('disabled',false);
            this.self.modal('show');
        };
        if(this.data.mode == "update") {
            this.objects.modal_title_sub = this.self.find('h4.modal-title span');
            this.objects.modal_title_sub.text("Edit");
            this.objects.input_id.prop('disabled',false).val(this.data.item.id);
            this.objects.input_fname.val(this.data.item.first_name);
            this.objects.input_lname.val(this.data.item.last_name);
            this.objects.input_email.val(this.data.item.email);
            this.objects.input_role.val(this.data.item.role_id);
            this.objects.toggle_pass.prop('disabled',false);
            var status = this.data.item.status;
            this.objects.input_status.each(function(){
                if($(this).val() == status) {
                    $(this).prop('checked',true);
                } else {
                    $(this).prop('checked',false);
                }
            });
            this.self.modal('show');
        };
        if(this.objects.toggle_pass.is(':checked')) {
            this.objects.input_password.prop('disabled',false);
        }
        else {
            this.objects.input_password.prop('disabled',true);
        }
        // Event bindings.
        
    },    
    getData: function(data) {
        if(data) {
            this.data.permission_list = data;
            this.render();
        }
        else{
            $.ajax({
                url: site.base_url+'roles/manage/get_all',
                method: 'get',
                data: "",
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                    this.render();
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.data.role_list = response.data;
                    }
                    else {
                        toastr["error"](response.message);
                    }
                    this.render();
                }
            });
        }
    },
    new: function(e) {
        this.objects.form[0].reset();
        this.data.mode = 'add';
        this.render();
    },
    edit: function(data) {
        this.objects.form[0].reset();
        if(data){this.data.item = data};
        this.data.mode = 'update';
        this.render();
    },
    save: function(e) {
        e.preventDefault();
        var errors = 0;

        // Validate first name.
        if($.trim(this.objects.input_fname.val()).length < 2) {
            this.objects.input_fname.addClass('error');
            errors++;
        } else { this.objects.input_fname.removeClass('error'); }

        // Validate last name.
        if($.trim(this.objects.input_lname.val()).length < 2) {
            this.objects.input_lname.addClass('error');
            errors++;
        } else { this.objects.input_lname.removeClass('error'); }

        // Validate first email.
        var email_filter = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if(!email_filter.test(this.objects.input_email.val())) {
            this.objects.input_email.addClass('error');
            errors++;
        } else { this.objects.input_email.removeClass('error'); }

        // Validate first password.
        if(this.objects.input_verify.is(':checked') === false) {
            if(this.objects.toggle_pass.is(':checked')) {
                if(this.objects.input_password.val().length < 6) {
                    this.objects.input_password.addClass('error');
                    errors++;
                } else { this.objects.input_password.removeClass('error'); }
            }
        } else { this.objects.input_password.removeClass('error'); }

        if(errors == 0) {
            var endpoint = site.base_url+'users/manage/'+this.data.mode;
            var data = $(e.target).serialize()+
                       '&page='+admin_app.user.data.page.current+
                       '&limit='+admin_app.user.data.page.limit;
            if(this.data.last_signature != data) {
                $.ajax({
                    method: 'post',
                    url: endpoint,
                    data: data,
                    context: this,
                    error: function(jqXHR,textStatus,errorThrown){
                        toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                        this.enableFields();
                    },
                    success: function(response) {
                        if(response.status == "ok") {
                            toastr["success"](response.message);
                            this.enableFields();
                            this.self.modal('hide');
                            admin_app.user.getData.call(admin_app.user,response.data);
                            this.data.last_signature = data;
                        }
                        else {
                            this.enableFields();
                            toastr["error"](response.message);
                            this.self.modal('hide');
                        }
                    }
                });
            }
            else {
                this.self.modal('hide');
            }
        }
    },
    disableFields: function() {
        this.objects.input_fname.prop('disabled',true);
        this.objects.input_lname.prop('disabled',true);
        this.objects.input_email.prop('disabled',true);
        this.objects.input_password.prop('disabled',true);
        this.objects.input_role.prop('disabled',true);
        this.objects.input_status.prop('disabled',true);
        this.objects.input_verify.prop('disabled',true);
        this.objects.toggle_pass.prop('disabled',true);
        this.objects.buttons.prop('disabled',true);
    },
    enableFields: function() {
        this.objects.input_fname.prop('disabled',false);
        this.objects.input_lname.prop('disabled',false);
        this.objects.input_email.prop('disabled',false);
        this.objects.input_role.prop('disabled',false);
        this.objects.input_status.prop('disabled',false);
        this.objects.input_verify.prop('disabled',false);
        this.objects.toggle_pass.prop('disabled',false);
        if(this.objects.input_verify.is(':checked')) {
            this.objects.toggle_pass.prop('disabled',true);
            this.objects.input_password.prop('disabled',true);
        }
        else {
            this.objects.toggle_pass.prop('disabled',false);
            if(this.objects.toggle_pass.is(':checked')) {
                this.objects.input_password.prop('disabled',false);
            }
            else {
                this.objects.input_password.prop('disabled',true);
            }
        }
        this.objects.buttons.prop('disabled',false);
    },
    togglePass: function(e) {
        var input = $(e.target);
        if(input.is(':checked')) {
            this.objects.input_password.prop('disabled',false);
        }
        else {
            this.objects.input_password.prop('disabled',true);
            this.objects.input_password.removeClass('error');
        }
    },
    toggleVerify: function(e) {
        var input = $(e.target);
        if(input.is(':checked')) {
            this.objects.toggle_pass.prop('disabled',true);
            this.objects.input_password.prop('disabled',true);
        }
        else {
            this.objects.toggle_pass.prop('disabled',false);
            if(this.objects.toggle_pass.is(':checked')) {
                this.objects.input_password.prop('disabled',false);
            }
            else {
                this.objects.input_password.prop('disabled',true);
            }
        }
    }
};
