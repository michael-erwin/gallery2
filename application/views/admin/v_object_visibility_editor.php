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
                                <label><input type="radio" name="visibility" value="public" data-for="public-content" />&nbsp;Public</label>
                            </div>
                            <div class="col-xs-4">
                                <label><input type="radio" name="visibility" value="protected" data-for="protected-content" />&nbsp;Protected</label>
                            </div>
                            <div class="col-xs-4">
                                <label><input type="radio" name="visibility" value="private" data-for="private-content" />&nbsp;Private</label>
                            </div>
                        </div>
                        <div class="form-group clearfix row content-block protected-content">
                            <div class="col-xs-12">
                                <div class="search-box">
                                    <input type="text" role="search-box" class="form-control" placeholder="Search email to include" />
                                    <ul role="hint-box" class="hint-box">
                                        <li>No results.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="form-group clearfix row content-block protected-content">
                            <div class="col-xs-12">
                                Who can see this content.
                            </div>
                        </div>
                        <div class="form-group clearfix row content-block protected-content">
                            <div class="col-xs-12">
                                <ul class="input-tags">

                                </ul>
                            </div>
                        </div>
                        <div class="form-group clearfix row content-block private-content">
                            <div class="col-xs-12">
                                Share using special link?
                            </div>
                        </div>
                        <div class="form-group clearfix row content-block private-content">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <input type="text" class="form-control input-sm" name="special_link" role="special_link_box" placeholder="URL" readonly>
                                    <div class="input-group-addon input-sm">
                                        <label>
                                            <input type="checkbox" role="special_link_toggle" title="Click to enabled special link.">
                                            Yes
                                        </label>
                                    </div>
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
