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
        if($.inArray('all',site.permissions) !== -1 || $.inArray('category_view',site.permissions) !== -1){
            this.getData();
        }else{
            this.render();
        }
    },
    render: function() {
        if($.inArray('all',site.permissions) !== -1 || $.inArray('category_view',site.permissions) !== -1){
            // Enable view.
            this.objects.new_button.prop('disabled',false);
            $('.content-block').css('display','block');
            $('#message_block').css('display','none');



            // Build HTML rows content of table.
            var table_html = "";
            for(var item in this.data.items){

                if($.inArray('all',site.permissions) !== -1 || $.inArray('category_add',site.permissions) !== -1){
                    this.objects.new_button.prop('disabled',false);
                }else{
                    this.objects.new_button.prop('disabled',true);
                }
                
            };
            this.objects.table_body.html(table_html);
            // Attach events.
            // this.self.find('button[data-id="new_entry"]').unbind().on('click',this.new_subcat.bind(this));
            // this.self.find('button[data-id="edit_entry"]').unbind().on('click',this.edit.bind(this));
            // this.self.find('button[data-id="sharing"]').unbind().on('click',this.share.bind(this));
            // this.self.find('button[data-id="delete_entry"]').unbind().on('click',this.delete.bind(this));
            // this.self.find('#categories_table tbody i.active').unbind().on('click',this.toggleSubcat.bind(this));
        }else{
            this.objects.new_button.prop('disabled',true);
            $('.content-block').css('display','none');
            $('#message_block .alert').text("You don't have enough permission to view this content.");
            $('#message_block').css('display','block');
        }
    },
    getData: function(data) {
        /*if(data) {
            this.data.media_list = data;
            this.render();
        }
        else{
            $.ajax({
                url: site.base_url+'admin/media/categories/json',
                method: 'get',
                data: 'list='+this.objects.media_type_dropdown.val(),
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                },
                success: function(response) {
                    this.data.media_list = response;
                    this.render();
                }
            });
        }*/
    },
    new: function(e) {
        admin_app.presentation_editor.open.call(admin_app.presentation_editor);
    },
    edit: function(e) {
        var parent = $(e.target).parents('tr');
        var data = JSON.parse(parent.attr('data-all'));
        admin_app.presentation_editor.edit.call(admin_app.presentation_editor,data);
    },
    share: function(e) {
        var data = $(e.target).parents('tr').data('all');
        admin_app.visibility_editor.open("categories",data);
    },
    delete: function(e) {
        console.log("Delete triggered.");
        var parent = $(e.target).parents('tr');
        var data = JSON.parse(parent.attr('data-all'));
        var id = data.id;
        var doDelete = function() {
            $.ajax({
                url: site.base_url+'categories/manage/delete',
                method: "post",
                data: 'id='+id+'&type='+this.objects.media_type_dropdown.val(),
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
        modal.confirm('Associated media under presentation "'+data.title+'" will be moved to uncategorized. Do you want to continue?',doDelete.bind(this));
    }
};
