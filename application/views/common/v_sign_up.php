<div class="register-box">
    <div class="register-logo">
        <a href="<?php echo base_url();?>"><b>Gallery</b></a>
    </div>
        <div class="register-box-body">
            <p class="login-box-msg">Sign up for new membership</p>
            <form method="post">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="fname" placeholder="First name">
                    <span class="fa fa-user-circle form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="lname" placeholder="Last name">
                    <span class="fa fa-user-circle-o form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" name="email" placeholder="Email">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox">&nbsp;&nbsp;I agree to the <a href="#">terms</a>
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign Up</button>
                    </div>
                </div>
            </form>
            <a href="login.html" class="text-center">I'm already a member</a>
        </div>
    </div>