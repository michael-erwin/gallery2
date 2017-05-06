function formatSizeUnits(bytes){if(bytes == 0) return '0 Bytes';var k = 1000,dm = 2,sizes = ['B', 'kB', 'MB', 'GB', 'TB'],i = Math.floor(Math.log(bytes) / Math.log(k));return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];};
String.prototype.UCFirst = function() {return this.charAt(0).toUpperCase() + this.slice(1);}; var media_file = { download: function(o){ var uid = $(o).data('uid'); var title = $(o).data('title'); var mime = $(o).data('mime'); var type = $(o).data('type'); var mode = "zip"; var filename = title.replace(' ','-')+'-'+uid; if(mime == "JPG" || mime == "MP4") mode = "full"; if(type == "photos"){ window.location = site.base_url+'photos/download/'+mode+'/'+filename; } else if(type == "videos"){ window.location = site.base_url+'videos/download/'+mode+'/'+filename; } }
}; var favorites = { self: null, objects: { action_box: null, photos: null, videos: null, badge_photos: null, badge_videos: null, modal: null, modal_body: null }, data: { photos: [], videos: [] }, init: function() { this.objects.action_box = $('#actions'); this.self = $('#menu_favorites'); this.objects.menu_photos = this.self.find('[data-id="photos"]'); this.objects.menu_videos = this.self.find('[data-id="videos"]'); this.objects.badge_photos = this.self.find('[data-id="fav_badge_photos"]'); this.objects.badge_videos = this.self.find('[data-id="fav_badge_videos"]'); this.objects.modal = $('#modal_favorites'); this.objects.modal_title = $('#modal_favorites h4.modal-title'); this.objects.modal_body = $('#modal_favorites [data-id="contents"]'); this.getData.call(this); }, render() { if(this.data.photos.length > 0) { this.objects.badge_photos.text(this.data.photos.length).css('display','inline'); } else { this.objects.badge_photos.text(this.data.photos.length).css('display','none'); } if(this.data.videos.length > 0) { this.objects.badge_videos.text(this.data.videos.length).css('display','inline'); } else { this.objects.badge_videos.text(this.data.videos.length).css('display','none'); } this.objects.menu_photos.unbind('click').on('click',this.fetch.bind(this)); this.objects.menu_videos.unbind('click').on('click',this.fetch.bind(this)); }, add: function(e) { var parent_thumb = $(e.target).parents('.thumb'); var parent_data = JSON.parse(parent_thumb.attr('data-data')); var media_type = parent_thumb.attr('data-media'); var media_type_s = media_type+'s'; var existed = $.inArray(parent_data.id,this.data[media_type_s]); if(existed === -1) { $.ajax({ method: "POST", context: this, url: site.base_url+'favorites/add/'+media_type, data: 'id='+parent_data.id, error: function(jqXHR,textStatus,errorThrown){ toastr["error"]("Failed to load content.", "Error "+jqXHR.status); }, success: function(response){ if(response.status == "ok"){ this.data = response.data; toastr["success"]("Item \""+parent_data.title+"\" added to favorites.","Success"); this.render(); }else{ if(response.code == 403){ toastr["info"]("This feature is for registered members only. Please sign in with your account or sign up.","Notice"); }else{ toastr["error"](response.message,"Error"); } } } }); } else { toastr["info"]("Item \""+parent_data.title+"\" already exist."); } }, remove: function(e) { var parent_item = $(e.target).parents('.item'); var id = parent_item.attr('data-id'); var type = parent_item.attr('data-type'); $.ajax({ method: "POST", context: this, url: site.base_url+'favorites/remove/'+type, data: 'id='+id, error: function(jqXHR,textStatus,errorThrown){ toastr["error"]("Failed to load content.", "Error "+jqXHR.status); }, success: function(response){ if(response.status == "ok"){ this.data = response.data; parent_item.remove(); if(this.data[type+'s'].length == 0) this.objects.modal.modal('hide'); toastr["success"](response.message); this.render(); }else{ toastr["error"](response.message,"Error"); } } }); }, getCookie: function(name) { var nameEQ = name + "="; var ca = document.cookie.split(';'); for(var i=0;i < ca.length;i++) { var c = ca[i]; while (c.charAt(0)==' ') c = c.substring(1,c.length); if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length); } return null; }, setCookie: function() { var d = new Date(); d.setDate(d.getDate()+14); var expires = "; expires="+ d.toUTCString(); var path = "; path=/"; document.cookie = "favorites="+JSON.stringify(this.data)+expires+path; }, getData: function() { $.ajax({ method: "GET", context: this, url: site.base_url+'favorites/get', error: function(jqXHR,textStatus,errorThrown){ toastr["error"]("Failed to load content.", "Error "+jqXHR.status); }, success: function(response){ if(response.status == "ok"){ this.data = response.data; this.render(); }else{ if(response.code != 403) toastr["error"](response.message,"Error"); } } }); }, fetch: function(e) { var media_type = $(e.target).data('id'); if(media_type == "photos") { if(this.data.photos.length > 0) { var loading = '<div class="favorites-loading"><img src="'+site.base_url+'assets/img/hourglass.gif" /></div>'; this.objects.modal_title.text('My Favorites (Photos)'); this.objects.modal_body.html(loading); this.objects.modal.modal('show'); $.ajax({ type: "get", context: this, url: site.base_url+"photos/info?id="+this.data.photos.join(','), context: this, success: function(response) { var data = response.data; var thumbs = ""; if(!data.length) { var seo_link = data.title.split(' '); thumbs += '<div class="item col-xs-6 col-sm-4 col-md-3" data-type="photo" data-id="'+data.id+'">'+ '<a class="favorites-img-preview" title="'+data.title+'" href="'+site.base_url+'photos/item/'+seo_link.join('-')+'-'+data.uid+'" target="_blank" style="background-image:url('+site.base_url+'media/photos/public/128/'+data.uid+'.jpg)"></a>'+ '<div class="controls">'+ '<a class="remove-button overlay-ctrl-btn" title="Remove"><span class="glyphicon glyphicon-remove"></span></a>'+ '</div>'+ '</div>'; } else if(data.length > 0) { for(var x=0; x<data.length; x++) { var seo_link = data[x].title.split(' '); thumbs += '<div class="item col-xs-6 col-sm-4 col-md-3" data-type="photo" data-id="'+data[x].id+'">'+ '<a class="favorites-img-preview" title="'+data[x].title+'" href="'+site.base_url+'photos/item/'+seo_link.join('-')+'-'+data[x].uid+'" target="_blank" style="background-image:url('+site.base_url+'media/photos/public/128/'+data[x].uid+'.jpg)"></a>'+ '<div class="controls">'+ '<a class="remove-button overlay-ctrl-btn" title="Remove"><span class="glyphicon glyphicon-remove"></span></a>'+ '</div>'+ '</div>'; } } this.objects.modal_body.html(thumbs); this.objects.modal_body.find("a.remove-button").unbind("click").click(this.remove.bind(this)); this.objects.modal.modal('show'); } }); } } else if(media_type == "videos") { if(this.data.videos.length > 0) { var loading = '<div class="favorites-loading"><img src="'+site.base_url+'assets/img/hourglass.gif" /></div>'; this.objects.modal_title.text('My Favorites (Videos)'); this.objects.modal_body.html(loading); this.objects.modal.modal('show'); $.ajax({ type: "get", url: site.base_url+"videos/info?id="+this.data.videos.join(','), context: this, success: function(response) { var data = response.data; var thumbs = ""; if(!data.length) { var seo_link = data.title.split(' '); thumbs += '<div class="item col-xs-6 col-sm-4 col-md-3" data-type="video" data-id="'+data.id+'">'+ '<a class="favorites-img-preview" title="'+data.title+'" href="'+site.base_url+'videos/item/'+seo_link.join('-')+'-'+data.uid+'" target="_blank" style="background-image:url('+site.base_url+'media/videos/public/128/'+data.uid+'.jpg)"></a>'+ '<div class="controls">'+ '<a class="remove-button overlay-ctrl-btn" title="Remove"><span class="glyphicon glyphicon-remove"></span></a>'+ '</div>'+ '</div>'; } else if(data.length > 0) { for(var x=0; x<data.length; x++) { var seo_link = data[x].title.split(' '); thumbs += '<div class="item col-xs-6 col-sm-4 col-md-3" data-type="video" data-id="'+data[x].id+'">'+ '<a class="favorites-img-preview" title="'+data[x].title+'" href="'+site.base_url+'videos/item/'+seo_link.join('-')+'-'+data[x].uid+'" target="_blank" style="background-image:url('+site.base_url+'media/videos/public/128/'+data[x].uid+'.jpg)"></a>'+ '<div class="controls">'+ '<a class="remove-button overlay-ctrl-btn" title="Remove"><span class="glyphicon glyphicon-remove"></span></a>'+ '</div>'+ '</div>'; } } this.objects.modal_body.html(thumbs); this.objects.modal_body.find("a.remove-button").unbind("click").click(this.remove.bind(this)); this.objects.modal.modal('show'); } }); } } }
}; var results = { document: $('body'), self: $('#results_app'), data: { type: 'photos', keywords: '', category_id: '', category_name: '', main_category_id: "", main_category_name: "", crumbs: {'Home': ""}, route: 'search', page: { current: 1, total: 0, limit: 20 }, items: { entries: [], total: 0 } }, objects: { breadcrumbs: null, search_form: null, search_type_display: null, search_type_options: null, pagination: null, pagination_index: null, pagination_total: null, pagination_prev: null, pagination_next: null, thumbs_box: null, category_thumbs_box: null, loading: null }, init: function(){ this.self = $('#results_app'); this.objects.breadcrumbs = this.self.find('[data-id="breadcrumbs"]'); this.objects.search_form = this.self.find('[data-id="search_form"]'); this.objects.search_type_display = this.self.find('[data-id="search_box_display"]'); this.objects.search_type_options = this.self.find('[data-id="search_box_option"]'); this.objects.pagination = this.self.find('.m-pagination'); this.objects.pagination_index = this.self.find('.m-pagination .index'); this.objects.pagination_total = this.self.find('.m-pagination .total'); this.objects.pagination_prev = this.self.find('.m-pagination button.prev'); this.objects.pagination_next = this.self.find('.m-pagination button.next'); this.objects.thumbs_box = this.self.find('[data-id="thumbs"]'); this.objects.category_thumbs_box = $('#category_thumbs_display'); this.objects.media_item_box = $('#media_item_display'); this.objects.loading = this.self.find('[data-id="loading"]'); this.objects.pagination_index.unbind().on('keypress',this.goPage.bind(this)) .on('paste',function(){return false}); this.objects.search_type_options.unbind().on('click',this.pickType.bind(this)); this.objects.search_form.unbind().on('submit',this.getData.bind(this)); this.objects.pagination_prev.unbind().on('click',this.prevPage.bind(this)); this.objects.pagination_next.unbind().on('click',this.nextPage.bind(this)); this.attachThumbActions(); }, getData: function(e) { if(e) { e.preventDefault(); this.data.route = "search"; var get_url = site.base_url+"search/"+this.data.type; this.data.keywords = this.objects.search_form.find('[name="kw"]').val(); var data = { kw: this.data.keywords, p: 1, l: this.data.page.limit, m: 'json' }; } else if(this.data.route == "search") { var get_url = site.base_url+"search/"+this.data.type; this.data.keywords = this.objects.search_form.find('[name="kw"]').val(); var data = { kw: this.data.keywords, p: this.data.page.current, l: this.data.page.limit, m: 'json' }; } else if(this.data.route == "categories") { var get_url = site.base_url+"categories/"+ this.data.main_category_name+'-'+this.data.main_category_id+'/'+ this.data.category_name+'-'+this.data.category_id+'/'+ this.data.type+'/'+ this.data.page.current; var data = { l: this.data.page.limit, m: 'json' }; if(page.share_id.length === 32) data.share_id = page.share_id; } $.ajax({ type: "get", url: get_url, context: this, data: data, dataType: "json", success: function(response) { this.data.crumbs = response.crumbs; this.data.page = response.page; this.data.items = response.items; this.data.page_meta = response.page_meta; this.document.animate({scrollTop: "0px"},300,this.render.bind(this)); } }); }, render: function(){ var entries = this.data.items.entries; var html = ""; document.title = this.data.page_meta.title; if(entries.length > 0){ if(this.data.type == "photos") { for(var photo in entries) { var photo = entries[photo]; var data = JSON.stringify(photo); var thumb = site.base_url+'media/photos/public/256/'+photo.uid+'.jpg'; var seo_link = photo.title.split(' '); seo_link = site.base_url+'photos/item/'+seo_link.join('-')+'-'+photo.uid; html += '<div class="thumb-box centered col-md-3 col-sm-4 col-xs-6">'+ '<div class="thumb" data-data=\''+data.replace("'","")+'\' data-media="photo">'+ '<a title="'+photo.title+'" class="image-link photo-preview" href="'+seo_link+'" style="background-image:url(\''+thumb+'\')">'+ '<img src="'+thumb+'" />'+ '</a>'+ '<div class="title">'+ '<span>'+photo.title+'</span>'+ '</div>'+ '<div class="options">'+ '<b title="Download" data-id="download"><i class="fa fa-lg fa-download"></i></b>'+ '<b title="Add to favorites" data-id="favorites"><i class="fa fa-lg fa-star"></i></b>'+ '</div>'+ '</div>'+ '</div>'; } } else if(this.data.type == "videos") { for(var video in entries) { var video = entries[video]; var data = JSON.stringify(video); var thumb = site.base_url+'media/videos/public/256/'+video.uid+'.jpg'; var seo_link = site.base_url+'videos/item/'+video.title.replace(' ','-')+'-'+video.uid; html += '<div class="thumb-box centered col-md-3 col-sm-4 col-xs-6">'+ '<div class="thumb" data-data=\''+data.replace("'","")+'\' data-media="video">'+ '<a title="'+video.title+'" class="image-link video-preview" href="'+seo_link+'" style="background-image:url(\''+thumb+'\')">'+ '<img src="'+thumb+'" />'+ '</a>'+ '<div class="title">'+ '<span>'+video.title+'</span>'+ '</div>'+ '<div class="options">'+ '<b title="Download" data-id="download"><i class="fa fa-lg fa-download"></i></b>'+ '<b title="Add to favorites" data-id="favorites"><i class="fa fa-lg fa-star"></i></b>'+ '</div>'+ '</div>'+ '</div>'; } } } else { html = '<div class="alert alert-warning">No results.</div>'; } var breadcrumbs = ""; for(var crumb in this.data.crumbs){ if(this.data.crumbs[crumb] != "") { breadcrumbs += '\n<li><a href="'+this.data.crumbs[crumb]+'">'+crumb+'</a></li>'; } else { breadcrumbs += '\n<li><a class="active">'+crumb+'</a></li>'; } } this.objects.breadcrumbs.html('<ol class="breadcrumb">'+breadcrumbs+'</ol>'); this.objects.pagination_total.text(this.data.page.total); if(this.data.page.current < this.data.page.total) { this.objects.pagination_next.prop('disabled', false); } else { this.objects.pagination_next.prop('disabled', true); } if(this.data.page.current > 1) { this.objects.pagination_prev.prop('disabled', false); } else { this.objects.pagination_prev.prop('disabled', true); } if(this.data.page.total == 0) { this.objects.pagination_index.prop('disabled', true); } else { this.objects.pagination_index.prop('disabled', false); } this.objects.pagination_index.val(this.data.page.current); this.objects.category_thumbs_box.html(''); this.objects.media_item_box.html(''); this.objects.thumbs_box.html(html); var current_uri = ""; if(this.data.route == "search") { current_uri = site.base_url+'search/'+this.data.type+'?kw='+this.data.keywords+'&p='+this.data.page.current; } else if(this.data.route == "categories") { current_uri = site.base_url+'categories/'+ this.data.main_category_name+'-'+this.data.main_category_id+'/'+ this.data.category_name+'-'+this.data.category_id+'/'+ this.data.type+'/'+ this.data.page.current; if(page.share_id.length === 32) current_uri += '?share_id='+page.share_id; } history.replaceState(null, null, current_uri); this.attachThumbActions(); }, attachThumbActions: function() { this.objects.thumbs_box.find('a').unbind('click').click(modal_media.open.bind(modal_media)); this.objects.thumbs_box.find('[data-id="favorites"]').unbind('click').click(favorites.add.bind(favorites)); this.objects.thumbs_box.find('[data-id="download"]').unbind('click').click(this.download.bind(this)); }, pickType: function(e){ var self = $(e.target); var parent = self.parent('li'); var type = parent.attr('data-value'); this.objects.search_type_options.removeClass("selected"); parent.addClass("selected"); this.objects.search_type_display.text(type.UCFirst()); this.data.type = type; this.objects.search_form.find('[name="type"]').val(type); self.parents('div.search-types').blur(); }, prevPage: function(){ if(this.data.page.current > 1) { this.data.page.current -= 1; this.getData(); } }, goPage: function(e){ if(e.keyCode < 48 || e.keyCode > 57){ if(e.keyCode == 13){ this.data.page.current = $(e.target).val(); this.getData(); } else{ return false; } } else{ console.log(e.target.value); } }, nextPage: function(){ if(this.data.page.current < this.data.page.total) { this.data.page.current += 1; this.getData(); } }, scrollToTop(){ this.document.animate({scrollTop: "0px"},300,function(){}); }, download: function(e) { var thumb = $(e.target).parents(".thumb"); var data = JSON.parse(thumb.attr("data-data")); var type = thumb.attr("data-media"); var filename = (data.title).replace(' ','-')+'-'+data.uid; if(type == "photo") { window.location = site.base_url+'photos/download/full/'+filename; } if(type == "video") { window.location = site.base_url+'videos/download/full/'+filename; } }
}; var media_box = { goFullScreen : function(id) { var el = document.getElementById(id); if (el.requestFullscreen) { el.requestFullscreen(); } else if (el.webkitRequestFullscreen) { el.webkitRequestFullscreen(); } else if (el.mozRequestFullScreen) { el.mozRequestFullScreen(); } else if (el.msRequestFullscreen) { el.msRequestFullscreen(); } }, exitFullScreen: function() { if (document.exitFullscreen) { document.exitFullscreen(); } else if (document.webkitExitFullscreen) { document.webkitExitFullscreen(); } else if (document.mozCancelFullScreen) { document.mozCancelFullScreen(); } else if (document.msExitFullscreen) { document.msExitFullscreen(); } }
}; var modal_media = { objects: { self: $('#modal_media_details'), content_body: $('#modal_media_details .media-item-display'), media_box: $('#modal_media_details .media-block .media'), info_box: $('#modal_media_details .info-block .info') }, open: function(e){ e.preventDefault(); var thumb = $(e.target).parents('.thumb'); var data = JSON.parse(thumb.attr('data-data')); var type = thumb.attr('data-media')+'s'; var contents = this.buildTable(data,type); this.objects.self.modal('show'); this.objects.content_body.addClass('loading'); this.objects.media_box.removeClass('media-photo media-video'); setTimeout(function(){ this.objects.content_body.removeClass('loading'); this.objects.info_box.html(contents); if(type == "photos") { var photo_link = site.base_url+'photos/preview/lg/'+(data.title).replace(' ','-')+'-'+data.uid; this.objects.media_box.removeClass('media-video').addClass('media-photo'); photo_page_box.init.call(photo_page_box); photo_page_box.objects.main_photo.attr('src',photo_link); } else if(type == "videos") { var video_link = site.base_url+'videos/preview/'+(data.title).replace(' ','-')+'-'+data.uid; var video_poster = site.base_url+'media/videos/public/480/'+data.uid+'.jpg'; this.objects.media_box.removeClass('media-photo').addClass('media-video'); video_page_box.poster(video_poster); video_page_box.src(video_link); video_page_box.play(); } }.bind(this),500); }, buildTable: function(data,type) { var mime = (type == "photos")? "JPG" : "MP4"; var tags = (data.tags).split(' '); var tag_html = ""; var btn_zip = ""; for(var i=0;i<tags.length;i++) { var tag_seo_link = site.base_url+'search/'+type+'?kw='+tags[i]; tag_html += '<a class="label label-default" href="'+tag_seo_link+'">'+tags[i]+'</a>&nbsp;'; } if(data.has_zip){ if(data.has_zip > 0){btn_zip = '\n<button class="btn btn-danger" data-mime="ZIP" data-type="'+type+'" data-title="'+data.title+'" data-uid="'+data.uid+'" onclick="media_file.download(this)" >All (ZIP)</button>';} } var table = '<table class="table table-bordered">'+ '<thead>'+ '<tr><th colspan="2">Details</th></tr>'+ '</thead>'+ '<tbody>'+ '<tr><td>Title</td><td>'+data.title+'</td></tr>'+ '<tr><td>Description</td><td>'+data.description+'</td></tr>'+ '<tr><td>Dimension</td><td>'+data.width+'x'+data.height+'</td></tr>'+ '<tr><td>File size</td><td>'+formatSizeUnits(data.file_size)+'</td></tr>'+ '</tbody>'+ '</table>'+ '<table class="table table-bordered">'+ '<thead>'+ '<tr><th>Tags</th></tr>'+ '</thead>'+ '<tbody>'+ '<tr><td>'+ tag_html+ '</td></tr>'+ '</tbody>'+ '</table>'+ '<table class="table table-bordered">'+ '<thead>'+ '<tr><th>Downloads</th></tr>'+ '</thead>'+ '<tbody>'+ '<tr><td style="text-align:center">'+ '<button class="btn btn-success" data-mime="'+mime+'" data-type="'+type+'" data-title="'+data.title+'" data-uid="'+data.uid+'" onclick="media_file.download(this)">Full ('+mime+')</button>'+btn_zip+ '</td></tr>'+ '</tbody>'+ '</table>'; return table; }
}; var photo_page_box = { objects: { main_box: $('.media-item-display .media-photo'), main_photo: $('.media-item-display .media-photo img'), fs_button: $('.media-item-display .media-photo .display-options span.fullscreen'), fs_x_button: $('#photo_fullscreen .exit-btn'), fs_content: $('#photo_fullscreen .display-content') }, data: { fullscreen: false }, init: function() { this.objects.main_box = $('.media-item-display .media-photo'); this.objects.main_photo = $('.media-item-display .media-photo img'); this.objects.fs_button = $('.media-item-display .media-photo .display-options span.fullscreen'); this.objects.fs_x_button = $('#photo_fullscreen .exit-btn'); this.objects.fs_content = $('#photo_fullscreen .display-content'); this.objects.main_photo.unbind('load').on('load',this.stateLoaded.bind(this)); this.objects.fs_button.unbind('click').on('click', this.goFullScreen.bind(this)); this.objects.fs_x_button.unbind('click').on('click', this.exitFullScreen.bind(this)); }, render: function() { if(this.data.fullscreen) { var photo_url = this.objects.main_photo.attr('src'); var content = '<img src="'+photo_url+'">'; this.objects.fs_content.html(content); media_box.goFullScreen('photo_fullscreen'); } else { this.objects.fs_content.html(""); media_box.exitFullScreen(); } }, goFullScreen: function() { this.data.fullscreen = true; this.render(); }, exitFullScreen: function() { this.data.fullscreen = false; this.render(); }, stateLoaded: function() { this.objects.main_box.addClass('loaded'); }
}; var video_page_box = (function(){ if(document.querySelector('#video_item_object')){ var player = videojs("video_item_object",{"controls": true, "autoplay": false}); $('#modal_media_details').on('hidden.bs.modal',player.pause.bind(player)); return player; }
}());
