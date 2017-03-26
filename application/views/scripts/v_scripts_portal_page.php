<?php
// Get backdrop for CTA block.
$folder = 'assets/img/wallpapers/';
$files = scandir($folder);
$photos = [];
foreach($files as $file)
{
    if(is_file($folder.$file))
    {
        $photos[] = $file;
    }
}
$i = rand(0,count($photos)-1);
$backdrop_photo = base_url($folder.$photos[$i]);
?><script>
    ;(function($){
        //$('#page_background').
        var src = '<?php echo $backdrop_photo;?>';
        $img = $('<img src="'+src+'">');
        $img.on('load',function(){
            console.log('Image loaded.');
            $('#page_background').css({
                'background-image': 'url("'+src+'")',
                'display': 'block'
            });
        });
        $img.appendTo('#page_background');
    }(jQuery))
</script>
