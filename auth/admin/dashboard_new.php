<?php
    session_start();
    include "../config.php";

    // error_reporting(0);

    // SESSION CHECK SET OR NOT
    if (!isset($_SESSION['admin'])) {
        header('location:index.php');
    }

    $sql    = "SELECT count(*) FROM `users` where status = ?";
    $result = $db->prepare($sql);

    // All users count
    $sql    = "SELECT count(*) FROM `users`";
    $result = $db->prepare($sql);
    $result->execute();
    $totalUsers = $result->fetchColumn(); 

    // All Files Submitted
    $files_tax_sql = "SELECT count(*) FROM `tax_information` where is_file_submit = ?";
    $files_tax_result = $db->prepare($files_tax_sql);
    $files_tax_result->execute(array('Yes'));
    $fileSubmittedUsers = $files_tax_result->fetchColumn();

    // All Files not Submitted
    $tax_sql = "SELECT count(*) FROM `tax_information` where is_file_submit = ?";
    $tax_result = $db->prepare($tax_sql);
    $tax_result->execute(array('No'));
    $fileNotSubmittedUsers = $tax_result->fetchColumn();

    // All Files not Submitted
    $tax_sql2 = "SELECT COUNT(*) 
                FROM users 
                LEFT JOIN tax_information ON users.email = tax_information.email 
                WHERE tax_information.is_file_submit IS NULL";
    $tax_result2 = $db->prepare($tax_sql2);
    $tax_result2->execute();
    $fileNotSubmittedUsers2 = $tax_result2->fetchColumn();


    // All Verified Users
    $email_verified_sql = "SELECT count(*) FROM `users` where email_verified = ?";
    $email_verified_result = $db->prepare($email_verified_sql);
    $email_verified_result->execute(array('no'));
    $emailNotVerifiedUsers = $email_verified_result->fetchColumn();


    // Get last 5 sign ups
    $userData = $db->prepare('SELECT * FROM users ORDER BY created_at desc LIMIT 0, 7');
    $userData->execute();


    // Function to Encrypt Decrypt String
    function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        // Update your secret key before use
        $secret_key = '$7PHKqGt$yRlPjyt89rds4ioSDsglpk/';
        // Update your secret iv before use
        $secret_iv = '$QG8$hj7TRE2allPHPlBbrthUtoiu23bKJYi/';
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">
<head>
    <meta charset="utf-8" />
    <title>Paragon AFS | Admin Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="Paragon AFS" name="author" />

    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />
    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?php include '../inc/css/global.php' ?>
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include '../inc/header.php'; ?>

        <!-- ========== App Menu ========== -->
        <?php include '../inc/sidebar.php'; ?>
        <!-- Left Sidebar End -->

        
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col">

                            <div class="h-100">
                                <div class="row mb-3 pb-1">
                                    <div class="col-12">
                                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                            <div class="flex-grow-1">
                                                <h4 class="fs-16 mb-1">Good Morning, Admin!</h4>
                                                <p class="text-muted mb-0"><?= array_rand(array_flip(["The journey of a thousand miles begins with one step. - Lao Tzu","Fortune favors the bold. - Virgil","Life is what happens when you’re busy making other plans. - John Lennon","When the going gets tough, the tough get going. - Joe Kennedy"]), 1); ?></p>
                                            </div>
                                            <div class="mt-3 mt-lg-0">
                                                <p id="refreshTimer" style="display: inline-block;margin-right: 10px;"></p>
                                                <button type="button" class="btn btn-soft-success" id="users_dashboard_table_refresh">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                            </div>
                                        </div><!-- end card header -->
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                                <div class="row">
                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Total Users</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <!-- <h5 class="text-success fs-14 mb-0">
                                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +16.24 %
                                                        </h5> -->
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="<?= $totalUsers; ?>">0</span></h4>
                                                        <a href="/auth/admin/clients.php" class="text-decoration-underline">View All Users</a>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-success rounded fs-3">
                                                            <i class="bx bx-user-circle"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Email Verification Pending</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <!-- <h5 class="text-danger fs-14 mb-0">
                                                            <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -3.57 %
                                                        </h5> -->
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="<?= $emailNotVerifiedUsers ?>">0</span></h4>
                                                        <form action="/auth/admin/clients.php" method="POST">
                                                            <div class="banner_one_search_form_input_box">
                                                                <input type="hidden" name="form_email_verified" value="no">
                                                                <button type="submit" style="background: none;border: none;padding: 0;color: #4b38b3;" class="text-decoration-underline">View Users Not Verified</button>
                                                            </div>
                                                        </form>
                                                        <!-- <a href="" class="text-decoration-underline">View Users Not Verified</a> -->
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-danger rounded fs-3">
                                                            <i class="bx bx-user-x"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Files Submitted</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <!-- <h5 class="text-success fs-14 mb-0">
                                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +29.08 %
                                                        </h5> -->
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="<?= $fileSubmittedUsers ?>">0</span></h4>
                                                        <form action="/auth/admin/clients.php" method="POST">
                                                            <input type="hidden" name="form_file_submit" value="Yes">
                                                            <button type="submit" style="background: none;border: none;padding: 0;color: #4b38b3;" class="text-decoration-underline">View Submitted Files</button>
                                                        </form>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-info rounded fs-3">
                                                            <i class="bx bxs-file"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Files Submission Pending</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <!-- <h5 class="text-muted fs-14 mb-0">
                                                            +0.00 %
                                                        </h5> -->
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="<?= $fileNotSubmittedUsers ?>">0</span></h4>
                                                        <form action="/auth/admin/clients.php" method="POST">
                                                            <input type="hidden" name="form_file_submit" value="No">
                                                            <button type="submit" style="background: none;border: none;padding: 0;color: #4b38b3;" class="text-decoration-underline">View Files Not Submitted</button>
                                                        </form>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-danger rounded fs-3">
                                                            <i class="bx bx-file-blank"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->
                                </div> <!-- end row-->

                                <div class="row">
                                    
                                    <div class="col-xl-7">
                                        <div class="card">

                                            <div class="card-header border-0 align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1">Daily Users</h4>
                                                <div>
                                                    <button type="button" class="btn btn-soft-secondary btn-sm shadow-none">
                                                        ALL
                                                    </button>
                                                    <button type="button" class="btn btn-soft-secondary btn-sm shadow-none">
                                                        1M
                                                    </button>
                                                    <button type="button" class="btn btn-soft-secondary btn-sm shadow-none">
                                                        6M
                                                    </button>
                                                    <button type="button" class="btn btn-soft-primary btn-sm shadow-none">
                                                        1Y
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- end card header -->

                                            <!-- end card header -->

                                            <div class="card-body p-0 pb-2">
                                                <div class="w-100">
                                                    <div id="user_tax_information_chart" class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div><!-- end card body -->

                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-5">
                                        <div class="card">
                                            <div class="card-header align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1">Recents Signups</h4>
                                               
                                            </div><!-- end card header -->

                                            <div class="card-body">
                                                <div class="table-responsive table-card">
                                                    <table class="table table-borderless table-hover table-nowrap align-middle mb-0">

                                                        <thead class="table-light">
                                                            <tr class="text-muted">
                                                                <th scope="col">Full Name</th>
                                                                <th scope="col">Phone</th>
                                                                <th scope="col">Email</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php
                                                            while ($row = $userData->fetch(PDO::FETCH_ASSOC)) {
                                                                $userId = $row['id'];
                                                                ?>
                                                                <tr class="" id="row<?php echo $userId; ?>">
                                                                    <td><?= $row['first_name'] ?> <?= $row['last_name'] ?></td>
                                                                    <td><?= encrypt_decrypt('decrypt', $row['phone']) ?></td>
                                                                    <td><?= encrypt_decrypt('decrypt', $row['email'] ) ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                            <!-- <tr>
                                                                <td>Absternet LLC</td>
                                                                <td>Sep 20, 2021</td>
                                                                <td><img src="assets/images/users/avatar-1.jpg" alt="" class="avatar-xs rounded-circle me-2 shadow">
                                                                    <a href="#javascript: void(0);" class="text-body fw-medium">Donald Risher</a>
                                                                </td>
                                                                <td><span class="badge bg-success-subtle text-success p-2">Deal Won</span></td>
                                                                <td>
                                                                    <div class="text-nowrap">$100.1K</div>
                                                                </td>
                                                            </tr> -->
                                                            
                                                        </tbody><!-- end tbody -->
                                                    </table><!-- end table -->
                                                </div><!-- end table responsive -->
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div>
                                    <!-- end col -->
                                </div>



                            </div> <!-- end .h-100-->

                        </div> <!-- end col -->

                    </div>

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © Paragon Accounting and Financial Services.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>


    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Vector map-->
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


    <?php
        // Get the current year and month
        $year = date('Y');
        $month = date('m');

        // Get the number of days in the current month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Initialize an array to store the dates
        $dates = [];

        // Generate dates for each day of the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            // Create a date string in the format 'M d, Y'
            $date = date('M d, Y', mktime(0, 0, 0, $month, $day, $year));
            // Add the date string to the array
            $dates[] = $date;
        }

        // Convert the PHP array to a JavaScript array
        $jsonDates = json_encode($dates);

        // Your database query
        $query = "
            SELECT 
                DATE(users.created_at) AS date,
                COUNT(users.id) AS total_users,
                SUM(IF(users.email_verified = 'no', 1, 0)) AS email_verified_count,
                SUM(IF(tax_information.is_file_submit = 'Yes', 1, 0)) AS files_submitted_count
            FROM users
            LEFT JOIN tax_information ON users.email = tax_information.email
            WHERE MONTH(users.created_at) = MONTH(CURRENT_DATE()) AND YEAR(users.created_at) = YEAR(CURRENT_DATE())
            GROUP BY DATE(users.created_at)
            ORDER BY DATE(users.created_at) ASC;
        ";

        // Execute the query and fetch the data
        $result = $db->query($query);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['date']] = [
                'date' => $row['date'],
                'total_users' => $row['total_users'],
                'email_verified_count' => $row['email_verified_count'],
                'files_submitted_count' => $row['files_submitted_count']
            ];
        }

        // Create an array for all days of the month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $dateFormat = 'Y-m-d';
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        $dateRange = [];
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $dateRange[] = date($dateFormat, strtotime($currentDate));
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        // Create arrays for each series
        $totalUsersData = [];
        $emailVerifiedData = [];
        $filesSubmittedData = [];

        foreach ($dateRange as $date) {
            if (isset($data[$date])) {
                $totalUsersData[] = $data[$date]['total_users'];
                $emailVerifiedData[] = $data[$date]['email_verified_count'];
                $filesSubmittedData[] = $data[$date]['files_submitted_count'];
            } else {
                $totalUsersData[] = 0;
                $emailVerifiedData[] = 0;
                $filesSubmittedData[] = 0;
            }
        }
    ?>
    <script>
        // Parse the JSON string into a JavaScript array
        var dates = <?php echo $jsonDates; ?>;

        // Format the dates as "dd Mmm yyyy"
        var formattedDates = dates.map(function(dateString) {
            var date = new Date(dateString);
            var day = date.getDate().toString().padStart(2, '0');
            var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            var month = monthNames[date.getMonth()];
            var year = date.getFullYear();
            return day + ' ' + month;
        });

        var options = {
            series: [{
                name: 'Users',
                type: 'column',
                data: <?php echo json_encode($totalUsersData); ?>
            }, {
                name: 'Email Not Verified',
                type: 'area',
                color: '#F44336',
                data: <?php echo json_encode($emailVerifiedData); ?>
            }, {
                name: 'Files Submitted',
                type: 'line',
                color: '#45cb85',
                data: <?php echo json_encode($filesSubmittedData); ?>
            }],
            chart: {
                height: 350,
                type: 'line',
                stacked: false,
            },
            stroke: {
                width: [0, 2, 5],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%'
                }
            },
            fill: {
                opacity: [0.85, 0.25, 1],
                gradient: {
                    inverseColors: false,
                    shade: 'light',
                    type: "vertical",
                    opacityFrom: 0.85,
                    opacityTo: 0.55,
                    stops: [0, 100, 100, 100]
                }
            },
            // dataLabels: {
            //     enabled: true,
            //     enabledOnSeries: [2]
            // },
            labels: dates,
            markers: {
                size: 0
            },
            labels: formattedDates,
            xaxis: {
                // type: 'datetime'
            },
            yaxis: {
                title: {
                    text: 'Count',
                },
                min: 0
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (y) {
                        if (typeof y !== "undefined") {
                            return y.toFixed(0);
                        }
                        return y;
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#user_tax_information_chart"), options);
        chart.render();


        $('#users_dashboard_table_refresh').on('click', function() {
            location.reload();
        });
        
    </script>

    <script>
        $(document).ready(function () {
            
            // Set the initial countdown time to 5 minutes
            let countdown = 5 * 60; // 5 minutes in seconds

            // Function to update the timer
            function updateTimer() {
                const minutes = Math.floor(countdown / 60);
                const seconds = countdown % 60;
                const formattedTime = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                document.getElementById('refreshTimer').innerHTML = `Next refresh in: ${formattedTime}`;
                
                // If countdown reaches 0, refresh the page and reset the timer
                if (countdown === 0) {
                    location.reload();
                    countdown = 5 * 60; // Reset the countdown to 5 minutes
                } else {
                    countdown--; // Decrease the countdown by 1 second
                }
            }

            // Initial call to update the timer
            updateTimer();

            // Set interval to update the timer every second
            setInterval(updateTimer, 1000);         
        });
    </script>

</body>

</html>