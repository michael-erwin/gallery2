admin_app.category_editor =
{
    self: '#modal_category_editor',
    objects: {
        'modal_title': null,
        'editor_form': null,
        'publish_options': null,
        'category_levels': null,
        'category_parent_dropdown': null,
        'parent_level_block': null,
        'icon_image': null,
        'icon_mesage': null,
        'icon_setdefault_option': null,
        'item': {
            'id': null,
            'title': null,
            'icon': null,
            'description': null,
            'category_level_main': null,
            'category_level_sub': null,
            'publish_yes': null,
            'publish_no': null
        },
        save_button: null,
        cancel_button: null
    },
    data: {
        editor_title: 'New Category',
        editor_visible: false,
        item: {
            id: "",
            level: null,
            type: null,
            title: "",
            description: "",
            icon: "",
            icon_default: 0,
            publish: "no"
        },
        type: "",
        task: 'add'
    },
    settings: {
        url_pattern: 'https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)',
        disabled: {
            'id': false,
            'title': false,
            'description': false,
            'category_parent_input': false,
            'category_level_main': false,
            'category_level_sub': false,
            'publish_yes': false,
            'publish_no': false
        }
    },
    init: function() {
        // Identify objects.
        this.self = $(this.self);
        this.objects.modal_title = this.self.find('h4.modal-title');
        this.objects.editor_form = this.self.find('form[data-id="editor_form"]');
        this.objects.parent_level_block = this.self.find('[data-id="parent_category"]');
        this.objects.icon_image = this.self.find('[data-id="icon-preview-box"] img');
        this.objects.icon_message = this.self.find('[data-id="icon-box-message"]');
        this.objects.icon_setdefault_option = this.self.find('[name="icon_default"]');
        this.objects.publish_options = this.self.find('[name="publish"]');
        this.objects.category_levels = this.self.find('[name="level"]');
        this.objects.category_parent_dropdown = this.self.find('[data-id="parent_category"] select');
        this.objects.item.id = this.self.find('[name="id"]');
        this.objects.item.title = this.self.find('[name="title"]');
        this.objects.item.description = this.self.find('[name="description"]');
        this.objects.item.icon = this.self.find('[name="icon"]');
        this.objects.item.category_level_main = this.self.find('[name="level"][value="1"]');
        this.objects.item.category_level_sub = this.self.find('[name="level"][value="2"]');
        this.objects.item.publish_yes = this.self.find('[name="publish"][value="yes"]');
        this.objects.item.publish_no = this.self.find('[name="publish"][value="no"]');
        this.objects.item.save_button = this.self.find('button[type="submit"]');
        this.objects.item.cancel_button = this.self.find('button[type="button"]');
        // Set UI behavior.
        this.self.unbind('hidden.bs.modal').on('hidden.bs.modal', (function(){
            this.data.editor_visible = false;
            this.objects.icon_image.css('display','none');
        }).bind(this));
        this.self.modal({backdrop: 'static'}).modal('hide');
        this.settings.disabled = {
            'id': false,
            'title': false,
            'description': false,
            'publish_yes': false,
            'publish_no': false
        };
        // Bind event handlers.
        this.objects.item.icon.unbind().on('blur',this.setIcon.bind(this));
        this.objects.icon_image.unbind().on('load',this.setIconSuccess.bind(this));
        this.objects.icon_image.on('error',this.setIconError.bind(this));
        this.objects.category_levels.unbind().on('click',this.setLevel.bind(this));
        this.objects.editor_form.unbind().on('submit',this.save.bind(this));
    },
    render: function() {
        // Update parent category dropdown list box contents.
        var parent_dropdown_html = '';
        for(var i=0;i<admin_app.category.data.media_list.length;i++){
            var current_item = admin_app.category.data.media_list[i];
            if((current_item.id > 1) && (current_item.level == 1)) parent_dropdown_html += '<option value="'+current_item.id+'">'+current_item.title+'</option>';
        };
        this.objects.parent_level_block.find('select.list').html(parent_dropdown_html);
        // Update input field values.
        this.self.find('.error').removeClass('error');
        this.objects.modal_title.text(this.data.editor_title);
        this.objects.item.id.val(this.data.item.id);
        this.objects.item.title.val(this.data.item.title);
        this.objects.item.description.val(this.data.item.description);
        this.objects.item.icon.val(this.data.item.icon);
        // Update image icon displayed.
        var new_url = $.trim(this.objects.item.icon.val());
        if(new_url != ""){
            var pattern = new RegExp(this.settings.url_pattern,"i");
            if(pattern.test(new_url)){
                this.objects.icon_image.attr('src',new_url);
            }
            else{
                this.objects.icon_image.attr('src',site.base_url+new_url);
            }
        }
        else{
            this.objects.icon_image.css('display','none');
            this.objects.icon_message.text('No image.').css('display','block');
        };
        // Update default thumbnail image checkbox.
        if(this.data.item.icon_default == 1){
            this.objects.icon_setdefault_option.prop('checked',true);
        }
        else{
            this.objects.icon_setdefault_option.prop('checked',false);
        }
        // Update category level radio buttons & sub category dropdown display logic.
        if(this.self.find('[name="level"]:checked').val() == 1){
            this.objects.parent_level_block.css('display','none');
            this.objects.icon_setdefault_option.prop('checked',false).parent("label").css('display','none');
        }
        else{
            this.objects.category_parent_dropdown.val(this.data.item.parent_id);
            this.objects.icon_setdefault_option.parent("label").css('display','inline');
            this.objects.parent_level_block.css('display','block');
        };
        // Update publish radio button display logic.
        if(this.data.item.published == "yes") {
            this.objects.item.publish_no.prop('checked',false);
            this.objects.item.publish_yes.prop('checked',true);
        }
        else if(this.data.item.published == "no") {
            this.objects.item.publish_yes.prop('checked',false);
            this.objects.item.publish_no.prop('checked',true);
        };
        if(this.data.editor_visible) {
            this.self.modal('show');
        }
        else {
            this.self.modal('hide');
        };
        // Disabled items.
        for(var item in this.settings.disabled) {
            if(this.settings.disabled[item]) {
                this.objects.item[item].prop('disabled',true);
            }
            else {
                this.objects.item[item].prop('disabled',false);
            }
        }
    },
    new: function(parent_id = "") {
        this.data.task = "add";
        this.data.item = {
            id: "",
            title: "",
            type: admin_app.category.data.media_type,
            level: 1,
            description: "",
            icon: "",
            parent_id: 0,
            publish: "no"
        };
        if(typeof parent_id !== "object"){
            this.data.item.level = 2;
            this.data.item.parent_id = parent_id;
            console.log(typeof parent_id);
        };
        this.settings.disabled['id'] = false;
        this.settings.disabled['title'] = false;
        this.settings.disabled['description'] = false;
        this.settings.disabled['publish_yes'] = false;
        this.settings.disabled['publish_no'] = false;
        this.settings.disabled['category_level_main'] = false;
        this.settings.disabled['category_level_sub'] = false;
        this.data.editor_title = "New Category ("+admin_app.category.data.media_type.ucfirst()+")";
        this.data.editor_visible = true;
        this.objects.category_levels.each(function(){
            if($(this).val() == 1) {
                if(typeof parent_id !== "object"){
                    $(this).prop('checked',false);
                }
                else{
                    $(this).prop('checked',true);
                }
            }
            else {
                if(typeof parent_id !== "object"){
                    $(this).prop('checked',true);
                }
                else{
                    $(this).prop('checked',false);
                }
            }
        });
        this.render();
    },
    edit: function(data) {
        this.data.task = "update";
        this.data.item = data;
        this.objects.category_levels.each(function(){
            if($(this).val() == data.level) {
                $(this).prop('checked',true);
            }
            else {
                $(this).prop('checked',false);
            }
        });
        if(data.level == 1){
            this.settings.disabled['category_level_main'] = false;
            this.settings.disabled['category_level_sub'] = true;
        }
        else{
            this.settings.disabled['category_level_main'] = true;
            this.settings.disabled['category_level_sub'] = false;
        };
        if(data.core == "yes") {
            this.settings.disabled['title'] = true;
            this.settings.disabled['description'] = true;
        }
        else {
            this.settings.disabled['title'] = false;
            this.settings.disabled['description'] = false;
        };
        this.settings.disabled['publish_yes'] = false;+")";
        this.settings.disabled['publish_no'] = false;

        this.data.editor_title = "Edit Category ("+admin_app.category.data.media_type.ucfirst()+")";
        this.data.editor_visible = true;
        this.render();
    },
    setIcon: function() {
        $this = this.objects.item.icon;
        var pattern = new RegExp(this.settings.url_pattern,"i");
        var url = $.trim($this.val());
        if(url != ""){
            if(this.data.item.icon != url){
                this.objects.icon_message.text('Loading...').css('display','block');
                if(pattern.test(url)){
                    this.objects.icon_image.css('display','none').attr('src',url);
                }
                else{
                    this.objects.icon_image.css('display','none').attr('src',site.base_url+url);
                }
            }
        }
        else{
            this.objects.icon_image.css('display','none');
            this.objects.icon_message.text('No image.').css('display','block');
        }
    },
    setIconError: function(){
        this.objects.icon_image.css('display','none');
        this.objects.icon_message.text('Unable to load image.').css('display','block');
    },
    setIconSuccess: function(){
        this.objects.icon_image.css('display','inline-block');
        this.objects.icon_message.text('Default.').css('display','none');
    },
    setLevel: function() {
        this.data.item.title = this.objects.item.title.val();
        this.data.item.description = this.objects.item.description.val();
        this.data.item.icon = this.objects.item.icon.val();
        this.render();
    },
    save: function(e) {
        e.preventDefault();
        var errors = 0;
        this.data.item.id = this.objects.item.id.val();
        this.data.item.title = this.objects.item.title.val();
        this.data.item.description = this.objects.item.description.val();
        this.data.item.icon = this.objects.item.icon.val();
        this.data.item.publish = this.self.find('[name="publish"]:checked').val();
        this.data.item.level = this.self.find('[name="level"]:checked').val();
        // Validate inputs.
        if($.trim(this.data.item.title).length == 0) {
            this.objects.item.title.addClass('error');
            errors++;
        };
        if(this.objects.icon_setdefault_option.is(":checked")){
            this.data.item.icon_default = 1;
            var icon_url = $.trim(this.objects.item.icon.val());
            if(icon_url == ""){
                this.objects.item.icon.addClass('error');
                errors++;
            }
            else{
                this.objects.item.icon.removeClass('error');
            }
        }
        else{
            this.data.item.icon_default = 0;
            this.objects.item.icon.removeClass('error');
        };
        if(this.data.item.level == 1){
            this.data.item.parent_id = 0;
        }
        else{
            var category_parent = this.objects.category_parent_dropdown;
            if(category_parent.val() === null){
                this.data.item.parent_id = 0;
                category_parent.addClass('error');
                errors++;
            }
            else{
                category_parent.removeClass('error');
                this.data.item.parent_id = category_parent.val();
            }
        };
        if(errors === 0) {
            this.objects.item.title.removeClass('error');
            this.disableState();
            var data = this.data.item;
            $.ajax({
                url: site.base_url+'categories/manage/'+this.data.task,
                method: "post",
                data: data,
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.data.editor_visible = false;
                        this.render();
                        admin_app.category.getData.call(admin_app.category,response.data);
                    }
                    else {
                        this.data.editor_visible = false;
                        this.render();
                        toastr["error"](response.message, "Error");
                    }
                }
            });
        };
        return false;
    },
    disableState: function() {
        this.settings.disabled = {
            'id': true,
            'title': true,
            'description': true,
            'publish_yes': true,
            'publish_no': true
        };
        this.render();
    },
    enableState: function() {
        this.settings.disabled = {
            'id': false,
            'title': false,
            'description': false,
            'publish_yes': false,
            'publish_no': false
        };
        this.render();
    }
};
