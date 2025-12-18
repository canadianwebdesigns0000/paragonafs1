
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner ">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="
            <?php if($_SESSION['name'] && isset($_SESSION['table']) && $_SESSION['table']== 'admin') {?>

                    index.php

                <?php } else { ?>

                    dashboard.php

                <?php } ?>
            ">
                <span class="logo-default">
                    <?php if($_SESSION['name'] && isset($_SESSION['table']) && $_SESSION['table']== 'admin') {?>

                        Admin

                    <?php } else { ?>

                        User

                    <?php } ?>
                    Panel
                </span>

            </a>
            <div class="menu-toggler sidebar-toggler">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
        <!-- END RESPONSIVE MENU TOGGLER -->

        <!-- BEGIN PAGE TOP -->
        <div class="page-top">
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <li class="separator hide"> </li>
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-user dropdown-dark">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <span class="username username-show-on-mobile"> <?php echo $_SESSION['name']; ?>  </span>
                            <i class="fa fa-angle-down"></i>
                            <!-- DOC: Do not remove below empty space(&nbsp;) as its purposely used -->
                             </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="<?php echo ($_SESSION['table'] == 'admin') ? '../admin/page_settings.php' : '../user/user_profile.php'; ?>">
                                    <i class="icon-user"></i> My Profile </a>
                            </li>
                            <li class="divider"> </li>

                            <li>
                                <a href="../logout.php">
                                    <i class="icon-key"></i> Log Out </a>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END PAGE TOP -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"> </div>