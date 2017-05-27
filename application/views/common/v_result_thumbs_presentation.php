<div class="thumb-box centered col-md-3 col-sm-4 col-xs-6">
    <div class="thumb">
        <a title="<?php echo $title;?>" 
            class="presentation-link" 
            style="background-image:url('<?php echo base_url();?>media/presentation_items/256/<?php echo $uid;?>.jpg')" 
            href="<?php echo base_url();?>presentations/photo/full/<?php echo @$parent_id;?>_<?php echo @$uid; if(isset($share_id)) echo "?share_id={$share_id}"; ?>" 
            data-sub-html="<h3><?php echo $title;?></h3><p><?php echo $caption;?></p>">
        </a>
        <div class="title">
            <span><?php echo $title;?></span>
        </div>
    </div>
</div>
