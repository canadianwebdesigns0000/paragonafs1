<li class="nav-item start <?php if((strpos($url, 'dashboard') != false)) echo 'active'; ?>">
    <a href="../user/dashboard.php" class="nav-link nav-toggle">
        <i class="icon-bar-chart"></i>
        <span class="title">Dashboard</span>
        <span class="selected"></span>
    </a>
</li>

<li class="nav-item <?php if((strpos($url, 'user_profile') != false)) echo 'active';?> ">
    <a href="../user/user_profile.php" class="nav-link ">
        <i class="icon-user"></i>
        <span class="title">Profile</span>
    </a>
</li>