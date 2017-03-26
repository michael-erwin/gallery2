<div id="signin_box" class="portal-item form-box">
        <div class="form-box-head">
            <a href="<?php echo base_url();?>">Gallery</a>
        </div>
        <div class="form-box-body">
            <p class="form-box-msg">Sign in to start your session</p>
            <form method="post" novalidate>
                <input type="hidden" name="redir" value="<?php echo @$_GET['redir'];?>">
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
                                <input type="checkbox" name="remember" value="1">&nbsp;&nbsp;Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                </div>
            </form>
            <a href="<?php echo base_url();?>account/forgot">I forgot my password</a><br>
            <a href="<?php echo base_url();?>account/signup" class="text-center">Sign up for new membership</a>
        </div>
    </div>
