admin_app.presentation_entry_editor =
{
    self: '#modal_presentation_entry_editor',
    config: {
        mode: 'new'
    },
    objects: {
        form: null,
        title: null,
        description: null,
        save: null
    },
    data: {
        item: {
            title: "",
            description: ""
        }
    },
    init: function() {
        this.self = $(this.self);
        this.objects.form = this.self.find('form')[0];
        this.objects.title = this.self.find('[name="title"]');
        this.objects.description = this.self.find('[name="description"]');
        this.objects.save = this.self.find('[role="save_btn"]');
        // Bind events
        this.objects.save.unbind('click').on('click',this.save.bind(this));
    },
    render: function() {
        this.enableState();
        this.self.find('input').removeClass('error');
        this.self.find('textarea').removeClass('error');
        if(this.config.mode == "new") this.self.find('[role="mode"]').text('New');
        if(this.config.mode == "edit") this.self.find('[role="mode"]').text('Edit');
        this.objects.title.val(this.data.item.title);
        this.objects.description.val(this.data.item.description);
        $(this.self).modal({backdrop: 'static'});
    },
    new: function() {
        this.config.mode = "new";
        this.objects.form.reset();
        this.data.item = { title: "", description: "" };
        this.render();
    },
    edit: function(data) {
        this.config.mode = "edit";
        this.data.item = { id: data.id, title: data.title, description: data.description };
        this.render();
    },
    save: function() {
        var errors = 0;
        var data = {
            title: $.trim(this.objects.title.val()),
            description: $.trim(this.objects.description.val()),
            items: ""
        };
        if(data.title.length == 0) {
            this.objects.title.addClass('error');
            errors++;
        } else if(data.title.length < 3) {
            this.objects.title.addClass('error');
            errors++;
            toastr['error']('Title is too short','Error')
        } else {
            this.objects.title.removeClass('error');
        }
        
        if(errors == 0)
        {
            this.data.item.title = data.title;
            this.data.item.description = data.description;
            this.disableState();
            var endpoint = 'presentations/add';
            if(this.config.mode == "edit") endpoint = 'presentations/update';
            $.ajax({
                url: site.base_url+endpoint,
                method: "post",
                data: this.data.item,
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                    this.enableState();
                },
                success: function(response) {
                    this.enableState();
                    if(response.status == "ok") {
                        toastr["success"](response.message, "Success");
                        this.self.modal('hide');
                        admin_app.presentation.getData.call(admin_app.presentation);
                    } else {
                        toastr["error"](response.message, "Error");
                    }
                }
            });
        }
    },
    disableState: function() {
        this.self.find('input').prop('disabled',true);
        this.self.find('textarea').prop('disabled',true);
        this.self.find('button').prop('disabled',true);
    },
    enableState: function() {
        this.self.find('input').prop('disabled',false);
        this.self.find('textarea').prop('disabled',false);
        this.self.find('button').prop('disabled',false);
    }
};
