<?php
session_start();
include '../config.php';

//     error_reporting(0);
// SESSION CHECK SET OR NOT
if (!isset($_SESSION['user'])) {
    header('location:../index.php');
}

// Query To Get User Data
$userData = $db->prepare('SELECT * FROM users WHERE email=?');
$userData->execute(array($_SESSION['email']));
$rowUser = $userData->fetch(PDO::FETCH_ASSOC);

$image = file_exists('../'.$path.$rowUser['avatar']);

?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
    <title>Profile - User</title>
    <?php include '../include/css/mandatory.php' ?>
    <link href="../assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
    <?php include '../include/css/global.php' ?>
    <link href="../assets/plugins/froiden-helper/helper.css" rel="stylesheet" type="text/css" />

</head>
<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo page-md">
<?php include '../include/header.php'; ?>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <?php include '../include/sidebar.php'; ?>
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <!-- BEGIN PAGE BASE CONTENT -->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PROFILE SIDEBAR -->
                    <div class="profile-sidebar">
                        <!-- PORTLET MAIN -->
                        <div class="portlet light profile-sidebar-portlet bordered">
                            <!-- SIDEBAR USERPIC -->
                            <div class="profile-userpic">
                                <img src="../<?php echo ($image == 1 && $rowUser['avatar'] != null) ? $path.$rowUser['avatar'] : $path.'no-image.png' ?>" class="img-responsive" alt=""> </div>
                            <!-- END SIDEBAR USERPIC -->
                            <!-- SIDEBAR USER TITLE -->
                            <div class="profile-usertitle margin-bottom-20">
                                <div class="profile-usertitle-name"> <?php echo $rowUser['name'] ?> </div>
                            </div>
                            <!-- END SIDEBAR USER TITLE -->
                        </div>
                    </div>
                    <!-- END BEGIN PROFILE SIDEBAR -->
                    <!-- BEGIN PROFILE CONTENT -->
                    <div class="profile-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet light bordered">
                                    <div class="portlet-title tabbable-line">
                                        <div class="caption caption-md">
                                            <i class="icon-globe font-green hide"></i>
                                            <span class="caption-subject font-green bold uppercase">Account Details</span>
                                        </div>
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#tab_1_1" data-toggle="tab">Personal Info</a>
                                            </li>
                                            <li>
                                                <a href="#tab_1_2" data-toggle="tab">Change Avatar</a>
                                            </li>
                                            <li>
                                                <a href="#tab_1_3" data-toggle="tab">Change Password</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tab-content">
                                            <!-- PERSONAL INFO TAB -->
                                            <div class="tab-pane active" id="tab_1_1">
                                                <form role="form" method="post" action="#" id="updateUser">
                                                    <div class="form-group form-md-line-input">
                                                        <input type="text" class="form-control" readonly value="<?php echo $rowUser['username'] ?> " name="username" id="username" />
                                                        <input type="hidden" name="id" value="<?php echo $rowUser['id'] ?>"/>
                                                        <label class="control-label">Username</label>
                                                        <span class=" help-block help-block-error font-green">You cannot change username.</span>

                                                    </div>
                                                    <div class="form-group form-md-line-input">
                                                        <input type="text" class="form-control" value="<?php echo $rowUser['name'] ?> " name="name" id="name"/>
                                                        <label class="control-label">Name</label>
                                                        <span class="form-control-focus"> </span>
                                                    </div>
                                                    <div class="form-group form-md-line-input">
                                                        <input type="text" class="form-control" value="<?php echo $rowUser['email'] ?> " name="email" id="email"/>
                                                        <label class="control-label">Email</label>
                                                        <span class="form-control-focus"> </span>
                                                    </div>
                                                    <div class="margiv-top-10">
                                                        <button type="submit" class="btn green btn-outline" onclick="editProfile(); return false;"> Save Changes </button>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- END PERSONAL INFO TAB -->
                                            <!-- CHANGE AVATAR TAB -->
                                            <div class="tab-pane" id="tab_1_2">
                                                <form action="#" role="form" id="updateImage" enctype="multipart/form-data">
                                                    <div class="form-group form-md-line-input">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                <img src="../<?php echo ($image == 1 && $rowUser['avatar'] != null) ? $path.$rowUser['avatar'] : 'http://www.placehold.it/200x150/EFEFEF/AAAAAA' ?>" alt="" /> </div>
                                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                                                            <div>
                                                                        <span class="btn btn-outline green btn-file">
                                                                            <span class="fileinput-new"> Select image </span>
                                                                            <span class="fileinput-exists"> Change </span>
                                                                            <input type="file" name="image"> </span>
                                                                <a href="javascript:;" class="btn default fileinput-exists btn-outline" data-dismiss="fileinput"> Remove </a>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="id" value="<?php echo $rowUser['id'] ?>"/>
                                                        <div class="clearfix margin-top-10">
                                                            <span class="label label-danger">NOTE!</span> Image preview only works in IE10+, FF3.6+, Safari6.0+, Chrome6.0+ and Opera11.1+. In older browsers the filename is shown instead. </div>

                                                    </div>
                                                    <div class="margin-top-10">
                                                        <button type="submit" class="btn green btn-outline" onclick="updateImage();return false"> Submit </button>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- END CHANGE AVATAR TAB -->
                                            <!-- CHANGE PASSWORD TAB -->
                                            <div class="tab-pane" id="tab_1_3">
                                                <form action="#" method="post" id="changePassword">
                                                    <div class="form-group form-md-line-input form-md-floating-label">
                                                        <input type="hidden" name="id" value="<?php echo $rowUser['id'] ?>"/>
                                                        <input type="password" class="form-control input-sm" name="password" id="password"/>
                                                        <label class="control-label">New Password</label>
                                                        <span class="form-control-focus"> </span>
                                                    </div>
                                                    <div class="form-group form-md-line-input form-md-floating-label">
                                                        <input type="password" class="form-control input-sm" name="cpassword" id="cpassword"/>
                                                        <label class="control-label">Re-type New Password</label>
                                                        <span class="form-control-focus"> </span>
                                                    </div>
                                                    <div class="margin-top-10">
                                                        <button type="submit" class="btn green btn-outline" onclick="changePassword();return false;"> Change Password </button>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- END CHANGE PASSWORD TAB -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END PROFILE CONTENT -->
                </div>
            </div>
            <!-- END PAGE BASE CONTENT -->
        </div>
        <!-- END CONTENT BODY -->
    </div>
    <!-- END CONTENT -->

</div>

<!--End page-container-->
<?php include '../include/footer.php' ?>
<?php include '../include/footerjs.php' ?>
<script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="../assets/plugins/froiden-helper/helper.js" type="text/javascript"></script>


<script>
    // Edit profile
    function editProfile() {
        $.easyAjax({
            type: "POST",
            url: "../ajax/user_profile.php",
            container: "#updateUser",
            data: $("#updateUser").serialize()
        });
    }

    // Update profile image
    function updateImage() {
        $.easyAjax({
            type: "POST",
            url: "../ajax/update_profile_image.php",
            container: "#updateImage",
            file:true,
            success:function (response) {
//                console.log(response.imageName);
                $('.img-responsive').attr('src',"../<?php echo $path?>"+response.imageName);
                console.log("../<?php echo $path?>"+response.imageName);
            }
        });
    }

    // Change password
    function changePassword() {
        $.easyAjax({
            type: "POST",
            url: "../ajax/profile_change_password.php",
            container: "#changePassword",
            data: $("#changePassword").serialize()
        });
    }

</script>

</body>
</html>
