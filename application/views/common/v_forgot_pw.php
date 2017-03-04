<div class="login-box">
        <div class="login-logo">
            <a href="<?php echo base_url();?>"><b>Gallery</b></a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Enter your email to reset password</p>
            <form method="post">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="Email">
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
