<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html;  charset=utf-8"/>
    <title>Mail</title>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
</head>
<body style=" margin:0; ">
<div style="width:900px;  background:#f6f6f6;  margin:0 auto;  padding-bottom: 75px; " id="wrapper">
    <div style="width:570px;  margin:0 auto; ">
        <div style="padding-top: 60px;  padding-bottom: 15px; " class="header-mail">
            <ul style="margin: 0; ">
                <?php if (!empty(logo()) && Storage::disk('SYSTEM_IMAGE_URL')->exists(logo())) { ?>
                    <li style="list-style:none;  float:left;  margin-left: -38px; ">
                        <a href="<?= URL::to('/'); ?>"><img
                                src="<?= Storage::disk('SYSTEM_IMAGE_URL')->url(logo()); ?>" alt="<?= (\App\Models\Setting::param ('site' , 'app_name')['value']); ?> "
                                alt=" <?= \App\Models\Setting::param('site', 'app_name')['value']; ?>"></a>
                    </li>
                <?php } ?>
                <li style="list-style:none;  float:right;  margin-right: 8px; ">
                    <a style="text-decoration:none;  font-family: 'Open Sans', sans-serif;  color:#498ea0; "
                       href="<?php echo URL::to('/'); ?>"><p>Login
                            to <?php echo \App\Models\Setting::param('site', 'app_name')['value']; ?></p></a>
                </li>
            </ul>
            <div style="clear:both"></div>
        </div>
        <!--header-mail ends here-->
        <div class="banner-mail"
             style="width:570px; height:235px; overflow:hidden;background:url('<?php echo URL::to('/'); ?>/assets/images/banner.jpg') 55%;">
            <div style="width:100%; min-height:235px; background:rgba(61,158,179,0.8);margin-top: 0px;">
                <h2 style="font-family: 'Open Sans', sans-serif; color:#fff;font-weight: 500;margin: 0; font-size:28px;text-align:center;line-height: 3; padding-top: 40px;text-transform: capitalize">
                    <?php echo \App\Models\Setting::param('site', 'app_name')['value']; ?></h2>

                <h2 style="font-family: 'Open Sans', sans-serif; color:#fff;font-weight: 100;margin: 0;font-size: 18px;text-align: center;">
                    Buy Medicines Online. Its easy as it's Name</h2>
            </div>
        </div>
        <!--banner-mail ends here-->
        <div style="background:#fff; padding-left: 30px; padding-right: 30px; padding-top: 50px; padding-bottom: 75px; "
             class="mail-content">
            <h2 style="font-family: 'Open Sans', sans-serif;  color:#272727; font-weight: 100; margin: 0;  font-size:14px; margin-bottom: 28px; ">
                Hi <span style="color:#404040; font-weight: bold; ">Admin</span></h2>

            <p style="font-family: 'Open Sans', sans-serif;  color:#272727; font-weight: 100; margin: 0;  font-size:14px; line-height: 1.6; margin-bottom: 25px; ">
                New prescription has been uploaded by <strong><?php echo $name; ?></strong> to <?php echo \App\Models\Setting::param('site', 'app_name')['value']; ?>.

			</p>

		</div>
        <!--mail-content-->
        <p style=" float:left; color:#8b8b8b; font-family: 'Open Sans', sans-serif; font-weight: 100; margin: 0px; font-size:11px; line-height: 1.6; margin-top: 20px; ">
            All rights reserved.&copy; 2011-2015 <a style="text-decoration:none"
                                                    href="<?php echo URL::to('/'); ?>"><?php echo \App\Models\Setting::param('site', 'website')['value']; ?></a>
        </p>

        <p style=" float:right; color:#8b8b8b; font-family: 'Open Sans', sans-serif; font-weight: 100; margin: 0px; font-size:11px; line-height: 1.6; margin-top: 20px; ">
            Unsubscribe</p>
    </div>
    <!--container ends here-->
</div>
<!--wrapper ends here-->
</body>
</html>
