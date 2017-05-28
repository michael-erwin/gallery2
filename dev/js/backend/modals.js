/* System */
 var modal = {
    "message" : function(message,fn_ok) {
        $('#modal_message').modal('show');
        $('#modal_message .modal-body').text(message);
        var ok_btn = $('#modal_message [data-btn="ok"]');
        if(fn_ok){
            ok_btn.unbind('click').click(function(){
                fn_ok();
                $('#modal_message').modal('hide');
            });
        }
        else{
            ok_btn.unbind('click').click(function(){
                $('#modal_message').modal('hide');
            });
        }
    },
    "confirm" : function(message,fn_yes,fn_no) {
        $('#modal_confirm').modal('show');
        $('#modal_confirm .modal-body').text(message);
        var yes_btn = $('#modal_confirm [data-btn="yes"]');
        if(fn_yes){
            yes_btn.unbind('click').click(function(){
                fn_yes();
                $('#modal_confirm').modal('hide');
            });
            if(fn_no) $('#modal_confirm').unbind('hide.bs.modal').on('hide.bs.modal', fn_no);
        }
        else{
            yes_btn.unbind('click').click(function(){
                $('#modal_confirm').modal('hide');
            });
            $('#modal_confirm').unbind('hide.bs.modal');
        }
    }
 };

/* Media Information */
var modal_media = {
    objects: {
        self: $('#modal_media_details'),
        content_body: $('#modal_media_details .media-item-display'),
        media_box: $('#modal_media_details .media-block .media'),
        info_box: $('#modal_media_details .info-block .info')
    },
    open: function(e){
        e.preventDefault();
        var thumb = $(e.target).parents('.media-entry');
        var data = thumb.data('data');
        var type = admin_app.library.data.type; // <-- Dependency
        var contents = this.buildTable(data,type);
        this.objects.self.modal('show');
        this.objects.content_body.addClass('loading');
        this.objects.media_box.removeClass('media-photo media-video');
        setTimeout(function(){
            this.objects.content_body.removeClass('loading');
            this.objects.info_box.html(contents);
            if(type == "photos") {
                var photo_link = site.base_url+'photos/preview/lg/'+(data.title).replace(' ','-')+'-'+data.uid;
                this.objects.media_box.removeClass('media-video').addClass('media-photo');
                photo_page_box.init.call(photo_page_box);
                photo_page_box.objects.main_photo.attr('src',photo_link);
            }
            else if(type == "videos") {
                var video_link = site.base_url+'videos/preview/'+(data.title).replace(' ','-')+'-'+data.uid;
                var video_poster = site.base_url+'media/videos/public/480/'+data.uid+'.jpg';
                this.objects.media_box.removeClass('media-photo').addClass('media-video');
                video_page_box.poster(video_poster);
                video_page_box.src(video_link);
                video_page_box.play();
            }
        }.bind(this),500);
    },
    buildTable: function(data,type) {
        var mime = (type == "photos")? "JPG" : "MP4";
        var tags = (data.tags).split(' ');
        var tag_html = "";
        var btn_zip = "";
        for(var i=0;i<tags.length;i++) {
            var tag_seo_link = site.base_url+'search/'+type+'?kw='+tags[i];
            tag_html += '<a class="label label-default" href="'+tag_seo_link+'">'+tags[i]+'</a>&nbsp;';
        }
        if(data.has_zip){
            if(data.has_zip > 0){btn_zip = '\n<button class="btn btn-danger" data-mime="ZIP" data-type="'+type+'" data-title="'+data.title+'" data-uid="'+data.uid+'" onclick="media_file.download(this)" >All (ZIP)</button>';}
        }
        function formatSizeUnits(bytes){
            if(bytes == 0) return '0 Bytes';
            var k = 1000,
                dm = 2,
                sizes = ['B','kB','MB','GB','TB'],
                i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        };
        var table =
            '<table class="table table-bordered">'+
                '<thead>'+
                    '<tr><th colspan="2">Details</th></tr>'+
                '</thead>'+
                '<tbody>'+
                    '<tr><td>Title</td><td>'+data.title+'</td></tr>'+
                    '<tr><td>Description</td><td>'+data.description+'</td></tr>'+
                    '<tr><td>Dimension</td><td>'+data.width+'x'+data.height+'</td></tr>'+
                    '<tr><td>File size</td><td>'+formatSizeUnits(data.file_size)+'</td></tr>'+
                '</tbody>'+
            '</table>'+
            '<table class="table table-bordered">'+
                '<thead>'+
                    '<tr><th>Tags</th></tr>'+
                '</thead>'+
                '<tbody>'+
                    '<tr><td>'+
                        tag_html+
                    '</td></tr>'+
                '</tbody>'+
            '</table>';
        return table;
    }
};
// Image Details Page Box
var photo_page_box = {
    objects: {
        main_box: $('.media-item-display .media-photo'),
        main_photo: $('.media-item-display .media-photo img'),
        fs_button: $('.media-item-display .media-photo .display-options span.fullscreen'),
        fs_x_button: $('#photo_fullscreen .exit-btn'),
        fs_content: $('#photo_fullscreen .display-content')
    },
    data: {
        fullscreen: false
    },
    init: function() {
        this.objects.main_box = $('.media-item-display .media-photo');
        this.objects.main_photo = $('.media-item-display .media-photo img');
        this.objects.fs_button = $('.media-item-display .media-photo .display-options span.fullscreen');
        this.objects.fs_x_button = $('#photo_fullscreen .exit-btn');
        this.objects.fs_content = $('#photo_fullscreen .display-content');

        this.objects.main_photo.unbind('load').on('load',this.stateLoaded.bind(this));
        this.objects.fs_button.unbind('click').on('click', this.goFullScreen.bind(this));
        this.objects.fs_x_button.unbind('click').on('click', this.exitFullScreen.bind(this));
    },
    render: function() {
        if(this.data.fullscreen) {
            var photo_url = this.objects.main_photo.attr('src');
            var content = '<img src="'+photo_url+'">';
            this.objects.fs_content.html(content);
            media_box.goFullScreen('photo_fullscreen');
        }
        else {
            this.objects.fs_content.html("");
            media_box.exitFullScreen();
        }
    },
    goFullScreen: function() {
        this.data.fullscreen = true;
        this.render();
    },
    exitFullScreen: function() {
        this.data.fullscreen = false;
        this.render();
    },
    stateLoaded: function() {
        this.objects.main_box.addClass('loaded');
    }
};
// Video Details Page Box
var video_page_box = (function(){
    if(document.querySelector('#video_item_object')){
        var player = videojs("video_item_object",{"controls": true, "autoplay": false});
        $('#modal_media_details').on('hidden.bs.modal',player.pause.bind(player));
        return player;
    }
}());
// Media Fullscreen
var media_box = {
    goFullScreen : function(id) {
        var el = document.getElementById(id);
        if (el.requestFullscreen) {
            el.requestFullscreen();
        } else if (el.webkitRequestFullscreen) {
            el.webkitRequestFullscreen();
        } else if (el.mozRequestFullScreen) {
            el.mozRequestFullScreen();
        } else if (el.msRequestFullscreen) {
            el.msRequestFullscreen();
        }
    },
    exitFullScreen: function() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
};
