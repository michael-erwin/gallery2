// Favorites
var favorites = {
    self: null,
    objects: {
        action_box: null,
        photos: null,
        videos: null,
        badge_photos: null,
        badge_videos: null,
        modal: null,
        modal_body: null
    },
    data: {
        photos: [],
        videos: []
    },
    init: function() {
        this.objects.action_box = $('#actions');
        this.self = $('#menu_favorites');
        this.objects.menu_photos = this.self.find('[data-id="photos"]');
        this.objects.menu_videos = this.self.find('[data-id="videos"]');
        this.objects.badge_photos = this.self.find('[data-id="fav_badge_photos"]');
        this.objects.badge_videos = this.self.find('[data-id="fav_badge_videos"]');
        this.objects.modal = $('#modal_favorites');
        this.objects.modal_title = $('#modal_favorites h4.modal-title');
        this.objects.modal_body = $('#modal_favorites [data-id="contents"]');
        this.getData.call(this);
    },
    render() {
        // Item count display.
        if(this.data.photos.length > 0) {
            this.objects.badge_photos.text(this.data.photos.length).css('display','inline');
        }
        else {
            this.objects.badge_photos.text(this.data.photos.length).css('display','none');
        }
        if(this.data.videos.length > 0) {
            this.objects.badge_videos.text(this.data.videos.length).css('display','inline');
        }
        else {
            this.objects.badge_videos.text(this.data.videos.length).css('display','none');
        }
        
        // Event bindings.
        this.objects.menu_photos.unbind('click').on('click',this.fetch.bind(this));
        this.objects.menu_videos.unbind('click').on('click',this.fetch.bind(this));
    },
    add: function(e) {
        var parent_thumb = $(e.target).parents('.thumb');
        var parent_data = JSON.parse(parent_thumb.attr('data-data'));
        var media_type = parent_thumb.attr('data-media');
        var media_type_s = media_type+'s';
        var existed = $.inArray(parent_data.id,this.data[media_type_s]);
        if(existed === -1) {
            $.ajax({
                method: "POST",
                context: this,
                url: site.base_url+'favorites/add/'+media_type,
                data: 'id='+parent_data.id,
                error: function(jqXHR,textStatus,errorThrown){
                    toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
                },
                success: function(response){
                    if(response.status == "ok"){
                        this.data = response.data;
                        toastr["success"]("Item \""+parent_data.title+"\" added to favorites.","Success");
                        this.render();
                    }else{
                        toastr["error"](response.message,"Error");
                    }
                }
            });
        }
        else {
            toastr["info"]("Item \""+parent_data.title+"\" already exist.");
        }
    },
    remove: function(e) {
        var parent_item = $(e.target).parents('.item');
        var id = parent_item.attr('data-id');
        var type = parent_item.attr('data-type');
        $.ajax({
            method: "POST",
            context: this,
            url: site.base_url+'favorites/remove/'+type,
            data: 'id='+id,
            error: function(jqXHR,textStatus,errorThrown){
                toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
            },
            success: function(response){
                if(response.status == "ok"){
                    this.data = response.data;
                    parent_item.remove();
                    if(this.data[type+'s'].length == 0) this.objects.modal.modal('hide');
                    toastr["success"](response.message);
                    this.render();
                }else{
                    toastr["error"](response.message,"Error");
                }
            }
        });
    },
    getCookie: function(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    },
    setCookie: function() {
        var d = new Date();
        d.setDate(d.getDate()+14);
        var expires = "; expires="+ d.toUTCString();
        var path = "; path=/";
        document.cookie = "favorites="+JSON.stringify(this.data)+expires+path;
    },
    getData: function() {
        $.ajax({
            method: "GET",
            context: this,
            url: site.base_url+'favorites/get',
            error: function(jqXHR,textStatus,errorThrown){
                toastr["error"]("Failed to load content.", "Error "+jqXHR.status);
            },
            success: function(response){
                if(response.status == "ok"){
                    this.data = response.data;
                    this.render();
                }else{
                    if(response.code != 403) toastr["error"](response.message,"Error");
                }
            }
        });
    },
    fetch: function(e) {
        var media_type = $(e.target).data('id');
        if(media_type == "photos") {
            if(this.data.photos.length > 0) {
                var loading = '<div class="favorites-loading"><img src="'+site.base_url+'assets/img/hourglass.gif" /></div>';
                this.objects.modal_title.text('My Favorites (Photos)');
                this.objects.modal_body.html(loading);
                this.objects.modal.modal('show');
                $.ajax({
                    type: "get",
                    context: this,
                    url: site.base_url+"photos/info?id="+this.data.photos.join(','),
                    context: this,
                    success: function(response) {
                        var data = response.data;
                        var thumbs = "";
                        if(!data.length) {
                            var seo_link = data.title.split(' ');
                            thumbs +=
                            '<div class="item col-xs-6 col-sm-4 col-md-3" data-type="photo" data-id="'+data.id+'">'+
                                '<a class="favorites-img-preview" title="'+data.title+'" href="'+site.base_url+'photos/item/'+seo_link.join('-')+'-'+data.uid+'" target="_blank" style="background-image:url('+site.base_url+'media/photos/public/128/'+data.uid+'.jpg)"></a>'+
                                '<div class="controls">'+
                                    '<a class="remove-button overlay-ctrl-btn" title="Remove"><span class="glyphicon glyphicon-remove"></span></a>'+
                                '</div>'+
                            '</div>';
                        }
                        else if(data.length > 0) {
                            for(var x=0; x<data.length; x++) {
                                var seo_link = data[x].title.split(' ');
                                thumbs +=
                                '<div class="item col-xs-6 col-sm-4 col-md-3" data-type="photo" data-id="'+data[x].id+'">'+
                                    '<a class="favorites-img-preview" title="'+data[x].title+'" href="'+site.base_url+'photos/item/'+seo_link.join('-')+'-'+data[x].uid+'" target="_blank" style="background-image:url('+site.base_url+'media/photos/public/128/'+data[x].uid+'.jpg)"></a>'+
                                    '<div class="controls">'+
                                        '<a class="remove-button overlay-ctrl-btn" title="Remove"><span class="glyphicon glyphicon-remove"></span></a>'+
                                    '</div>'+
                                '</div>';
                            }
                        }

                        this.objects.modal_body.html(thumbs);
                        this.objects.modal_body.find("a.remove-button").unbind("click").click(this.remove.bind(this));
                        this.objects.modal.modal('show');
                    }
                });
            }
        }
        else if(media_type == "videos") {
            if(this.data.videos.length > 0) {
                var loading = '<div class="favorites-loading"><img src="'+site.base_url+'assets/img/hourglass.gif" /></div>';
                this.objects.modal_title.text('My Favorites (Videos)');
                this.objects.modal_body.html(loading);
                this.objects.modal.modal('show');
                $.ajax({
                    type: "get",
                    url: site.base_url+"videos/info?id="+this.data.videos.join(','),
                    context: this,
                    success: function(response) {
                        var data = response.data;
                        var thumbs = "";
                        if(!data.length) {
                            var seo_link = data.title.split(' ');
                            thumbs +=
                            '<div class="item col-xs-6 col-sm-4 col-md-3" data-type="video" data-id="'+data.id+'">'+
                                '<a class="favorites-img-preview" title="'+data.title+'" href="'+site.base_url+'videos/item/'+seo_link.join('-')+'-'+data.uid+'" target="_blank" style="background-image:url('+site.base_url+'media/videos/public/128/'+data.uid+'.jpg)"></a>'+
                                '<div class="controls">'+
                                    '<a class="remove-button overlay-ctrl-btn" title="Remove"><span class="glyphicon glyphicon-remove"></span></a>'+
                                '</div>'+
                            '</div>';
                        }
                        else if(data.length > 0) {
                            for(var x=0; x<data.length; x++) {
                                var seo_link = data[x].title.split(' ');
                                thumbs +=
                                '<div class="item col-xs-6 col-sm-4 col-md-3" data-type="video" data-id="'+data[x].id+'">'+
                                    '<a class="favorites-img-preview" title="'+data[x].title+'" href="'+site.base_url+'videos/item/'+seo_link.join('-')+'-'+data[x].uid+'" target="_blank" style="background-image:url('+site.base_url+'media/videos/public/128/'+data[x].uid+'.jpg)"></a>'+
                                    '<div class="controls">'+
                                        '<a class="remove-button overlay-ctrl-btn" title="Remove"><span class="glyphicon glyphicon-remove"></span></a>'+
                                    '</div>'+
                                '</div>';
                            }
                        }

                        this.objects.modal_body.html(thumbs);
                        this.objects.modal_body.find("a.remove-button").unbind("click").click(this.remove.bind(this));
                        this.objects.modal.modal('show');
                    }
                });
            }
        }
    }
};
