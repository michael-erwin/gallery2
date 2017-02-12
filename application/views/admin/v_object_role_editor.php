<div class="modal common" id="modal_role_editor" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form data-id="editor_form">
                <input type="hidden" name="id" />
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Role (<span></span>)</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group clearfix row">
                            <div class="col-sm-3">
                                Name
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="name" />
                            </div>
                        </div>
                        <div class="form-group clearfix row">
                            <div class="col-sm-3">
                                Description
                            </div>
                            <div class="col-sm-9">
                                <textarea class="form-control input-sm" name="description"></textarea>
                            </div>
                        </div>
                        <div class="form-group clearfix row">
                            <div class="col-sm-3">
                                Permissions
                            </div>
                            <div class="col-sm-9">
                                <div class="form-control" data-id="list_container">
                                    <table data-id="list" class="table table-bordered">
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="bottom-control">
                                    <label data-id="toggle_select_all">
                                        <input type="checkbox">&nbsp;Check/uncheck all
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" data-id="save_btn">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
