admin_app.user =
{
    self: $('div.content-wrapper'),
    config: {
        expanded_items: {}
    },
    objects: {
        'new_button': null,
        'search_box': null,
        'table_body': null,
        'page_prev': null,
        'page_next': null,
        'page_now': null,
        'page_total': null,
        'display_buttons': null,
        'search_clock': null
    },
    data: {
        items: [],
        page: {
            current: 1,
            limit: 15,
            total: null
        }
    },
    init: function() {
        this.objects.new_button = this.self.find('button[data-id="new_button"]');
        this.objects.search_box = this.self.find('input[data-id="search_box"]');
        this.objects.search_form = this.self.find('form[data-id="search_form"]');
        this.objects.table_body = this.self.find('tbody[data-id="list"]');
        this.objects.page_prev = this.self.find('button.prev');
        this.objects.page_next = this.self.find('button.next');
        this.objects.page_now = this.self.find('input.index');
        this.objects.page_total = this.self.find('span.of-pages .total');
        this.objects.display_buttons = this.self.find('[data-id="quick_buttons"] button.display');
        // Bind events.
        this.objects.new_button.on('click',this.new.bind(this));
        this.objects.search_form.submit(function(e){e.preventDefault();});
        this.objects.search_box.on('keydown',this.doLiveSearch.bind(this));
        this.objects.page_prev.on('click',this.prevPage.bind(this));
        this.objects.page_next.on('click',this.nextPage.bind(this));
        this.objects.page_now.on('keypress',this.goPage.bind(this));
        this.objects.display_buttons.on('click',this.displaySize.bind(this));
        if($.inArray('all',site.permissions) !== -1 || $.inArray('user_view',site.permissions) !== -1){
            this.getData.call(this);
        }else{
            this.render();
        }
    },
    render: function() {
        var empty_table_text = "No items found.";
        // Apply permissions.
        if($.inArray('all',site.permissions) !== -1 || $.inArray('user_view',site.permissions) !== -1){
            $('.quickbar').css('display','block');
            $('.table_block').css('display','block');
            $('#message_block').css('display','none');
            this.objects.search_box.prop('disabled',false);
            if($.inArray('all',site.permissions) !== -1 || $.inArray('user_add',site.permissions) !== -1){
                this.objects.new_button.prop('disabled',false);
            }else{
                this.objects.new_button.prop('disabled',true);
            }
        }else{
            $('.quickbar').css('display','none');
            $('.table_block').css('display','none');
            $('#message_block').css('display','block').find('.alert').text("You don't have enough permission to view this content.");
            this.objects.search_box.prop('disabled',true);
            this.objects.new_button.prop('disabled',true);
        }
        // Build table entries.
        var list_rows = "";
        if(this.data.items.length > 0) {
            for (var i = 0; i < this.data.items.length; i++) {
                var user = this.data.items[i];
                var status = (user.status == "active")? 'success':'default';
                var edit_btn = "";
                var delete_btn = "";
                if($.inArray('all',site.permissions) !== -1 || $.inArray('user_edit',site.permissions) !== -1){
                    edit_btn = '<button class="btn btn-primary btn-xs" data-id="edit_entry" title="Edit">'+
                                    '<i class="fa fa-pencil"></i>'+
                                '</button>&nbsp;';
                }
                if($.inArray('all',site.permissions) !== -1 || $.inArray('user_delete',site.permissions) !== -1){
                    delete_btn = '<button class="btn btn-danger btn-xs" data-id="delete_entry" title="Delete">'+
                                    '<i class="fa fa-trash"></i>'+
                                '</button>&nbsp;';
                }
                list_rows += 
                '<tr data-info=\''+JSON.stringify(user)+'\'>'+
                    '<td>'+user.email+'</td>'+
                    '<td>'+user.first_name+' '+user.last_name+'</td>'+
                    '<td>'+user.role_name+'</td>'+
                    '<td><span class="label label-'+status+'">'+user.status+'</span></td>'+
                    '<td>'+edit_btn+delete_btn+'</td>'+
                '</tr>';
            }
        }
        else {
            list_rows = 
            '<tr colspan="5">'+
                '<td>'+empty_table_text+'</td>'+
            '</tr>';
        };
        this.objects.table_body.html(list_rows);

        // Results display button logic.
        var limit = this.data.page.limit;
        this.objects.display_buttons.each(function(){
            if($(this).data('display') == limit) {
                $(this).addClass('active');
            }
            else {
                $(this).removeClass('active');
            }
        });

        // Pagination display logic.
        this.objects.page_now.val(this.data.page.current);
        this.objects.page_total.text(this.data.page.total);
        if(this.data.page.current == 1) {
            this.objects.page_prev.prop('disabled',true);
        } else { this.objects.page_prev.prop('disabled',false); }
        if(this.data.page.current < this.data.page.total) {
            this.objects.page_next.prop('disabled',false);
        } else { this.objects.page_next.prop('disabled',true); }
        if(this.data.page.total == 1) {
            this.objects.page_total.prop('disabled',true);
        } else { this.objects.page_total.prop('disabled',false); }

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
            var endpoint = site.base_url+'users/manage/get_page';
            var sdata = 'limit='+this.data.page.limit+'&page='+this.data.page.current;
            if($.trim(this.objects.search_box.val()) != "")
            {
                endpoint = site.base_url+'users/manage/search';
                sdata += '&kw='+this.objects.search_box.val();
            }
            $.ajax({
                url: endpoint,
                method: 'get',
                data: sdata,
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                    this.render();
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.data.items = response.data;
                        this.data.page = response.page
                    }
                    else {
                        toastr["error"](response.message);
                    }
                    this.render();
                }
            });
        }
    },
    displaySize: function(e) {
        var $this = $(e.target);
        this.data.page.current = 1;
        this.data.page.limit = Number($this.data('display'));
        this.getData();
    },
    doLiveSearch(e) {
        if(e.which === 13) return false;
        function senKeys() {
            $.ajax({
                url: site.base_url+'users/manage/search',
                method: "get",
                data: 'kw='+e.target.value+'&limit='+this.data.page.limit+'&page='+this.data.page.current,
                context: this,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to reach content.", "Error "+jqXHR.status);
                    this.render();
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.data.page = response.page;
                        this.getData(response.data);
                    }
                    else {
                        toastr["error"](response.message, "Error");
                        this.render();
                    }
                }
            });
        }
        clearTimeout(this.objects.search_clock);
        this.objects.search_clock = setTimeout(senKeys.bind(this),1000);
    },
    new: function(e) {
        admin_app.user_editor.new.call(admin_app.user_editor);
    },
    edit: function(e) {
        var row = $(e.target).parents("tr");
        var data = row.data('info');
        admin_app.user_editor.edit.call(admin_app.user_editor,data);
    },
    delete: function(e) {
        var row = $(e.target).parents("tr");
        var data = row.data('info');
        var id = data.id;
        var doDelete = function() {
            $.ajax({
                url: site.base_url+'users/manage/delete',
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
        modal.confirm('Do you want to delete a user?',doDelete.bind(this));
    },
    goPage: function(e) {
        if(e.which >= 48 && e.which <= 57) {
            return true;
        }
        else if(e.which == 13) {
            if(e.target.value > this.data.page.total) {
                this.objects.page_now.val(this.data.page.total);
            }
            else if(e.target.value == 0) {
                this.data.page.current = 1;
            }
            else if(e.target.value <= this.data.page.total) {
                this.data.page.current = e.target.value;
            }
            this.getData();
        }
        else { return false; }
    },
    prevPage: function() {
        if(this.data.page.current > 1) {
            this.data.page.current = Number(this.data.page.current) - 1;
            this.getData();
        }
    },
    nextPage: function() {
        if(this.data.page.current < this.data.page.total) {
            this.data.page.current = Number(this.data.page.current) + 1;
            this.getData();
        }
    }
};
