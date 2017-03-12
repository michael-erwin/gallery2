// Media
var media_file = {
    download: function(o){
        var uid = $(o).data('uid');
        var title = $(o).data('title');
        var mime = $(o).data('mime');
        var type = $(o).data('type');
        var mode = "zip";
        var filename = title.replace(' ','-')+'-'+uid;

        if(mime == "JPG" || mime == "MP4") mode = "full";
        
        if(type == "photos"){
            window.location = site.base_url+'photos/download/'+mode+'/'+filename;
        }
        else if(type == "videos"){
            window.location = site.base_url+'videos/download/'+mode+'/'+filename;
        }
    }
};
