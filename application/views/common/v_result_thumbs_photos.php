<div class="thumb-box centered col-md-3 col-sm-4 col-xs-6">
    <div class="thumb" data-data='<?php echo preg_replace("/'/", '', $data);?>' data-media="photo">
        <a title="<?php echo $title;?>" class="image-link photo-preview" href="<?php echo base_url();?>photos/item/<?php echo $seo_title;?>" style="background-image:url('<?php echo base_url();?>media/photos/public/256/<?php echo $uid;?>.jpg')">
            <img src="<?php echo base_url();?>media/photos/public/128/<?php echo $uid;?>.jpg" />
        </a>
        <div class="title">
            <span><?php echo $title;?></span>
        </div>
        <div class="options">
            <b title="Download" data-id="download"><i class="fa fa-lg fa-download"></i></b>
            <b title="Add to favorites" data-id="favorites"><i class="fa fa-lg fa-star"></i></b>
        </div>
    </div>
</div>
