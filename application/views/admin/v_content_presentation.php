<div class="content-toolbar content-box clearfix">
    <div class="control-left">
        <button class="btn btn-control btn-sm" data-id="new_button">New</button>
    </div>
    <div class="control-right">
        <div class="toolbar-search">
            <form data-id="search_form">
                <input type="text" class="form-control input-sm" disabled="true" placeholder="Search..." data-id="search_box" />
                <button data-id="search_btn" disabled="true">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<div class="content-block">
    <table id="presentation_table" class="table table-bordered">
        <thead>
            <tr>
                <th colspan="2">Title</th>
                <th>Description</th>
                <th>Visibility</th>
                <th>Items</th>
                <th>Options</th>
            </tr>
        </thead>
        <tbody data-id="list">
        </tbody>
    </table>
</div>
<div id="message_block" class="row" style="display:none">
    <div class="col-xs-12"><div class="alert alert-danger">You don't have enough permission to view this content.</div></div>
</div>
