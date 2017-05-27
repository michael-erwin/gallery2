admin_app.presentation_items_editor =
{
    self: '#modal_presentation_items_editor',
    config: {},
    objects: {
        'entry_title': null,
        'sort_box': null,
        'input_files': null,
        'upload_btn': null
    },
    data: {
        entry: null,
        items: [],
        files: [],
        files_processed: 0
    },
    init: function() {
        this.self = $(this.self);
        this.objects.entry_title = this.self.find('[role="entry-title"]');
        this.objects.sort_box = this.self.find('ul.sortable-items');
        this.objects.input_files = this.self.find('input[type="file"]');
        this.objects.upload_btn = this.self.find('li.add');

        // Initial values.
        this.data.items = [];
        this.data.files = [];

        // Bindings.
        this.objects.input_files.unbind('click').on('click',function(){this.value=null;});
        this.self.delegate('input[type="file"]','change',this.addFiles.bind(this));
        this.self.delegate('.remove','click',this.delete.bind(this));
        $(this.self).on('hidden.bs.modal',function(){admin_app.presentation.getData()});

        // Apply sortable elements.
        this.objects.sort_box.sortable({
            items: '.item',
            handle:'.image',
            placeholder:'sortable-item-placeholder',
            forcePlaceholderSize: true,
            update: this.sort.bind(this)
        });
    },
    render: function() {
        this.enableState();
        this.objects.entry_title.text(this.data.entry.title);
        
        // Clear previous html.
        this.self.find('li.item').remove();
        this.self.find('li.loading').remove();
        this.self.find('li.error').remove();

        // Build html.
        for(var i=0;i<this.data.items.length;i++) {
            var item = this.data.items[i];
            var item_tag = $(this.createItemTag('item',item));
            item_tag.find('input').on('blur',this.changeText.bind(this));
            this.self.find('li.active').last().after(item_tag);
        }

        // Apply sequence number.
        this.updateSequenceItem();

        // Show modal.
        $(this.self).modal({backdrop: 'static'});
    },
    open: function(data) {
        // Reset data.
        this.data = {
                entry: { id: data.id, title: data.title, description: data.description },
                items: [],
                files: [],
                files_processed: 0
            };
        this.getData();
    },
    getData: function(render=true) {
        if(render === true) this.disableState();
        var endpoint = 'presentations/get/'+this.data.entry.id+'/items';
        $.ajax({
            url: site.base_url+endpoint,
            method: "get",
            context: this,
            error: function(jqXHR,textStatus,errorThrown){
                toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                this.enableState();
            },
            success: function(response) {
                this.enableState();
                if(response.status == "ok") {
                    this.data.items = response.data.items;
                    if(render === true) this.render();
                } else {
                    toastr["error"](response.message, "Error");
                }
                this.enableState();
            }
        });
    },
    sort: function(event,ui) {
        this.disableState();
        var endpoint = 'presentations/update';
        var sequence = [];

        // Get new sequence.
        this.objects.sort_box.find('li.item').each(function(){
            var item_info = $(this).data('info');
            sequence.push(item_info.id);
        });

        // Update object.
        var new_data = {
            id: this.data.entry.id,
            items: sequence.join(',')
        };
        $.ajax({
            url: site.base_url+endpoint,
            method: "post",
            context: this,
            data: new_data,
            error: function(jqXHR,textStatus,errorThrown){
                toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                this.enableState();
            },
            success: function(response) {
                this.enableState();
                if(response.status == "ok") {
                    this.getData();
                } else {
                    toastr["error"](response.message, "Error");
                }
                this.enableState();
            }
        });
    },
    addFiles: function(e) {
        this.data.files = [];
        var files = e.target.files;
        for(var i=0;i<files.length;i++) {
            if(files[i].type != "image/jpeg") {
                toastr['error']('Not a jpeg file.',files[i].name);
            } else {
                var loading_tag = this.createItemTag('loading');
                var my_elem = $(loading_tag);
                var index_item = this.self.find('li.active');
                    index_item.last().after(my_elem);
                this.data.files.push({
                    processed: false,
                    file: files[i],
                    elem: my_elem
                });
            }
        }
        this.upload.call(this);
    },
    getTailFile: function() {
        var found = false;
        for(var i=0; i<this.data.files.length; i++) {
            if(!this.data.files[i].processed) {
                found = this.data.files[i];
                break;
            }
        }
        return found;
    },
    upload: function() {
        if(this.data.files_processed < this.data.files.length) {

            // Current file object.
            var file_item = this.getTailFile.call(this);

            if(file_item) {

                var xhr = new XMLHttpRequest();
                var tag = this.createItemTag('error');

                // Form data.
                var form_data = new FormData();
                form_data.append('file', file_item.file);
                form_data.append('id', this.data.entry.id);

                // Events.
                xhr.error = function(e) {
                    this.data.files_processed++;
                    file_item.processed = true;
                    toastr["warning"]("Failed to upload "+file_item.file.name+"?");
                    file_item.elem.replaceWith(tag);
                };
                xhr.onload = function(e) {
                    this.data.files_processed++;
                    try {
                        var response = JSON.parse(xhr.responseText);

                        if(response.status == "ok") {
                            this.data.items.push(response.data);
                            tag = this.createItemTag('item',response.data);
                            file_item.elem.replaceWith(tag);
                        }
                        else {
                            toastr["warning"](response.message,"Warning");
                            file_item.elem.replaceWith(tag);
                        }

                        this.updateSequenceItem();
                    }
                    catch(e) {
                        toastr["error"]("Failed to upload "+file_item.file.name+".",xhr.statusText);
                        file_item.elem.replaceWith(tag);
                    }
                    file_item.processed = true;
                    this.upload.call(this);
                }.bind(this);

                // Submit files.
                xhr.open('POST', site.base_url+'upload/presentation_item', true);
                xhr.send(form_data);
            }
        }
    },
    createItemTag: function(mode,data) {
        var mod = "item";
        var inf = {
            id: "", uid: "", title: "", caption: "", bg: ""
        };
        if(mode) {
            mod = mode;
            if(data) {
                if(data.id) inf.id = data.id;
                if(data.uid) inf.bg = "background-image:url('"+site.base_url+"media/presentation_items/128/"+data.uid+".jpg')";
                if(data.title) inf.title = data.title;
                if(data.caption) inf.caption = data.caption;
            }
        };
        var tag = 
            '<li class="'+mod+' active" data-info=\''+(JSON.stringify(inf)).replace(/[']/g,"&#39;")+'\'>'+
                '<div class="remove" title="Remove">×</div>'+
                '<div class="image" style="'+inf.bg+'">'+
                    '<div class="series"></div>'+
                '</div>'+
                '<div class="info">'+
                    '<div class="field">'+
                        '<input class="form-control" type="text" maxlength="16" name="title" placeholder="Title" value="'+inf.title+'">'+
                    '</div>'+
                    '<div class="field">'+
                        '<input class="form-control" type="text" maxlength="64" name="caption" placeholder="Caption" value="'+inf.caption+'">'+
                    '</div>'+
                '</div>'+
            '</li>';
        return tag;
    },
    delete: function(e) {
        var item = $(e.target).parents('li.item');
        var info = item.data('info');
        function delete_action() {
            this.disableState();
            var endpoint = 'presentations/delete/item';
            $.ajax({
                url: site.base_url+endpoint,
                method: "post",
                context: this,
                data: 'id='+info.id,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                    this.enableState();
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.getData();
                    } else {
                        toastr["error"](response.message, "Error");
                    }
                    this.enableState();
                }
            });
        }
        modal.confirm('Do you want to delete an item from '+this.data.entry.title+'?',delete_action.bind(this));
    },
    updateSequenceItem: function() {
        // Apply numeric sequence.
        this.self.find('.item').each(function(index) {
            $(this).data('index',index);
            $(this).find('.series').html(index+1);
        });
    },
    changeText: function(e) {
        var field = $(e.target);
        var field_name = field.attr('name');
        var index  = field.parents('li.item').data('index');
        var item_data = this.data.items[index];

        var old_val = item_data[field_name];
        var new_val = field.val();

        // Check if data changed before deciding to update.
        if(old_val != new_val) {
            var endpoint = 'presentations/update/item';

            // Update object.
            var new_data = 'id='+item_data.id+'&'+field_name+'='+new_val;
            $.ajax({
                url: site.base_url+endpoint,
                method: "post",
                context: this,
                data: new_data,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                    this.enableState();
                },
                success: function(response) {
                    if(response.status == "ok") {
                        this.getData(false);
                    } else {
                        toastr["error"](response.message, "Error");
                    }
                    this.enableState();
                }
            });
        }
    },
    disableState: function() {
        this.self.find('.modal-body').addClass('no-interact');
        this.self.find('input').prop('disabled',true);
        this.self.find('textarea').prop('disabled',true);
        this.self.find('button').prop('disabled',true);
    },
    enableState: function() {
        this.self.find('.modal-body').removeClass('no-interact');
        this.self.find('input').prop('disabled',false);
        this.self.find('textarea').prop('disabled',false);
        this.self.find('button').prop('disabled',false);
    }
};
