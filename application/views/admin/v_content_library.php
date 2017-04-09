<div class="content-toolbar content-box clearfix">
        <div class="control-left">
            <select class="form-control input-sm" data-id="media_type">
                <option value="photos" selected>Photos</option>
                <option value="videos">Videos</option>
            </select>
            <select class="form-control input-sm" data-id="category">
                <option value="0" selected>Any category</option>
            </select>
            <select class="form-control input-sm" data-id="visibility">
                <option value="any" selected>Any visibility</option>
                <option value="public">Public</option>
                <option value="protected">Protected</option>
                <option value="private">Private</option>
            </select>
            <select class="form-control input-sm" data-id="bulk_operation">
                <option value="default" selected="true">Bulk Operation</option>
                <option value="change_category">Change Category</option>
                <option value="change_visibility">Change Visibility</option>
                <option value="delete">Delete</option>
            </select>
        </div>
        <div class="control-right">
            <div class="toolbar-search">
                <form data-id="search_form">
                    <input type="text" class="form-control input-sm" placeholder="Search..." data-id="search_box" />
                    <button data-id="search_btn">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
</div>
<div class="content-block quickbar clearfix">
    <div class="float-left">
        <div data-id="quick_buttons">
            <button class="btn btn-default btn-sm toggle-select" title="Select or deselect all">
                <i class="fa fa-check"></i>
            </button>
            <button class="btn btn-default btn-sm display active" title="Thumbnail view" data-display="thumb">
                <i class="fa fa-th-large"></i>
            </button>
            <button class="btn btn-default btn-sm display" title="List view" data-display="list">
                <i class="fa fa-th-list"></i>
            </button>
        </div>
    </div>
    <div class="m-pagination float-right">
        <button class="btn btn-default btn-sm prev"><i class="glyphicon glyphicon-chevron-left"></i></button>
        <input type="text" class="form-control input-sm index" />
        <span class="of-pages">&nbsp;of <span class="total">5</span>&nbsp;</span>
        <button class="btn btn-default btn-sm next"><i class="glyphicon glyphicon-chevron-right"></i></button>
    </div>
</div>
<div class="content-block row" data-id="content">
    <!-- Thumbnails will load here. -->
</div>
<div id="message_block" class="row" style="display:none">
    <div class="col-xs-12"><div class="alert alert-danger">No results found.</div></div>
</div>
<div class="content-block quickbar clearfix">
    <div class="m-pagination float-right">
        <button class="btn btn-default btn-sm prev"><i class="glyphicon glyphicon-chevron-left"></i></button>
        <input type="text" class="form-control input-sm index" />
        <span class="of-pages">&nbsp;of <span class="total">5</span>&nbsp;</span>
        <button class="btn btn-default btn-sm next"><i class="glyphicon glyphicon-chevron-right"></i></button>
    </div>
</div>
