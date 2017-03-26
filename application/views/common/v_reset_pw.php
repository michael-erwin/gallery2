<div id="reset_pw_box" class="portal-item form-box">
        <div class="form-box-head">
            <a href="<?php echo base_url();?>">Gallery</a>
        </div>
        <div class="form-box-body">
            <p class="form-box-msg">Reset your password</p>
            <form method="post" novalidate>
                <input type="hidden" name="email" value="<?php echo $_GET['email'];?>">
                <input type="hidden" name="token" value="<?php echo $_GET['token'];?>">
                <div class="form-group has-feedback">
                    <input type="password" name="npassword" class="form-control" placeholder="New password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="rpassword" class="form-control" placeholder="Retype password">
                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
