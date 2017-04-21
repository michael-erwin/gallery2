<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo @$title;?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/libs/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/libs/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/toastr/toastr.min.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/theme.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/iCheck/square/blue.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/portal.min.css">
</head>
<body class="hold-transition login-page">
    <div id="page_background"></div>
    <?php echo @$main_form;?>
    
    <script src="<?php echo base_url();?>assets/libs/jquery/jquery-2.2.4.min.js"></script>
    <script src="<?php echo base_url();?>assets/libs/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugins/iCheck/icheck.min.js"></script>
    <script>
        var site = {base_url:"<?php echo base_url();?>"};
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
<?php if(isset($_GET['auth_error'])):?>
        toastr['warning']("<?php echo htmlentities($_GET['auth_error']);?>","Warning");
<?php endif;?>
<?php if(isset($_GET['auth_notice'])):?>
        toastr['info']("<?php echo htmlentities($_GET['auth_notice']);?>","Notice");
<?php endif;?>
    </script>
    <script src="<?php echo base_url();?>assets/js/portal.js"></script>
    <?php echo @$js_script;?>
</body>
</html>