admin_app.role =
{
    self: $('div.content-wrapper'),
    config: {
        expanded_items: {}
    },
    objects: {
        'new_button': null,
        'table_body': null
    },
    data: {
        items: []
    },
    init: function() {
        this.objects.new_button = this.self.find('button[data-id="new_button"]');
        this.objects.table_body = this.self.find('tbody[data-id="list"]');

        // Bind events.
        this.objects.new_button.on('click',this.new.bind(this));
        this.getData.call(this);
    },
    render: function() {
        // Build table entries.
        var list_rows = "";
        if(this.data.items.length > 0) {
            for (var i = 0; i < this.data.items.length; i++) {
                var role = this.data.items[i];
                var permissions = role.permissions.split(',');
                var permissions_html = "";
                for(var n=0;n<permissions.length;n++) {
                    permissions_html += '<span class="label label-primary">'+permissions[n]+'</span>\n'
                };
                var options = "";
                if(role.core == "no") {
                    options = 
                        '<button class="btn btn-primary btn-xs" data-id="edit_entry" title="Edit">'+
                            '<i class="fa fa-pencil"></i>'+
                        '</button>&nbsp;'+
                        '<button class="btn btn-danger btn-xs" data-id="delete_entry" title="Delete">'+
                            '<i class="fa fa-trash"></i>'+
                        '</button>';
                }
                list_rows += 
                '<tr data-info=\''+JSON.stringify(role)+'\'>'+
                    '<td>'+role.name+'</td>'+
                    '<td>'+role.description+'</td>'+
                    '<td>'+permissions_html+'</td>'+
                    '<td>'+options+'</td>'+
                '</tr>';
            }
        }
        else {
            list_rows = 
            '<tr colspan="4">'+
                '<td>No items found.</td>'+
            '</tr>';
        };
        this.objects.table_body.html(list_rows);
        // Event bindings.
        this.self.find('button[data-id="edit_entry"]').unbind().on('click',this.edit.bind(this));
        this.self.find('button[data-id="delete_entry"]').unbind().on('click',this.delete.bind(this));
    },
    getData: function(data) {
        if(data) {
            this.data.items = data;
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
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.data.items = response.data;
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
        admin_app.role_editor.new.call(admin_app.role_editor);
    },
    edit: function(e) {
        var row = $(e.target).parents("tr");
        var data = row.data('info');
        admin_app.role_editor.edit.call(admin_app.role_editor,data);
    },
    delete: function(e) {
        var row = $(e.target).parents("tr");
        var data = row.data('info');
        var id = data.id;
        var doDelete = function() {
            $.ajax({
                url: site.base_url+'roles/manage/delete',
                method: "post",
                data: 'id='+id,
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to reach content.", "Error "+jqXHR.status);
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.getData(response.data);
                        toastr["success"](response.message);
                    }
                    else {
                        toastr["error"](response.message, "Error");
                    }
                }
            });
        };
        modal.confirm('Associated users with the role will be moved to guest. Do you want to delete a role?',doDelete.bind(this));
    }
};
