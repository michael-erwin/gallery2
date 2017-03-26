<div id="signup_box" class="portal-item register-box">
    <div class="form-box-head">
        <a href="<?php echo base_url();?>">Gallery</a>
    </div>
        <div class="form-box-body">
            <p class="form-box-msg">Sign up for new membership</p>
            <form method="post" novalidate>
                <div class="form-group has-feedback">
                    <input type="text" name="fname" class="form-control" placeholder="First name">
                    <span class="fa fa-user-circle form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" name="lname" class="form-control" placeholder="Last name">
                    <span class="fa fa-user-circle-o form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="email" name="email" class="form-control" placeholder="Email">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="agree">&nbsp;&nbsp;I agree to the <a href="#" target="_blank">terms</a>
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign Up</button>
                    </div>
                </div>
            </form>
            <a href="<?php echo base_url();?>account/signin" class="text-center">I'm already a member</a>
        </div>
    </div>