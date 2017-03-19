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
$data['backdrop_photo'] = base_url($folder.$photos[$i]);
?><script>
    console.log(<?php echo $i;?>);
</script>