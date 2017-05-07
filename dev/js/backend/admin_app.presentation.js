admin_app.presentation =
{
    self: $('div.content-wrapper'),
    config: {},
    objects: {
        'new_button': null,
        'table_body': null
    },
    data: {
        items: []
    },
    init: function() {
        // Identify objects.
        this.objects.new_button = this.self.find('[data-id="new_button"]');
        this.objects.table_body = this.self.find('#presentation_table tbody');
        // Bind events
        this.objects.new_button.unbind().on('click',this.new);
        // Get data.
        if($.inArray('all',site.permissions) !== -1 || $.inArray('presentation_edit',site.permissions) !== -1){
            this.getData();
        }else{
            this.render();
        }
    },
    render: function() {
        if($.inArray('all',site.permissions) !== -1 || $.inArray('presentation_add',site.permissions) !== -1 || $.inArray('presentation_edit',site.permissions) !== -1){
            // Enable view.
            this.objects.new_button.prop('disabled',false);
            $('.content-block').css('display','block');
            $('#message_block').css('display','none');

            // Build HTML rows content of table.
            var table_html = '';
            var option_button = '';

            if($.inArray('all',site.permissions) !== -1 || $.inArray('presentation_add',site.permissions) !== -1){
                option_button += '<button class="btn btn-primary btn-xs mini" data-id="edit_media" title="Add or edit media items."><i class="fa fa-file-image-o"></i></button>';
            }
            if($.inArray('all',site.permissions) !== -1 || $.inArray('presentation_edit',site.permissions) !== -1){
                option_button += '\n<button title="Sharing" class="btn btn-primary btn-xs mini" data-id="sharing"><i class="fa fa-share-alt"></i></button>';
            }
            if($.inArray('all',site.permissions) !== -1 || $.inArray('presentation_edit',site.permissions) !== -1){
                option_button += '\n<button class="btn btn-primary btn-xs mini" data-id="edit_entry" title="Edit basic details."><i class="fa fa-pencil"></i></button>';
            }
            if($.inArray('all',site.permissions) !== -1 || $.inArray('presentation_delete',site.permissions) !== -1){
                option_button += '\n<button class="btn btn-danger btn-xs mini" data-id="delete_entry" title="Delete this entry."><i class="fa fa-trash"></i></button>';
            }

            for(var i=0;i<this.data.items.length;i++) {
                var item = this.data.items[i];
                var count = ($.trim(item.items).length == 0)? 0 : item.items.split(',').length;
                table_html += '<tr data-all=\''+(JSON.stringify(item)).replace(/[']/g,"&#39;")+'\'>'+
                                '<td>'+item.title+'</td><td>'+item.description+'</td><td>'+item.share_level+'</td><td>'+count+'</td><td>'+option_button+'</td>'+
                              '</tr>';
            }
            this.objects.table_body.html(table_html);
            // Attach events.
            this.self.find('button[data-id="edit_media"]').unbind().on('click',this.edit_media.bind(this));
            this.self.find('button[data-id="edit_entry"]').unbind().on('click',this.edit_basic.bind(this));
            this.self.find('button[data-id="sharing"]').unbind().on('click',this.share.bind(this));
            this.self.find('button[data-id="delete_entry"]').unbind().on('click',this.delete.bind(this));
        }else{
            this.objects.new_button.prop('disabled',true);
            $('.content-block').css('display','none');
            $('#message_block .alert').text("You don't have enough permission to view this content.");
            $('#message_block').css('display','block');
        }
    },
    getData: function(data) {
        if(data) {
            this.data.items = data;
            this.render();
        }
        else{
            $.ajax({
                url: site.base_url+'presentations/get',
                method: 'get',
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.data.items = response.data.items;
                        this.render();
                    } else {
                        toastr["error"](response.message, "Error");
                    }
                }
            });
        }
    },
    new: function(e) {
        admin_app.presentation_entry_editor.new.call(admin_app.presentation_entry_editor);
    },
    edit_basic: function(e) {
        var parent = $(e.target).parents('tr');
        var data = parent.data('all');
        admin_app.presentation_entry_editor.edit.call(admin_app.presentation_entry_editor,data);
    },
    edit_media: function(e) {
        var parent = $(e.target).parents('tr');
        var data = parent.data('all');
        toastr['info']('No function implemented yet.','Info');
    },
    share: function(e) {
        var parent = $(e.target).parents('tr');
        var data = parent.data('all');
        admin_app.visibility_editor.open("presentations",data);
    },
    delete: function(e) {
        var parent = $(e.target).parents('tr');
        var data = parent.data('all');
        var id = data.id;
        var doDelete = function() {
            $.ajax({
                url: site.base_url+'presentations/delete',
                method: "post",
                data: 'id='+id,
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to reach content.", "Error "+jqXHR.status);
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.getData();
                        toastr["success"](response.message);
                    }
                    else {
                        toastr["error"](response.message, "Error");
                    }
                }
            });
        };
        modal.confirm('Do you want to delete entry named "'+data.title+'"?',doDelete.bind(this));
    }
};
