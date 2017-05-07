<div class="user-panel">
                    <div class="pull-left image">
                        <!-- <img alt="User Image" class="img-circle" src="<?php echo base_url();?>assets/img/user.jpg"> -->
                        <i class="fa fa-user-circle sidebar-user-icon" aria-hidden="true"></i>
                    </div>
                    <div class="pull-left info" style="margin-left: 8px">
                        <p><?php echo @$_SESSION['user']['first_name'];?> <?php echo @$_SESSION['user']['last_name'];?></p>
                        <!-- Status -->
                        <a href="#"><i class="fa fa-circle bullet-admin"></i> <?php echo @$_SESSION['user']['role_name'];?></a>
                    </div>
                </div>
