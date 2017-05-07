admin_app.presentation_content_editor =
{
    self: '#presentation_content_editor',
    config: {
        mode: 'new'
    },
    objects: {
        
    },
    data: {
        items: []
    },
    init: function() {
        this.self = $(this.self);
        
    },
    render: function() {
        
    },
    new: function() {
        this.config.mode = "new";
    },
    edit: function() {
        this.config.mode = "edit";
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
    }
};
