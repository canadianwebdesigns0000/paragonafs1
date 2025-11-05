<?php 
$url=$_SERVER['PHP_SELF'];
error_reporting(0);
// SESSION CHECK SET OR NOT
if (!isset($_SESSION['admin'])) {
    header('location:index.php');
}
 
// QUERY TO GET USER DATA
// $adminSidebarData = $db->prepare("SELECT * FROM admin");
// $adminSidebarData->execute();
?>        
        
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="assets/images/paragon_logo_icon.png" alt="" height="40">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/paragon_logo.png" alt="" height="50">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="assets/images/paragon_logo_icon.png" alt="" height="30">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/paragon_logo.png" alt="" height="50">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">

                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link <?php if((strpos($url, 'dashboard') != false)) echo 'active';?>" href="/auth/admin/dashboard.php">
                                <i class="mdi mdi-speedometer"></i> <span data-key="t-dashboard">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link <?php if((strpos($url, 'clients') != false) || (strpos($url, 'view-tax-information') != false)) echo 'active';?>" href="/auth/admin/clients.php">
                                <i class="mdi mdi-account-group"></i> <span data-key="t-clients">Clients</span>
                            </a>
                        </li>

                    <?php if ($_SESSION['is_superadmin'] === 'yes') : ?>
                        <li class="menu-title"><span data-key="t-admin">Admin</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link <?php if((strpos($url, 'users') !== false)) echo 'active';?>" href="/auth/admin/users.php">
                                <i class="mdi mdi-account-circle-outline"></i> <span data-key="t-clients">Users</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link <?php if((strpos($url, 'logs') != false)) echo 'active';?>" href="/auth/admin/logs.php">
                                <i class="mdi mdi-notebook-outline"></i> <span data-key="t-logs">Logs</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>