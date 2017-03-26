<div class="modal common" id="modal_visibility_editor" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form data-id="editor_form">
                <input type="hidden" name="id" />
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Visibility</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group clearfix row">
                            <div class="col-xs-4">
                                <label><input type="radio" name="visibility" value="public" />&nbsp;Public</label>
                            </div>
                            <div class="col-xs-4">
                                <label><input type="radio" name="visibility" value="protected" />&nbsp;Protected</label>
                            </div>
                            <div class="col-xs-4">
                                <label><input type="radio" name="visibility" value="private" />&nbsp;Private</label>
                            </div>
                        </div>
                        <div class="form-group clearfix row protected-content">
                            <div class="col-xs-12">
                                <input type="text" class="form-control" placeholder="Search email" />
                            </div>
                        </div>
                        <div class="form-group clearfix row protected-content">
                            <div class="col-xs-12">
                                <ul class="input-tags">
                                    <li>sample@email.com</li>
                                </ul>
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
