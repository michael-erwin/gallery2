<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo @$title;?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/libs/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/libs/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/theme.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/iCheck/square/blue.css">
</head>
<body class="hold-transition login-page">
    <?php echo @$main_form;?>

    <!-- jQuery 2.2.4 -->
    <script src="<?php echo base_url();?>assets/libs/jquery/jquery-2.2.4.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="<?php echo base_url();?>assets/libs/bootstrap/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="<?php echo base_url();?>assets/plugins/iCheck/icheck.min.js"></script>
    <script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
    <?php echo @$js_script;?>
    </script>
</body>
</html>