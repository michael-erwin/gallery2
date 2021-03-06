<div class="row">
    <div class="col-xs-12 col-sm-8 media-block">
        <div class="media media-photo">
            <img src="<?php echo @$photo_url;?>">
            <div class="display-options">
                <span class="display-options-icon fullscreen overlay-ctrl-btn" title="Fullscreen"></span>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-4 info-block">
        <div class="info">
            <table class="table table-bordered">
                <thead>
                    <tr><th colspan="2">Details</th></tr>
                </thead>
                <tbody>
                    <tr><td>Title</td><td><?php echo @$title;?></td></tr>
                    <tr><td>Description</td><td><?php echo @$description;?></td></tr>
                    <tr><td>Dimension</td><td><?php echo @$dimension;?></td></tr>
                    <tr><td>File size</td><td><?php echo @$file_size;?></td></tr>
                </tbody>
            </table>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Tags</th></tr>
                </thead>
                <tbody>
                    <tr><td>
                    <?php foreach($tags as $tag):?>
                        <a class="label label-default" href="<?php echo base_url("search/photos?kw={$tag}");?>"><?php echo @$tag;?></a>
                    <?php endforeach;?>
                    </td></tr>
                </tbody>
            </table>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Downloads</th></tr>
                </thead>
                <tbody>
                    <tr><td style="text-align:center">
                        <button class="btn btn-success" data-mime="JPG" data-type="photos" data-title="<?php echo @$title;?>" data-uid="<?php echo @$uid;?>" onclick="media_file.download(this)">Full (JPG)</button>
                        <?php if($has_zip > 0):?>&nbsp;<button class="btn btn-danger" data-mime="ZIP" data-type="photos" data-title="<?php echo @$title;?>" data-uid="<?php echo @$uid;?>" onclick="media_file.download(this)">All (ZIP)</button><?php endif;?>
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
