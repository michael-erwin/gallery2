<div class="content-toolbar content-box clearfix">
    <div class="control-left">
        <button class="btn btn-control btn-sm" data-id="new_button">New</button>
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
<div class="content-block clearfix">
    <div class="float-left">
        <div data-id="quick_buttons">
            <button class="btn btn-default btn-sm display active" title="Display 15 items" data-display="15">
                15
            </button>
            <button class="btn btn-default btn-sm display" title="Display 30 items" data-display="30">
                30
            </button>
            <button class="btn btn-default btn-sm display" title="Display 50 items" data-display="50">
                50
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
<div id="users_table">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Role</th>
                <th>Status</th>
                <th>Options</th>
            </tr>
        </thead>
        <tbody data-id="list">
            <tr>
                <td colspan="5">No content.</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="content-block clearfix">
    <div class="float-left"></div>
    <div class="m-pagination float-right">
        <button class="btn btn-default btn-sm prev"><i class="glyphicon glyphicon-chevron-left"></i></button>
        <input type="text" class="form-control input-sm index" />
        <span class="of-pages">&nbsp;of <span class="total">5</span>&nbsp;</span>
        <button class="btn btn-default btn-sm next"><i class="glyphicon glyphicon-chevron-right"></i></button>
    </div>
</div>
