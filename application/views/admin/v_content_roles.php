<div class="content-toolbar content-box clearfix">
    <div class="control-left">
        <button class="btn btn-control btn-sm" data-id="new_button">New</button>
    </div>
    <div class="control-right">
        <div class="toolbar-search">
            <form data-id="search_form">
                <input type="text" class="form-control input-sm" disabled="true" placeholder="Search..." data-id="search_box" />
                <button data-id="search_btn">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<div id="role_table" class="table_block">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Permissions</th>
                <th>Options</th>
            </tr>
        </thead>
        <tbody data-id="list">
            
        </tbody>
    </table>
</div>
<div id="message_block" class="row" style="display:none">
    <div class="col-xs-12"><div class="alert alert-danger">No results found.</div></div>
</div>