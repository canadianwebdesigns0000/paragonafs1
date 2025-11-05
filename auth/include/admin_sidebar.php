<li class="nav-item start <?php if((strpos($url, 'dashboard') != false)) echo 'active open'; ?>">
    <a href="dashboard.php" class="nav-link nav-toggle">
        <i class="icon-bar-chart"></i>
        <span class="title">Dashboard</span>
        <span class="selected"></span>
    </a>
</li>

<li class="nav-item <?php if((strpos($url, 'users') != false)) echo 'active';?> ">
    <a href="users.php" class="nav-link ">
        <i class="icon-users"></i>
        <span class="title">Users</span>
    </a>
</li>

<li class="nav-item <?php if((strpos($url, 'page_settings') != false)) echo 'active';?> ">
    <a href="page_settings.php" class="nav-link ">
        <i class="icon-settings"></i>
        <span class="title">Profile Setting </span>
    </a>
</li>