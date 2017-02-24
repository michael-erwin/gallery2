<div class="modal common" id="modal_user_editor" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form data-id="editor_form" novalidate>
                <input type="hidden" name="id" />
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">User (<span></span>)</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group clearfix row">
                            <div class="col-sm-3">
                                First Name
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="fname" placeholder="John" />
                            </div>
                        </div>
                        <div class="form-group clearfix row">
                            <div class="col-sm-3">
                                Last Name
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="lname" placeholder="Doe" />
                            </div>
                        </div>
                        <div class="form-group clearfix row">
                            <div class="col-sm-3">
                                Email
                            </div>
                            <div class="col-sm-9">
                                <input type="email" class="form-control input-sm" name="email" placeholder="john.doe@domain.com" />
                            </div>
                        </div>
                        <div class="form-group clearfix row">
                            <div class="col-sm-3">
                                Password
                            </div>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="password" class="form-control input-sm" name="password" placeholder="At least 6 characters." disabled>
                                    <div class="input-group-addon input-sm">
                                        <input type="checkbox" data-id="password_toggle" title="Click to set password.">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group clearfix row">
                            <div class="col-sm-3">
                                Role
                            </div>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" name="role">
                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group clearfix row" style="margin-bottom: 5px">
                            <div class="col-sm-3">
                                Status
                            </div>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-xs-6"><label><input type="radio" name="status" value="active">&nbsp;Active</label></div>
                                    <div class="col-xs-6"><label><input type="radio" name="status" value="inactive" checked>&nbsp;Inactive</label></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group clearfix row" style="margin-bottom: 0px">
                            <div class="col-sm-3">
                                &nbsp;
                            </div>
                            <div class="col-sm-9">
                                <label>
                                    <input type="checkbox" name="verify_email" value="1">&nbsp;
                                    Send email verification &amp; password reset.
                                </label>
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
