admin_app.visibility_editor =
{
    self: '#modal_visibility_editor',
    config: {
        
    },
    objects: {
        'form': null,
        'main_radios': null,
        'protected_block': null,
    },
    data: {
        visibility: "public",
        user_ids: []
    },
    init: function() {
        this.self = $(this.self);
        this.objects.main_radios = this.self.find('[name="visibility"]');
        this.objects.protected_block = this.self.find('div.protected-content');

        // Modal setup.
        this.self.modal({backdrop: 'static'}).modal('hide');

        // Event bindings.
        this.self.find('[data-id="save_btn"]').unbind('click').on('click',this.save.bind(this));
        this.objects.main_radios.unbind('click').on('click',this.setValue.bind(this));

        // Initialize events.
        //this.getData();
        this.render();
    },
    render: function() {
        // Visibility display logic.
        $this = this.data.visibility;
        this.objects.main_radios.each(function(){
            if($(this).val() == $this){
                $(this).prop('checked',true);
            }else{
                $(this).prop('checked',false);
            }
        });
        if(this.data.visibility == "protected"){
            this.objects.protected_block.css('display','block');
        }else{
            this.objects.protected_block.css('display','none');
        }
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
    open: function(type) {
        this.self.modal();
    },
    save: function(e) {
        e.preventDefault();
        var errors = 0;
        console.log("Save clicked.");
    },
    setValue: function(e){
        this.data.visibility = e.target.value;
        this.render.call(this);
    },
    disableFields: function() {
        
    },
    enableFields: function() {
        
    }
};
