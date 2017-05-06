admin_app.visibility_editor =
{
    self: '#modal_visibility_editor',
    config: {
        selected_level: 'public'
    },
    objects: {
        'form': null,
        'main_radios': null,
        'search_box': null,
        'hint_box': null,
        'email_tag_box': null,
        'special_link_box': null,
        'special_link_toggle': null
    },
    search_clock: null,
    data: {
        type: '',
        item: {
            'id': null,
            'share_level': null,
            'pvt_share_id': null
        },
        allowed_users: [],
        search_hints: []
    },
    init: function() {
        this.self = $(this.self);
        this.objects.form = this.self.find('[data-id="editor_form"]');
        this.objects.search_box = this.self.find('[role="search-box"]');
        this.objects.hint_box = this.self.find('[role="hint-box"]');
        this.objects.main_radios = this.self.find('[name="visibility"]');
        this.objects.email_tag_box = this.self.find('ul.input-tags');
        this.objects.special_link_box = this.self.find('[role="special_link_box"]');
        this.objects.special_link_toggle = this.self.find('[role="special_link_toggle"]');

        // Modal setup.
        this.self.modal({backdrop: 'static'}).modal('hide');

        // Event bindings.
        this.objects.search_box.unbind('keydown').on('keydown',this.liveSearch.bind(this));
        this.objects.form.submit(this.save.bind(this));
        this.self.find('[data-id="save_btn"]').unbind('click').on('click',this.save.bind(this));
        this.objects.main_radios.unbind('click').on('click',this.setValue.bind(this));
        this.objects.hint_box.delegate("li",{
            mouseover: this.hintHover.bind(this),
            mousedown: this.addEmail.bind(this)
        });
        this.objects.email_tag_box.delegate('.del').on('click',this.removeEmail);
        this.objects.special_link_box.unbind().on('focus',this.focusSpecialLinkBox.bind(this));
        this.objects.special_link_toggle.unbind().on('click',this.toggleSpecialLink.bind(this));

        // Initialize events.
        this.render();
    },
    render: function() {
        // Visibility display logic.
        var selected_level = this.config.selected_level;
        this.objects.form[0].reset();
        this.objects.main_radios.prop('checked',false);
        this.objects.email_tag_box.html("");
        var selected_radio = this.self.find('input[value="'+selected_level+'"]');
        var selected_tabid = selected_radio.data('for');
        this.self.find('.content-block').css('display','none');
        this.self.find('.content-block.'+selected_tabid).css('display','block');
        selected_radio.prop('checked',true);
        if(selected_level == 'protected'){
            for(var i=0;i<this.data.allowed_users.length;i++) {
                this.addEmail.call(this,this.data.allowed_users[i]);
            }
        } else if(selected_level == 'private') {
            var hash = $.trim(this.data.item.pvt_share_id);
            var link = site.base_url+this.data.type+'/share/get/'+hash;
            if(hash.length > 0){
                this.objects.special_link_toggle.prop('checked',true);
            }else{
                this.objects.special_link_toggle.prop('checked',false);
                link = "";
            }
            if(this.objects.special_link_toggle.is(':checked')) {
                this.objects.special_link_box.val(link).prop('disabled',false);
            } else {
                this.objects.special_link_box.val(link).prop('disabled',true);
            }
            if((this.data.item.id.split(',')).length > 1) {
                this.self.find('.content-block.'+selected_tabid).css('display','none');
            }
        }
    },
    open: function(type,data) {
        this.data.allowed_users = [];
        this.data.type = type;
        this.data.item = {
            id: data.id,
            share_level: data.share_level,
            pvt_share_id: data.pvt_share_id
        };
        if(data.level) this.data.item.level = data.level;
        if(data.subcats){
            var sub_ids = [];
            for(var subcat in data.subcats){
                sub_ids.push(subcat.split('s_')[1]);
            }
            this.data.item.sub_ids = sub_ids.join(',');
        }
        this.objects.hint_box.html('<li>No results.</li>');
        this.objects.email_tag_box.removeClass('error');
        if(data.share_level == "private"){
            this.config.selected_level = "private";
        }else if(data.share_level == "public"){
            this.config.selected_level = "public";
        }else if(data.share_level == "multiple"){
            this.config.selected_level = "multiple";
        }else{
            this.config.selected_level = "protected";
        }
        this.render();
        this.self.modal();

        if(this.config.selected_level == "protected"){
            this.disableFields();
            var user_ids = String(this.data.item.share_level).replace(/[\[\]]/g,"");
            $.ajax({
                url: site.base_url+'users/info',
                method: 'get',
                data: 'ids='+user_ids,
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                    this.render();
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.data.allowed_users = response.data;
                    }
                    else {
                        toastr["error"](response.message);
                    }
                    this.enableFields();
                    this.render();
                }
            });
        }
    },
    save: function(e) {
        e.preventDefault();
        var errors = 0;
        var visibility = this.self.find('[name="visibility"]:checked').val();
        var user_ids = [];
        var data = {
            id: this.data.item.id,
            share_level: visibility,
            pvt_share_id: "",
            user_ids: ""
        };
        if(this.data.item.level) data.level = this.data.item.level;
        if(this.data.item.sub_ids) data.sub_ids = this.data.item.sub_ids;
        if(!visibility) {
            toastr['error']('No visibility option selected.','Error');
            errors++;
        }else if(visibility == "protected"){
            this.objects.email_tag_box.find('input[name="id[]"]').each(function(){
                user_ids.push(this.value);
            });
            if(user_ids.length == 0){
                this.objects.email_tag_box.addClass('error');
                errors++;
            }else{
                this.objects.email_tag_box.removeClass('error');
                data.user_ids = user_ids.join(',');
            }
        } else if(visibility == "private"){
            if(this.objects.special_link_toggle.is(':checked')){
                data.pvt_share_id = this.data.item.pvt_share_id;
            }
        }

        if(errors == 0){
            this.disableFields();
            $.ajax({
                url: site.base_url+this.data.type+'/share',
                method: 'POST',
                data: data,
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                    this.enableFields();
                },
                success: function(response) {
                    this.self.modal('hide');
                    if(response.status == "ok") {
                        toastr["success"](response.message,"Updated");
                        if(this.data.type == "photos" || this.data.type == "videos"){
                            admin_app.library.getData();
                        }
                        if(this.data.type == "categories"){
                            admin_app.category.getData();
                        }
                    } else {
                        toastr["error"](response.message);
                    }
                    this.enableFields();
                }
            });
        }
    },
    setValue: function(e){
        this.config.selected_level = e.target.value;
        this.render.call(this);
    },
    liveSearch: function(e){
        if(e.keyCode != 13 && e.keyCode != 38 && e.keyCode != 40) {
            if(this.search_clock) clearTimeout(this.search_clock);
            this.search_clock = setTimeout(this.getHint.bind(this),600);
        }else if(e.keyCode == 38){ /* Up */
            if(this.data.search_hints.length > 0){
                var active = this.objects.hint_box.children('.item.active');
                if(active.length > 0){
                    var current = active.prev('.item');
                    if(current.length > 0){
                        this.objects.hint_box.children('.item.active').removeClass('active');
                        current.addClass('active');
                    }
                }else{
                    var current = this.objects.hint_box.children('.item').get(0);
                    $(current).addClass('active');
                }
            }
        }else if(e.keyCode == 40){ /* Down */
            if(this.data.search_hints.length > 0){
                var active = this.objects.hint_box.children('.item.active');
                if(active.length > 0){
                    var current = active.next('.item');
                    if(current.length > 0){
                        this.objects.hint_box.children('.item.active').removeClass('active');
                        current.addClass('active');
                    }
                }else{
                    var current = this.objects.hint_box.children('.item').get(0);
                    $(current).addClass('active');
                }
            }
        }else{  /* Enter */
            var active = this.objects.hint_box.children('.item.active');
            if(active.length > 0){
                var data = active.data('info');
                this.addEmail.call(this,data);
                e.target.blur();
            }
            return false;
        }
    },
    hintHover: function(e){
        this.objects.hint_box.children('li').removeClass('active');
        $(e.target).addClass('active');
    },
    getHint: function(e){
        $.ajax({
            url: site.base_url+'search/email',
            method: 'get',
            data: "kw="+this.objects.search_box.val(),
            context: this,
            error: function(jqXHR,textStatus,errorThrown){
                toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                this.renderHint.call(this);
            },
            success: function(response) {
                if(response.status == "ok") {
                    this.data.search_hints = response.data.items;
                }
                else {
                    toastr["error"](response.message);
                }
                this.renderHint.call(this);
            }
        });
    },
    renderHint: function(){
        var list = '';
        if(this.data.search_hints.length > 0){
            var hints = this.data.search_hints;
            for(var i=0;i<hints.length;i++){
                list += '<li class="item" data-info=\''+JSON.stringify(hints[i])+'\'>'+hints[i].email+'</li>';
            }
        }else{
            list = '<li>No results.</li>';
        }
        this.objects.hint_box.html(list);
    },
    addEmail: function(data){
        var error = 0;
        if((typeof data.id) === "undefined"){
            var data = $(this.objects.hint_box.find('li.active')).data('info');
        };
        if(this.objects.email_tag_box.find('input[value="'+data.id+'"]').length > 0) error++;
        var tag = 
            '<li>'+
                '<input type="hidden" name="id[]" value="'+data.id+'">'+
                '<span class="name">'+data.email+'</span>'+
                '<span class="del">×</span>'+
            '</li>';
        if(error == 0){
            $(tag).appendTo(this.objects.email_tag_box);
        }else{
            toastr['info']("Email already exist.","Duplicate");
        }
    },
    removeEmail: function(e){
        $(e.target).parents('li').remove();
    },
    focusSpecialLinkBox: function(e){
        if(e.target.disabled === false) $(e.target).select();
    },
    toggleSpecialLink: function(e){
        var checkbox = e.target;     
        if(checkbox.checked) {
            if((this.data.item.id.split(',')).length > 1){
                toastr['error']('Special link cannot be assigned to multiple items. Use category instead.','Error');
                this.objects.special_link_box.val('').prop('disabled',true);
                checkbox.checked = false;
            }else if(this.data.item.pvt_share_id && this.data.item.pvt_share_id.length === 32){
                var link = site.base_url+this.data.type+'/share/get/'+this.data.item.pvt_share_id;
                this.objects.special_link_box.prop('disabled',false).val(link);
            }else{
                this.disableFields();
                $.ajax({
                    url: site.base_url+'account/md5',
                    method: 'get',
                    context: this,
                    error: function(jqXHR,textStatus,errorThrown){
                        toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                        this.enableFields();
                    },
                    success: function(response) {
                        if(response.status == "ok") {
                            this.data.item.pvt_share_id = response.data;
                            var link = site.base_url+this.data.type+'/share/get/'+response.data;
                            this.objects.special_link_box.prop('disabled',false).val(link);
                        }
                        else {
                            toastr["error"](response.message);
                            this.data.item.share_id = "";
                            this.objects.special_link_box.val('').prop('disabled',true);
                            checkbox.checked = false;
                        }
                        this.enableFields();
                    }
                });
            }
        } else {
            this.objects.special_link_box.val('').prop('disabled',true);
        }
    },
    disableFields: function() {
        this.self.find('input').prop('disabled',true);
        this.self.find('button').prop('disabled',true);
    },
    enableFields: function() {
        this.self.find('input').prop('disabled',false);
        this.self.find('button').prop('disabled',false);
    }
};
