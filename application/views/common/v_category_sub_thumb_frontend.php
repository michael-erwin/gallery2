<div class="thumb-box col-xs-6 col-sm-4 col-md-3 centered">
    <div class="category-thumb">
        <div class="subcat link-overlay">
            <a title="<?php echo @$title;?>" class="title ellipsis"><?php echo @$title;?></a>
            <div class="cat-cta">
            <?php if(isset($medias)):?>
                <?php if(count($medias) > 0):?>
                    <?php foreach($medias as $media):?>
                        <a title="<?php echo $media['title'];?>" class="ellipsis" href="<?php echo $media['link'];?>"><?php echo $media['title'];?></a>
                    <?php endforeach;?>
                <?php endif;?>
            <?php endif;?>
            </div>
            <div class="background-image" style="background-image:url('<?php echo @$icon;?>')"></div>
            <div class="background-color"></div>
        </div>
    </div>
</div>
