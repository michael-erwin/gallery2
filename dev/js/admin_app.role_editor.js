admin_app.role_editor =
{
    self: $('#modal_role_editor'),
    config: {
        
    },
    objects: {
        'form': null,
        'modal_title_sub': null,
        'input_id': null,
        'input_name': null,
        'input_description': null,
        'list_box': null,
        'toggleAll': null
    },
    data: {
        mode: null,
        permission_list: [],
        item: {
            'id': null,
            'name': "",
            'description': "",
            'permissions': ""
        },
        last_sinature: null
    },
    init: function() {
        this.self = $('#modal_role_editor');
        this.objects.form = this.self.find('form');
        this.objects.input_id = this.self.find('input[name="id"]');
        this.objects.input_name = this.self.find('input[name="name"]');
        this.objects.input_description = this.self.find('textarea[name="description"]');
        this.objects.modal_title_sub = this.self.find('h4.modal-title span');
        this.objects.list_box = this.self.find('table[data-id="list"]');
        this.objects.toggleAll = this.self.find('[data-id="toggle_select_all"] input');

        // Modal setup.
        this.self.modal({backdrop: 'static'}).modal('hide');

        // Reset settings.
        this.data.mode = null;

        // Event bindings.
        this.objects.form.unbind().on('submit',this.save.bind(this));
        this.objects.toggleAll.unbind().on('click',this.toggleSelectAll.bind(this));

        // Initialize events.
        this.getData();
    },
    render: function() {
        // Build permission table entries.
        var list_rows = "";
        if(this.data.permission_list.length > 0) {
            for (var i = 0; i < this.data.permission_list.length; i++) {
                var permission = this.data.permission_list[i];
                list_rows += 
                '<tr>'+
                    '<td><input type="checkbox" name="permission[]" value="'+permission.name+'"></td>'+
                    '<td title="'+permission.description+'">'+permission.name+'</td>'+
                '</tr>';
            }
        }
        else {
            list_rows = 
            '<tr colspan="3">'+
                '<td>No permission_list found.</td>'+
            '</tr>';
        };
        this.objects.list_box.html(list_rows);
        // Display behavior.
        if(this.data.mode == "add") {
            this.objects.input_id.val(null).prop('disabled',true);
            this.objects.input_name.removeClass('error');
            this.objects.input_description.removeClass('error');
            this.objects.modal_title_sub.text("New");
            this.self.modal('show');
        };
        if(this.data.mode == "update") {
            var current_permissions = this.data.item.permissions.split(',');
            this.objects.input_id.prop('disabled',false).val(this.data.item.id);
            this.objects.input_name.val(this.data.item.name).removeClass('error');
            this.objects.input_description.val(this.data.item.description).removeClass('error');
            this.self.find('input[name="permission[]"]').each(function(){
                var row = $(this).parents('tr');
                var $this = $(this);
                var permission = $this.val();
                for(var i=0; i<current_permissions.length; i++) {
                    if(permission == current_permissions[i]) {
                        $this.prop('checked',true);
                        row.addClass('active');
                    }
                }
            });
            this.objects.modal_title_sub.text("Edit");
            this.self.modal('show');
        };
        // Event bindings.
        this.self.find('tr').unbind().on('click',this.toggleSelect.bind(this));
    },    
    getData: function(data) {
        if(data) {
            this.data.permission_list = data;
            this.render();
        }
        else{
            $.ajax({
                url: site.base_url+'roles/manage/get_permissions',
                method: 'get',
                data: "",
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.data.permission_list = response.data;
                        this.render();
                    }
                    else {
                        toastr["error"](response.message);
                    }
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
        if(data){this.data.item = data};
        this.data.mode = 'update';
        this.render();
    },
    save: function(e) {
        e.preventDefault();
        var errors = 0;
        var input_name = this.objects.input_name;

        if($.trim(input_name.val()).length < 2) {
            input_name.addClass('error');
            errors++;
        }
        else {
            input_name.removeClass('error');
        }

        if(errors == 0) {
            var endpoint = site.base_url+'roles/manage/'+this.data.mode;
            var data = $(e.target).serialize();
            if(this.data.last_sinature != data) {
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
                            admin_app.role.getData.call(admin_app.role,response.data);
                            this.data.last_sinature = data;
                        }
                        else {
                            this.enableFields();
                            toastr["error"](response.message);
                            this.self.modal('hide');
                        }
                    }
                });
                this.disableFields();
            }
            else {
                this.self.modal('hide');
            }
        }
    },
    disableFields: function() {
        this.objects.form.find('input').prop('disabled',true);
        this.objects.form.find('textarea').prop('disabled',true);
        this.objects.form.find('button').prop('disabled',true);
        this.self.find('tr').unbind('click').addClass('disabled');
    },
    enableFields: function() {
        this.objects.form.find('input').prop('disabled',false);
        this.objects.form.find('textarea').prop('disabled',false);
        this.objects.form.find('button').prop('disabled',false);
        this.self.find('tr').unbind().on('click',this.toggleSelect.bind(this)).removeClass('disabled');
    },
    toggleSelect: function(e) {
        var node = e.target.nodeName;
        var row  = $(e.target).parents('tr');
        if(node == "INPUT") {
            var input = $(e.target);
            if(input.is(':checked')) {
                row.addClass('active');
            }
            else {
                row.removeClass('active');
            }
        }
        else {
            var input = row.find('input');
            if(input.is(':checked')) {
                input.prop('checked',false);
                row.removeClass('active');
            }
            else {
                input.prop('checked',true);
                row.addClass('active');
            }
        }
    },
    toggleSelectAll: function(e) {
        var $this = $(e.target);
        var inputs = this.objects.list_box.find('[name="permission[]"]');
        if($this.is(':checked')) {
            inputs.each(function(){
                $(this).prop('checked',true)
                .parents('tr').addClass('active');
            })
        }
        else {
            inputs.each(function(){
                $(this).prop('checked',false)
                .parents('tr').removeClass('active');
            })
        }
    }
};
