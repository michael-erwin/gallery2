admin_app.photo_editor =
{
    self: "#modal_photo_editor",
    objects: {
        img_box: null,
        meta_date_added: null,
        meta_date_modified: null,
        meta_file_size: null,
        meta_dimension: null,
        title_box: null,
        description_box: null,
        tag_box: null,
        tag_box_input: null,
        downloadable_form: null,
        downloadable_text: null,
        downloadable_file: null,
        downloadable_progress_box: null,
        downloadable_progress_bar: null,
        downloadable_btn: null,
        save_btn: null
    },
    caller: null,
    data: {
        id: null,
        uid: null,
        category_id: null,
        title: null,
        description: null,
        tags: null,
        height: null,
        width: null,
        file_size: null,
        has_zip: null,
        share_id: null,
        date_added: null,
        date_modified: null
    },
    open: function(id,caller) {
        this.self.modal('show');
        this.getData(id);
        if(caller){
            this.caller = caller;
        }
    },
    init: function() {
        // Initialize main app element.
        this.self = $(this.self);
        // Initialize photo box.
        this.objects.img_box = this.self.find('.modal-media-box');
        this.objects.img_box.find('img[data-id="photo"]').unbind().on('load',this.imgLoaded.bind(this));
        // Initialize info boxes.
        this.objects.meta_date_added = this.self.find('[data-id="date_added"]');
        this.objects.meta_date_modified = this.self.find('[data-id="date_modified"]');
        this.objects.meta_file_size = this.self.find('[data-id="file_size"]');
        this.objects.meta_dimension = this.self.find('[data-id="dimension"]');
        this.objects.title_box = this.self.find('[name="title"]');
        this.objects.description_box = this.self.find('[name="description"]');
        // Initialize tag box.
        this.objects.tag_box = this.self.find('ul.input-tags');
        this.objects.tag_box.html("");
        this.objects.tag_box_input = $('<li class="input-text" contenteditable="true"></li>').appendTo(this.objects.tag_box);
        this.objects.tag_box_input.unbind('keypress').on('keypress',this.keyinTag.bind(this));
        this.objects.tag_box_input.unbind('keydown').on('keydown',this.backspTag.bind(this));
        this.objects.tag_box_input.unbind('paste').on('paste',function(){return false});
        this.objects.tag_box.unbind('click').on('click',(function(){this.objects.tag_box_input.focus()}).bind(this));
        // Initialize downloadable box.
        this.objects.downloadable_form = this.self.find('.downloadable_box form');
        this.objects.downloadable_file = this.self.find('.downloadable_box [data-id="upload_source_file"]');
        this.objects.downloadable_text = this.self.find('.downloadable_box [data-id="downloadable_text"]');
        this.objects.downloadable_progress_box = this.self.find('.downloadable_box [data-id="progress_box"]');
        this.objects.downloadable_progress_bar = this.self.find('.downloadable_box [role="progressbar"]');
        this.objects.downloadable_btn = this.self.find('.downloadable_box [data-id="upload_btn"]');
        // Initialize buttons.
        this.objects.save_btn = this.self.find('[data-id="save_btn"]');
        this.objects.save_btn.unbind('click').on('click',this.save.bind(this));
        this.objects.downloadable_btn.unbind('click').on('click',this.uploadTrigger.bind(this));
        this.objects.downloadable_file.unbind().on('change',this.uploadFile.bind(this));
        // Bind events.
        this.self.find('.downloadable_box button.delete').unbind('click').on('click',this.deleteZip.bind(this));
        this.self.on('hidden.bs.modal', (function(){this.objects.img_box.addClass("loading")}).bind(this));
        // Modal behavior.
        this.self.modal({backdrop: 'static',keyboard: false}).modal('hide');
    },
    render: function() {
        this.uploadZipEnd();
        this.objects.img_box.addClass("loading");
        this.objects.img_box.find('img[data-id="photo"]').attr('src',site.base_url+'photos/preview/lg/'+this.data.uid);
        this.enableState();
        this.objects.meta_date_added.text(this.data.date_added);
        this.objects.meta_date_modified.text(this.data.date_modified);
        this.objects.meta_file_size.text(this.data.file_size);
        this.objects.meta_dimension.text(this.data.width+'x'+this.data.height);
        this.objects.title_box.val(this.data.title);
        this.objects.description_box.val(this.data.description);
        this.objects.tag_box.find('li.input-tag').remove();
        var tags = $.trim(this.data.tags);
        tags = (tags.length > 0)? tags.split(' ') : [];
        this.makeTags(tags);
    },
    save: function() {
        var title = this.objects.title_box;
        var description = this.objects.description_box;
        var tags_li = this.objects.tag_box.find('li.input-tag span.name');
        var tags = [];

        tags_li.each(function(){
            tags.push($(this).text());
        });

        if($.trim(title.val()) != "") {
            title.removeClass("error");
            this.data.title = $.trim(title.val());
            this.data.description = $.trim(description.val());
            if(tags.length > 0) {
                this.data.tags = tags.join(' ');
            }
            else {
                this.data.tags = "";
            }
            this.pushChange();
        }
        else {
            title.addClass("error");
        }
    },
    pushChange: function() {
        this.disableState();
        var data = {
            id: this.data.id,
            title: this.data.title,
            description: this.data.description,
            tags: this.data.tags
        };
        $.ajax({
            context: this,
            url: site.base_url+"photos/update",
            method: "post",
            data: data,
            error: function(jqXHR,textStatus,errorThrown){
                toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
            },
            success: function(response) {
                if(response.status == "ok") {
                    if(this.caller) {
                        if(typeof this.caller == 'function') {
                            this.caller();
                        }
                        else {
                            this.caller.setName(this.data.title);
                        }
                    };
                    toastr["success"](response.message);
                    this.enableState();
                    this.close();
                }
                else {
                    toastr["error"](response.message, "Error");
                    this.enableState();
                    this.close();
                }
            }
        });
    },
    close: function() {
        this.self.modal('hide');
    },
    disableState: function() {
        var input = this.objects.tag_box_input;
        this.self.find('input').prop('disabled', true);
        this.self.find('textarea').prop('disabled', true);
        this.objects.tag_box.addClass('disabled');
        this.objects.tag_box_input.prop('contenteditable',false);
        if(input.text().length == 0) input.html('&nbsp;');
        this.self.find('button').prop('disabled', true);
    },
    enableState: function() {
        this.self.find('input').prop('disabled', false);
        this.self.find('textarea').prop('disabled', false);
        this.objects.tag_box.removeClass('disabled');
        this.objects.tag_box_input.prop('contenteditable',true);
        this.objects.tag_box_input.text("");
        this.self.find('button').prop('disabled', false);
    },
    getData: function(id) {
        var $this = this;
        var setData = function(response) {
            if(response.status == "ok") {
                this.data = response.data;
                this.render();
            }
            else {
                toastr["error"](response.message, "Error ");
            }
        };
        $.ajax({
            context: $this,
            url: site.base_url+'photos/info/'+id,
            error: function(jqXHR,textStatus,errorThrown){
                toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
            },
            success: setData.bind(this)
        });
    },
    imgLoaded: function(e) {
        $(e.target).parent(".modal-media-box").removeClass("loading");
    },
    makeTags: function(list) {
        for(var i=0; i<list.length; i++) {
            $(this.formatTag(list[i])).insertBefore(this.objects.tag_box_input);
        };
        this.objects.tag_box.delegate("li > span.del","click",this.removeTag);
    },
    formatTag: function(name) {
        return '<li class="input-tag"><span class="name">'+name+'</span><span class="del" title="Remove">×</span></li>';
    },
    keyinTag: function(e) {
        var self = e.target;
        var input = e.which;

        if(input>=48 && input<=57) { //(0-9)
            return true;
        }
        else if(input>=65 && input<=90) { //A-Z)
            return true;
        }
        else if(input>=97 && input<=122) { // (a-z)
            return true;
        }
        else if(input==45) { // dash(-)
            return true;
        }
        else if(input == 13 || input == 32) { // enter|space
            e.preventDefault();
            this.addTag(event);
        }
        else{
            return false;
        }
    },
    backspTag: function(e) {
        this.objects.tag_box.find('li.error').removeClass('error');
        if(e.which == 8) {
            var input = $(e.target).text();
            if($.trim(input) == "") {
                var last_tag = $(this.objects.tag_box).find('li.input-tag').last();
                last_tag.remove();
            }
        }
    },
    addTag: function(e) {
        var input = $.trim($(e.target).text());
        var errors = 0;
        if(input.length == 0) {
            errors++;
        }
        else {
            this.objects.tag_box.find('li.input-tag span.name').each(function(){
                var self = $(this);
                if(self.text() == input) {
                    self.parent('li').addClass('error');
                    errors++;
                }
            });
        };
        if(errors == 0) {
            var tag = this.formatTag(input);
            $(tag).insertBefore(this.objects.tag_box_input);
            $(e.target).text("");
        };
        return false;
    },
    removeTag: function(e) {
        $(e.target).parent("li").remove();
    },
    uploadTrigger: function(e){
        this.objects.downloadable_form[0].reset();
        this.objects.downloadable_file.trigger('click');
    },
    uploadZipStart: function(){
        // Disable closing window.
        this.self.find('button').prop("disabled",true);
        // Hide upload controls and show progress bar.
        this.objects.downloadable_progress_bar.css('width','0%').text('0%');
        this.objects.downloadable_text.css('display','none');
        this.objects.downloadable_btn.css('display','none');
        this.objects.downloadable_progress_box.css('display','block');
    },
    uploadZipEnd: function(){
        // Determine text for name & button.
        var file_text = "None";
        var btn_text = "Upload";
        if(this.data.has_zip > 0){
            btn_text = "Change";
            file_text = (this.data.title.split(' ')).join('-').toLowerCase()+'-'+this.data.uid+'.zip';
            this.objects.downloadable_text.addClass('active');
        }else{
            this.objects.downloadable_text.removeClass('active');
        }
        // Enable closing window.
        this.self.find('button').prop("disabled",false);
        // Show upload controls and hide progress bar.
        this.objects.downloadable_text.css('display','block').children('span').text(file_text);
        this.objects.downloadable_btn.text(btn_text).css('display','block');
        this.objects.downloadable_progress_box.css('display','none');
    },
    uploadFile: function(e){
        this.uploadZipStart.call(this);
        
        // XHR Object Definition
        this.objects.xhr = new XMLHttpRequest();
        var xhr = this.objects.xhr;

        // Form data.
        var form_data = new FormData();
        form_data.append('id', this.data.id);
        form_data.append('uid', this.data.uid);
        form_data.append('zip_file', e.target.files[0]);

        // Events.
        xhr.error = function(e) {
            toastr["warning"]("Failed to upload "+item_name+"?");
            console.log("This xhr.error");
        }.bind(this);
        xhr.upload.onprogress = function(e) {
            if(e.lengthComputable) {
                var percent = Math.ceil((e.loaded/e.total) * 100) + '%';
                this.objects.downloadable_progress_bar.css('width',percent).text(percent);
            }
        }.bind(this);
        xhr.onload = function(e) {
            try {
                var response = JSON.parse(xhr.responseText);
                if(response.status == "ok"){
                    this.data.has_zip = 1;
                    try{admin_app.library.getData.call(admin_app.library);}catch(e){};
                    toastr["success"](response.message,"Success");
                }else{
                    toastr["error"](response.message,"Error");
                }
            }catch(e){
                toastr["warning"]("Unknown response.","Warning");
            }
            setTimeout(this.uploadZipEnd.bind(this),1000);
        }.bind(this);

        // Submit files.
        xhr.open('POST', site.base_url+'upload/photos/zip', true);
        xhr.send(form_data);
    },
    deleteZip: function(e){
        function deleteZipFile(){
            this.disableState();
            this.data.has_zip = 0;
            $.ajax({
                context: this,
                method: 'POST',
                url: site.base_url+'upload/photos/clear',
                data: {id:this.data.id,uid:this.data.uid},
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Request failed.", "Error "+jqXHR.status);
                    this.enableState();
                },
                success: function(response){
                    try { admin_app.library.getData.call(admin_app.library); }
                    catch(e){}
                    setTimeout(function(){
                        if(response.status == "ok"){
                            toastr["success"](response.message);
                        }else{
                            toastr["error"](response.message,"Error");
                        }
                        this.enableState();
                        this.uploadZipEnd();
                    }.bind(this),1000);
                }
            });
        }
        modal.confirm("Do you want to delete associated zip file?",deleteZipFile.bind(this));
    }
};
