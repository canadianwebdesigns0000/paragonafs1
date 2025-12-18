<?php
    session_start();
    include '../config.php';


// SESSION CHECK SET OR NOT
if (!isset($_SESSION['admin'])) {
    header('location:index.php');
}

    // SELECT CURRENT LOGGED IN ADMIN DETAILS MATCH FROM THE DATABASE
    $statement = $db->prepare('SELECT * FROM `admin` where username=?');
    $statement->execute(array($_SESSION['username']));
    $userData = $statement->fetch(PDO::FETCH_ASSOC);
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

    <?php include '../include/css/mandatory.php' ?>
    <link href="../assets/plugins/froiden-helper/helper.css" rel="stylesheet" type="text/css" />
    <?php include '../include/css/global.php' ?>
</head>

<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo page-md">
<?php include '../include/header.php'; ?>
<div class="page-container">
    <?php include '../include/sidebar.php'; ?>
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-green">
                        <i class="icon-settings font-green"></i>
                        <span class="caption-subject bold uppercase">Profile Settings</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                    <div class="tab-pane active" id="tab_1_3">
                        <div class="row profile-account">
                            <div class="col-md-3">
                                <ul class="ver-inline-menu tabbable margin-bottom-10">
                                    <li class="active">
                                        <a data-toggle="tab" href="#tab_1-1">
                                            <i class="fa fa-cog"></i> Personal info </a>
                                        <span class="after"> </span>
                                    </li>
                                    <li>
                                        <a data-toggle="tab" href="#tab_3-3">
                                            <i class="fa fa-lock"></i> Change Password </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-9">
                                <div class="tab-content">
                                    <div id="tab_1-1" class="tab-pane active">
                                        <form method="POST" id="editProfile">
                                            <input type="hidden" name="id" value="<?php echo $userData['id'] ?>" id="id">
                                            <div class="form-group form-md-line-input ">
                                                <input type="text" placeholder="Name" name="name" class="form-control" value="<?php echo $userData['name'] ?>" id="name">
                                                <label class="control-label">Name</label>
                                                <span class="form-control-focus"> </span>
                                            </div>
                                            <div class="form-group form-md-line-input">
                                                <input type="text" class="form-control placeholder-no-fix" name="username" value="<?php echo $userData['username'] ?>" placeholder="Enter user name" id="username">
                                                <label class="control-label">Username</label>
                                                <span class="form-control-focus"> </span>
                                            </div>
                                            <div class="form-group form-md-line-input">
                                                <input type="email" class="form-control placeholder-no-fix" name="email" value="<?php echo $userData['email'] ?>" placeholder="Enter Email Address" id="email">
                                                <label class="control-label">Email</label>
                                                <span class="form-control-focus"> </span>
                                            </div>
                                            <div class="margiv-top-10">
                                                <button type="submit" class="btn green btn-outline btn-sm" onclick="editProfile();return false"> Save Changes </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="tab_3-3" class="tab-pane">
                                        <form method="POST" accept-charset="UTF-8" id="passwordEdit">
                                            <div class="form-group form-md-line-input form-md-floating-label">
                                                <input type="password" class="form-control input-sm" name="password" id="password">
                                                <input type="hidden" name="id" value="<?php echo $userData['id'] ?>" id="id">
                                                <label class="control-label">New Password</label>
                                                <span class="form-control-focus"> </span>
                                            </div>
                                            <div class="form-group form-md-line-input form-md-floating-label">
                                                <input type="password" class="form-control input-sm" name="cpassword" id="cpassword">
                                                <label class="control-label">Confirm Password</label>
                                                <span class="form-control-focus"> </span>
                                            </div>
                                            <div class="margin-top-10">
                                                <button type="submit" class="btn green btn-outline btn-sm" onclick="editPassword();return false"> Change Password </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--end col-md-9-->
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

            </div>
        </div>
    </div>
</div>
<?php include '../include/footer.php' ?>
<?php include '../include/footerjs.php' ?>
<script src="../assets/plugins/froiden-helper/helper.js" type="text/javascript"></script>
<script>

    function editPassword(){
        // Send ajax Request to ajax/change_password_admin.php to change password
        $.easyAjax({
            url: "ajax/change_password_admin.php",
            type: "POST",
            data: $("#passwordEdit").serialize(),
            container: "#passwordEdit"
        });
    }

    function editProfile(){
        // Send ajax Request to ajax/login.php  to verify the credentials
        $.easyAjax({
            url: "ajax/admin_profile.php",
            type: "POST",
            data: $("#editProfile").serialize(),
            container: "#editProfile"
        });
    }
</script>
</body>
</html>