admin_app.visibility_editor =
{
    self: '#modal_visibility_editor',
    config: {
        
    },
    objects: {
        'form': null,
        'main_radios': null,
        'protected_block': null,
        'search_box': null,
        'hint_box': null,
        'email_tag_box': null
    },
    search_clock: null,
    data: {
        visibility: "public",
        user_ids: [],
        search_hints: []
    },
    init: function() {
        this.self = $(this.self);
        this.objects.search_box = this.self.find('[role="search-box"]');
        this.objects.hint_box = this.self.find('[role="hint-box"]');
        this.objects.main_radios = this.self.find('[name="visibility"]');
        this.objects.protected_block = this.self.find('div.protected-content');
        this.objects.email_tag_box = this.self.find('ul.input-tags');

        // Modal setup.
        this.self.modal({backdrop: 'static'}).modal('hide');

        // Event bindings.
        this.objects.search_box.unbind('keydown').on('keydown',this.liveSearch.bind(this));
        this.self.find('[data-id="save_btn"]').unbind('click').on('click',this.save.bind(this));
        this.objects.main_radios.unbind('click').on('click',this.setValue.bind(this));
        this.objects.hint_box.delegate("li",{
            mouseover: this.hintHover.bind(this),
            mousedown: this.addEmail.bind(this)
        });
        this.objects.email_tag_box.delegate('.del').on('click',this.removeEmail);

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
        alert('No function assigned.');
    },
    setValue: function(e){
        this.data.visibility = e.target.value;
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
        console.log(data.id);
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
    disableFields: function() {
        
    },
    enableFields: function() {
        
    }
};
