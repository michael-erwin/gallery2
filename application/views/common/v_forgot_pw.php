<div id="forgot_pw_box" class="portal-item form-box">
        <div class="form-box-head">
            <a href="<?php echo base_url();?>">Gallery</a>
        </div>
        <div class="form-box-body">
            <p class="form-box-msg">Enter your email to reset password</p>
            <form method="post" novalidate>
                <div class="form-group has-feedback">
                    <input type="email" name="email" class="form-control" placeholder="Email">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Send reset link</button>
                    </div>
                </div>
            </form>
            <a href="<?php echo base_url();?>account/signin">Sign in to my account</a><br>
            <a href="<?php echo base_url();?>account/signup">Sign up for new membership</a>
        </div>
    </div>
