admin_app.library =
{
    self: $('div.content-wrapper'),
    objects: {
        media_type_dropdown_box: null,
        category_dropdown_box: null,
        bulk_operation_dropdown_box: null,
        search_form: null,
        search_box: null,
        display_quick_buttons: null,
        select_quick_buttons: null,
        content_box: null,
        pagination_box: null,
        pagination_index: null,
        pagination_total: null,
        pagination_prev: null,
        pagination_next: null,
        thumbs_box: null,
        active_search: null,
        search_clock: null,
    },
    data: {
        keyword: null,
        page: {current: 1,total: 0,limit: 24},
        items: {entries: [],total: 0},
        type: 'photos',
        category_id: "",
        category_list: [],
        display: 'thumb',
        selected: []
    },
    init: function() {
        // Assign objects.
        this.objects.media_type_dropdown_box = this.self.find('[data-id="media_type"]');
        this.objects.category_dropdown_box = this.self.find('[data-id="category"]');
        this.objects.visibility_dropdown_box = this.self.find('[data-id="visibility"]');
        this.objects.bulk_operation_dropdown_box = this.self.find('[data-id="bulk_operation"]');
        this.objects.search_form = this.self.find('[data-id="search_form"]');
        this.objects.search_box = this.self.find('[data-id="search_box"]');
        this.objects.display_quick_buttons = this.self.find('[data-id="quick_buttons"] button.display');
        this.objects.select_quick_buttons = this.self.find('[data-id="quick_buttons"] button.toggle-select');
        this.objects.pagination_prev = this.self.find('.m-pagination .prev');
        this.objects.pagination_index = this.self.find('.m-pagination .index');
        this.objects.pagination_next = this.self.find('.m-pagination .next');
        this.objects.pagination_total = this.self.find('.m-pagination .total');
        this.objects.content_box = this.self.find('[data-id="content"]');
        // Bind events.
        this.objects.media_type_dropdown_box.unbind().on('change',this.setMediaType.bind(this));
        this.objects.category_dropdown_box.unbind().on('change',this.setMediaCategory.bind(this));
        this.objects.visibility_dropdown_box.unbind().on('change',this.setVisibility.bind(this));
        this.objects.bulk_operation_dropdown_box.unbind().on('change',this.runBulkAction.bind(this));
        this.objects.search_form.unbind().on('submit',this.doSearch.bind(this));
        this.objects.search_box.unbind().on('keydown',this.liveSearch.bind(this));
        this.objects.display_quick_buttons.unbind().on('click',this.switchDisplay.bind(this));
        this.objects.select_quick_buttons.unbind().on('click',this.selectAllMedia.bind(this));
        this.objects.pagination_prev.unbind().on('click',this.prevPage.bind(this));
        this.objects.pagination_index.unbind().on('keypress',this.goPage.bind(this));
        this.objects.pagination_next.unbind().on('click',this.nextPage.bind(this));
        // Trigger initial contents.
        if($.inArray('all',site.permissions) !== -1 || $.inArray('media_view',site.permissions) !== -1){
            this.setMediaType();
        }else{
            this.render();
        }
    },
    render: function(){
        // Build result entries.
        var entries = this.data.items.entries;
        var html = "";
        if($.inArray('all',site.permissions) !== -1 || $.inArray('media_view',site.permissions) !== -1){
            // Thumb & list display.
            if(entries.length > 0){
                if(this.data.type == "photos") {
                    if(this.data.display == 'thumb') {
                        for(var n=0; n < entries.length; n++) {
                            var photo = entries[n];
                            var data  = JSON.stringify(photo);
                            var thumb = site.base_url+'media/photos/public/256/'+photo.uid+'.jpg';
                            var selected = $.inArray(photo.id,this.data.selected);
                            var item_class = (selected > -1)? "active" : "";
                            var checked = (selected > -1)? "checked" : "";
                            var controls = "";

                            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_change_category',site.permissions) !== -1){
                                controls += '<li title="Move to category" data-id="move_category">Move</b>';
                            }
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_share',site.permissions) !== -1){
                                controls += '<li title="Sharing" data-id="sharing">Sharing</li>';
                            }
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_edit',site.permissions) !== -1){
                                controls += '<li title="Edit" data-id="edit">Edit</li>';
                            }
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_delete',site.permissions) !== -1){
                                controls += '<li title="Delete" data-id="delete">Delete</li>';
                            }
                            var bulk_select = "";
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_delete',site.permissions) !== -1 || $.inArray('photo_change_category',site.permissions) !== -1){
                                bulk_select += '<label class="checkbox-ui" title="Bulk select"><input type="checkbox" '+checked+' value="'+photo.id+'"><i class="glyphicon glyphicon-ok"></i></label>';
                            }
                            
                            html +=
                            '<div class="media-entry thumb-box col-lg-2 '+item_class+' col-md-3 col-sm-4 col-xs-6" data-id="'+photo.id+'" data-category_id="'+photo.category_id+'" data-title="'+photo.title+'" data-data=\''+data+'\'>'+
                                '<div class="thumb" >'+
                                    '<a href="'+site.base_url+'photos/preview/lg/'+photo.uid+'" title="'+photo.title+'" class="image-link media-item photo-preview" style="background-image:url(\''+thumb+'\')">'+
                                    '</a>'+
                                    '<div class="controls"><a class="more" tabindex="2"><i class="fa fa-ellipsis-v"></i><ul class="options">'+controls+'</ul></a></div>'+
                                '</div>'+
                                '<div class="no-interact"></div>'+bulk_select+
                            '</div>';
                        }
                    }
                    else if(this.data.display == 'list') {
                        html += '<ul class="list-box">';
                        for(var i=0; i < entries.length; i++) {
                            var photo = entries[i];
                            var data  = JSON.stringify(photo);
                            var thumb = site.base_url+'media/photos/public/128/'+photo.uid+'.jpg';
                            var selected = $.inArray(photo.id,this.data.selected);
                            var item_class = (selected > -1)? "active" : "";
                            var checked = (selected > -1)? "checked" : "";
                            var controls = "";

                            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_change_category',site.permissions) !== -1){
                                controls += '<button title="Move to category" class="btn btn-primary btn-xs mini" data-id="move_category"><i class="fa fa-exchange"></i></button>';
                            }
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_share',site.permissions) !== -1){
                                controls += '<button title="Sharing" class="btn btn-primary btn-xs mini" data-id="sharing"><i class="fa fa-share-alt"></i></button>';
                            }
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_edit',site.permissions) !== -1){
                                controls += '<button title="Edit" class="btn btn-primary btn-xs mini" data-id="edit"><i class="fa fa-pencil"></i></button>';
                            }
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_delete',site.permissions) !== -1){
                                controls += '<button title="Delete" class="btn btn-danger btn-xs mini" data-id="delete"><i class="fa fa-trash"></i></button>';
                            }
                            
                            html +=
                                '<li class="media-entry list clearfix '+item_class+'" data-id="'+photo.id+'" data-category_id="'+photo.category_id+'" data-title="'+photo.title+'" data-data=\''+data+'\'>'+
                                    '<div class="check-box">'+
                                        '<label class="checkbox-ui" title="Bulk select"><input type="checkbox" '+checked+' value="'+photo.id+'"><i class="glyphicon glyphicon-ok"></i></label>'+
                                    '</div>'+
                                    '<div class="photo" style="background-image:url('+thumb+')">'+
                                        '<a href="'+site.base_url+'photos/preview/lg/'+photo.uid+'" class="photo-preview">&nbsp;</a>'+
                                    '</div>'+
                                    '<div class="detail">'+
                                        '<div class="title"><a href="'+site.base_url+'photos/preview/lg/'+photo.uid+'" class="photo-preview">'+photo.title+'</a></div>'+
                                        '<div class="description">'+photo.description+'</div>'+
                                    '</div>'+
                                    '<div class="controls float-right">'+controls+'</div>'+
                                    '<div class="no-interact"></div>'+
                                '</li>';
                        }
                        html += '</ul>';
                    }
                }
                else if(this.data.type == "videos") {
                    if(this.data.display == 'thumb') {
                        for(var n=0;n<entries.length;n++) {
                            var video = entries[n];
                            var data  = JSON.stringify(video);
                            var thumb = site.base_url+'media/videos/public/256/'+video.uid+'.jpg';
                            var vlink = site.base_url+'videos/preview/'+video.uid;
                            var selected = $.inArray(video.id,this.data.selected);
                            var item_class = (selected > -1)? "active" : "";
                            var checked = (selected > -1)? "checked" : "";
                            var controls = "";

                            if($.inArray('all',site.permissions) !== -1 || $.inArray('video_change_category',site.permissions) !== -1){
                                controls += '<li title="Move to category" data-id="move_category">Move</li>';
                            }
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('video_share',site.permissions) !== -1){
                                controls += '<li title="Sharing" data-id="sharing">Sharing</li>';
                            }
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('video_edit',site.permissions) !== -1){
                                controls += '<li title="Edit" data-id="edit">Edit</li>';
                            }
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('video_delete',site.permissions) !== -1){
                                controls += '<li title="Delete" data-id="delete">Delete</li>';
                            }
                            var bulk_select = "";
                            if($.inArray('all',site.permissions) !== -1 || $.inArray('video_delete',site.permissions) !== -1 || $.inArray('video_change_category',site.permissions) !== -1){
                                bulk_select += '<label class="checkbox-ui" title="Bulk select"><input type="checkbox" '+checked+' value="'+video.id+'"><i class="glyphicon glyphicon-ok"></i></label>';
                            }
                            
                            html +=
                            '<div class="media-entry thumb-box col-lg-2 col-md-3 col-sm-4 col-xs-6 '+item_class+'" data-id="'+video.id+'" data-category_id="'+video.category_id+'" data-title="'+video.title+'" data-data=\''+data+'\'>'+
                                '<div class="thumb" >'+
                                    '<a href="'+vlink+'" title="'+video.title+'" class="image-link media-item video-preview" style="background-image:url(\''+thumb+'\')">'+
                                    '</a>'+
                                    '<div class="controls"><a class="more" tabindex="1"><i class="fa fa-ellipsis-v"></i><ul class="options">'+controls+'</ul></a></div>'+
                                '</div>'+
                                '<div class="no-interact"></div>'+bulk_select+
                            '</div>';
                        }
                    }
                    else if(this.data.display == 'list') {
                        html += '<ul class="list-box">';
                            for(var i=0;i<entries.length;i++) {
                                var video = entries[i];
                                var data  = JSON.stringify(video);
                                var thumb = site.base_url+'media/videos/public/128/'+video.uid+'.jpg';
                                var vlink = site.base_url+'videos/preview/'+video.uid;
                                var selected = $.inArray(video.id,this.data.selected);
                                var item_class = (selected > -1)? "active" : "";
                                var checked = (selected > -1)? "checked" : "";
                                var controls = "";
                                
                                if($.inArray('all',site.permissions) !== -1 || $.inArray('video_change_category',site.permissions) !== -1){
                                    controls += '<button title="Move to category" class="btn btn-primary btn-xs" data-id="move_category"><i class="fa fa-exchange"></i></button>';
                                }
                                if($.inArray('all',site.permissions) !== -1 || $.inArray('video_share',site.permissions) !== -1){
                                    controls += '<button title="Sharing" class="btn btn-primary btn-xs" data-id="sharing"><i class="fa fa-share-alt"></i></button>';
                                }
                                if($.inArray('all',site.permissions) !== -1 || $.inArray('video_edit',site.permissions) !== -1){
                                    controls += '<button title="Edit" class="btn btn-primary btn-xs" data-id="edit"><i class="fa fa-pencil"></i></button>';
                                }
                                if($.inArray('all',site.permissions) !== -1 || $.inArray('video_delete',site.permissions) !== -1){
                                    controls += '<button title="Delete" class="btn btn-danger btn-xs" data-id="delete"><i class="fa fa-trash"></i></button>';
                                }
                                
                                html +=
                                    '<li class="media-entry list clearfix '+item_class+'" data-id="'+video.id+'" data-category_id="'+video.category_id+'" data-title="'+video.title+'" data-data=\''+data+'\'>'+
                                        '<div class="check-box">'+
                                            '<label class="checkbox-ui" title="Bulk select"><input type="checkbox" '+checked+' value="'+video.id+'"><i class="glyphicon glyphicon-ok"></i></label>'+
                                        '</div>'+
                                        '<div class="photo" style="background-image:url('+thumb+')">'+
                                            '<a href="'+vlink+'" title="'+video.title+'" class="video-preview">&nbsp;</a>'+
                                        '</div>'+
                                        '<div class="detail">'+
                                            '<div class="title"><a href="'+site.base_url+'videos/preview/'+video.uid+'" title="'+video.title+'" class="video-preview">'+video.title+'</a></div>'+
                                            '<div class="description">'+video.description+'</div>'+
                                        '</div>'+
                                        '<div class="controls float-right">'+controls+'</div>'+
                                        '<div class="no-interact"></div>'+
                                    '</li>';
                            }
                        html += '</ul>';
                    }
                }
            }
            else {
                html = '<div class="col-xs-12"><div class="alert alert-warning">No results found.</div></div>';
            }
            // Display results.
            this.objects.content_box.html(html);
            // Update pagination.
            this.objects.pagination_total.text(this.data.page.total);
            if(this.data.page.current < this.data.page.total) {
                this.objects.pagination_next.prop('disabled', false);
            }
            else {
                this.objects.pagination_next.prop('disabled', true);
            }
            if(this.data.page.current > 1) {
                this.objects.pagination_prev.prop('disabled', false);
            }
            else {
                this.objects.pagination_prev.prop('disabled', true);
            }
            if(this.data.page.total == 0) {
                this.objects.pagination_index.prop('disabled', true);
            }
            else {
                this.objects.pagination_index.prop('disabled', false);
            }
            this.objects.pagination_index.val(this.data.page.current);
            // Update display buttons.
            if($.inArray('all',site.permissions) !== -1 || $.inArray('photo_edit',site.permissions) !== -1 || $.inArray('photo_delete',site.permissions) !== -1 || $.inArray('video_edit',site.permissions) !== -1 || $.inArray('video_delete',site.permissions) !== -1){
                this.objects.select_quick_buttons.prop('disabled', false);
            }else{
                this.objects.select_quick_buttons.prop('disabled', true);
            }
            this.objects.display_quick_buttons.removeClass('active');
            this.self.find('button[data-display="'+this.data.display+'"]').addClass('active');
            // Update bulk operation displays (toolbar item, quick buttons & display items).
            if(this.data.selected.length > 0){
                if(!this.objects.bulk_operation_dropdown_box.hasClass('active')) this.objects.bulk_operation_dropdown_box.val('default');
                this.objects.bulk_operation_dropdown_box.addClass('active');
                this.objects.content_box.addClass('bulk_select_mode');
            }
            else{
                this.objects.select_quick_buttons.removeClass('active');
                this.objects.bulk_operation_dropdown_box.removeClass('active');
                this.objects.content_box.removeClass('bulk_select_mode');
            }
            // Bind functions.
            this.objects.content_box.find('.controls [data-id="move_category"]').unbind().on('mousedown',this.moveToCategory.bind(this));
            this.objects.content_box.find('.controls [data-id="sharing"]').unbind().on('mousedown',this.shareMedia.bind(this));
            this.objects.content_box.find('.checkbox-ui [type="checkbox"]').unbind().on('click',this.selectMedia.bind(this));
            this.objects.content_box.find('.controls [data-id="edit"]').unbind().on('mousedown',this.editMedia.bind(this));
            this.objects.content_box.find('.controls [data-id="delete"]').unbind().on('mousedown',this.deleteMedia.bind(this));
            // this.objects.content_box.find('a.photo-preview').fullsizable({detach_id: 'main_header',clickBehaviour: 'next'});
            // this.objects.content_box.find('a.video-preview').unbind('click').click(videomodal.open);
            this.objects.content_box.find('a.media-item').unbind('click').click(modal_media.open.bind(modal_media));
        }else{
            this.objects.media_type_dropdown_box.prop('disabled',true);
            this.objects.category_dropdown_box.prop('disabled',true);
            this.objects.bulk_operation_dropdown_box.prop('disabled',true);
            this.objects.search_box.prop('disabled',true);
            $('.quickbar').css('display','none');
            $('.table_block').css('display','none');
            $('#message_block').css('display','block').find('.alert').text("You don't have enough permission to view this content.");
            list_rows = 
                '<tr>'+
                    '<td colspan="4">You don\'t have enough permission to view this content.</td>'+
                '</tr>';
        }
    },
    runBulkAction: function(e){
        var $this = this;
        var action = e.target.value;
        if(action == "change_category"){
            var endpoint = site.base_url+this.data.type+'/move';
            var data = {
                id: this.data.selected,
                category_id: null
            };
            admin_app.category_selector.open(endpoint, data);
        }
        if(action == "change_visibility"){
            var data = { id: this.data.selected.join(','), share_level: 'multiple' };
            admin_app.visibility_editor.open.call(admin_app.visibility_editor,this.data.type,data);
            $(e.target).val('default');
        }
        else if(action == "delete"){
            var item_count = this.data.selected.length;
            var deleteItems = function(){
                $.ajax({
                    "method": "post",
                    "context": $this,
                    "url": site.base_url+$this.data.type+'/delete',
                    "data": {id: $this.data.selected},
                    "error" : function(jqXHR,textStatus,errorThrown){
                        toastr["error"]("Failed to delete \""+item_count+"\" items(s).", "Error "+jqXHR.status);
                    },
                    "success": function(response){
                        if(response.status == "ok"){
                            toastr["success"]("Deleted \""+item_count+"\" item(s).");
                            this.getData();
                        }
                        else{
                            toastr["error"]("Failed to delete "+item_count+" "+this.data.type+".", "Error 500");
                        }
                    }
                });
            };
            var cancelDelete = function(){this.objects.bulk_operation_dropdown_box.val('default')};
            modal.confirm("Do you want to delete "+item_count+" "+this.data.type+"?",deleteItems,cancelDelete.bind(this));
        }
    },
    doSearch: function(e){
        if($.inArray('all',site.permissions) !== -1 || $.inArray('media_view',site.permissions) !== -1){
            e.preventDefault();
            this.data.page.current = 1;
            this.getData();
        }
    },
    liveSearch: function(e){
        if(e.keyCode != 13) {
            this.data.page.current = 1;
            if(this.search_clock) clearTimeout(this.search_clock);
            this.search_clock = setTimeout(this.getData.bind(this),600);
        }
    },
    getData: function(e){
        if(e) e.preventDefault();
        var data = {
            kw: this.objects.search_box.val(),
            c: this.objects.category_dropdown_box.val(),
            v: this.objects.visibility_dropdown_box.val(),
            p: this.data.page.current,
            l: this.data.page.limit,
            m: 'json',
            edit: 1
        };
        //if(data.kw != this.data.keyword) {
            if(this.objects.active_search) this.objects.active_search.abort();
            this.objects.active_search = $.ajax({
                type: "get",
                url: site.base_url+"search/"+this.data.type,
                context: this,
                data: data,
                dataType: "json",
                success: function(response) {
                    this.data.keyword = data.kw;
                    this.data.page = response.page;
                    this.data.items = response.items;
                    this.data.selected = [];
                    this.render();
                }
            });
        //}
    },
    renderCategoryDropdown: function() {
        var category_structure = {};
        var category_list = this.data.category_list;
        var category_html = '<option value="" selected>Any category</option>';
        // Build category structure.
        for(var i=0;i<category_list.length;i++) {
            if(category_list[i].level == 1){
                category_structure['parent_'+category_list[i].id] = {self:category_list[i],children:[]};
            };
            if(category_list[i].level == 2){
                var my_parent_id = 'parent_'+category_list[i].parent_id;
                if(typeof category_structure[my_parent_id] !== 'undefined'){
                    category_structure[my_parent_id]['children'].push(category_list[i]);
                }
                else{
                    category_structure[my_parent_id] = {self:null,children:[category_list[i]]};
                }
            }
        };
        // Create HTML output.
        for(item in category_structure){
            var main_cat = category_structure[item];
            if(main_cat['self'].id == 1 && main_cat['self'].core == "yes"){
                category_html += '<option value="'+main_cat['self'].id+'">'+main_cat['self'].title+'</option>';
            }
            else{
                category_html += '<optgroup label="'+main_cat['self'].title+'">';
                if(main_cat['children'].length > 0){
                    for(var x=0;x<main_cat['children'].length;x++){
                        category_html += '<option value="'+main_cat['children'][x].id+'">'+main_cat['children'][x].title+'</option>';
                    }
                };
                category_html += '</optgroup>';
            }
        };
        this.objects.category_dropdown_box.html(category_html);
    },
    setMediaType: function(e) {
        this.objects.search_box.val("");
        this.objects.pagination_index.val(1);
        this.data.type = this.objects.media_type_dropdown_box.val();
        this.data.page.current = 1;
        // Fetch category list for selected media type
        $.ajax({
            "method": "get",
            "context": this,
            "url": site.base_url+'admin/media/categories/json',
            "data": "list="+this.data.type,
            "error" : function(jqXHR,textStatus,errorThrown){
                toastr["error"]("Failed to load category list.", "Error "+jqXHR.status);
            },
            "success": function(response){
                this.data.category_list = response;
                this.renderCategoryDropdown();
                this.getData();
            }
        });
    },
    setMediaCategory: function(e){
        this.objects.search_box.val("");
        this.objects.pagination_index.val(1);
        this.data.page.current = 1;
        this.getData();
    },
    setVisibility: function(e){
        this.objects.search_box.val("");
        this.objects.pagination_index.val(1);
        this.getData();
    },
    switchDisplay: function(e){
        var display_type = (e.target.nodeName == 'BUTTON')? $(e.target).attr('data-display') : $(e.target).parent('button').attr('data-display');
        if(this.data.display != display_type) {
            this.data.display = display_type;
            this.render();
        }
    },
    prevPage: function(){
        if(this.data.page.current > 1) {
            this.data.page.current -= 1;
            this.getData();
        }
    },
    goPage: function(e){
        if(e.type == 'keypress')
        {
            if(e.keyCode < 48 || e.keyCode > 57){
                if(e.keyCode == 13){
                    var to_page = $(e.target).val();
                    var input_keyword = this.objects.search_box.val();
                    if(input_keyword == this.data.keyword){
                        if(to_page > this.data.page.total){
                            this.data.page.current = this.data.page.total;
                        }
                        else{
                            this.data.page.current = to_page;
                        };
                        this.getData();
                    }
                    else {
                        this.data.page.current = to_page;
                        this.getData();
                    }
                }
                else{
                    return false;
                }
            }
        }
    },
    nextPage: function(){
        if(this.data.page.current < this.data.page.total) {
            this.data.page.current += 1;
            this.getData();
        }
    },
    moveToCategory: function(e){
        var parent = $(e.target).parents(".media-entry");
        var endpoint = site.base_url+this.objects.media_type_dropdown_box.val()+'/move';
        var data = {
            id: parent.attr('data-id'),
            category_id: parent.attr('data-category_id')
        };
        admin_app.category_selector.open(endpoint, data);
    },
    shareMedia: function(e){
        var item = $(e.target).parents('.media-entry');
        var data = item.data('data');
        console.log(data);
        admin_app.visibility_editor.open.call(admin_app.visibility_editor,this.data.type,data);
    },
    selectMedia: function(e){
        var item = $(e.target).parents(".media-entry");
        var checkbox = $(e.target);
        var id = checkbox.val();
        var index = $.inArray(id,this.data.selected);;
        if(checkbox.is(":checked")){
            item.addClass('active');
            if(index == -1){
                this.data.selected.push(id);
            }
        }
        else{
            item.removeClass('active');
            if(index > -1){
                this.data.selected.remove(id);
            }
        };
        if(this.data.selected.length > 0){
            if(!this.objects.bulk_operation_dropdown_box.hasClass('active')) this.objects.bulk_operation_dropdown_box.val('default');
            this.objects.bulk_operation_dropdown_box.addClass('active');
            this.objects.content_box.addClass('bulk_select_mode');
        }
        else{
            this.objects.bulk_operation_dropdown_box.removeClass('active');
            this.objects.content_box.removeClass('bulk_select_mode');
        }
    },
    selectAllMedia: function(e){
        var self = (e.target.nodeName == "I")? $(e.target).parent("button") : $(e.target);
        var current_items = this.data.items.entries;
        if(self.hasClass("active")){
            self.removeClass("active");
            this.data.selected = [];
            this.render();
        }
        else{
            self.addClass("active");
            this.data.selected = [];
            for(var i=0;i<current_items.length;i++ ){
                this.data.selected.push(current_items[i].id);
            };
            this.render();
        }
    },
    editMedia: function(e){
        var parent = $(e.target).parents(".media-entry");
        var id = parent.attr('data-id');
        if(this.data.type == "photos") {
            admin_app.photo_editor.open(id,this.getData.bind(this));
        }
        else if(this.data.type == "videos") {
            admin_app.video_editor.open(id,this.getData.bind(this));
        }
    },
    deleteMedia: function(e) {
        var parent = $(e.target).parents(".media-entry");
        var id = parent.attr('data-id');
        var item_name = parent.attr('data-title');
        var $this = this;
        var delete_item = function(){
            $.ajax({
                "method": "post",
                "context": $this,
                "url": site.base_url+$this.data.type+'/delete',
                "data": "id="+id,
                "error" : function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to delete \""+item_name+"\".", "Error "+jqXHR.status);
                },
                "success": function(response){
                    if(response.status == "ok"){
                        toastr["success"]("Deleted \""+item_name+"\".");
                        this.getData();
                    }
                    else{
                        toastr["error"](response.message);
                    }
                }
            });
        };
        modal.confirm("Do you want to delete a file?",delete_item);
    }
};
