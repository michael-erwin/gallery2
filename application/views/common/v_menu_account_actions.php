<?php if(isset($_SESSION['user'])):
?><ul>
                        <li class="hasmenu">
                            <a>FAVORITES</a>
                            <div class="box-wrapper">
                                <ul class="submenu" id="menu_favorites">
                                    <li><a data-id="photos">Photos <span class="badge" data-id="fav_badge_photos">0</span></a></li>
                                    <li><a data-id="videos">Videos <span class="badge" data-id="fav_badge_videos">0</span></a></li>
                                </ul>
                            </div>
                        </li>
                        <li>&nbsp;|&nbsp;</li>
                        <li class="hasmenu">
                            <a><?php echo strtoupper($_SESSION['user']['first_name']);?></a>
                            <div class="box-wrapper">
                                <ul class="submenu" id="menu_favorites">
                                    <li><a href="#account/profile">Profile</a></li>
                                    <?php if(in_array('all', $_SESSION['permissions']) || in_array('admin_access', $_SESSION['permissions'])):
                                    ?><li><a href="<?php echo base_url('admin');?>">Admin</a></li><?php endif;?>
                                    <li><a href="<?php echo base_url('account/signout');?>">Sign Out</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
<?php else:
?><ul>
                        <li>
                            <a href="<?php echo base_url('account/signup');?>">SIGN UP</a>
                        </li>
                        <li>&nbsp;|&nbsp;</li>
                        <li>
                            <a href="<?php echo base_url('account/signin');?>">SIGN IN</a>
                        </li>
                    </ul>
<?php endif;?>