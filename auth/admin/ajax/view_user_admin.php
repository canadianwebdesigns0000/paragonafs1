<?php
include '../../config.php';

$searchQuery = isset($_POST['search']) ? $_POST['search'] : '';

$searchColumns = [
    'first_name', 'last_name', 'phone', 'email'
];

$searchConditions = [];
foreach ($searchColumns as $column) {
    if ($column === 'phone' || $column === 'email') {
        // Encrypt the search query for phone and email columns
        $searchConditions[] = "`$column` = '" . encrypt_decrypt('encrypt', $searchQuery) . "'";
    } else {
        $searchConditions[] = "`$column` LIKE '%$searchQuery%'";
    }
}

$whereClause = implode(' OR ', $searchConditions);

// QUERY TO GET USER DATA AND TAX INFORMATION
$query = "SELECT * FROM admin";

// Add the WHERE clause if there is a search query
if (!empty($whereClause)) {
    $query .= " WHERE ($whereClause)";
}

$query .= " ORDER BY created_at";

$userData = $db->prepare($query);
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

function formatTimeAgo($timestamp) {
    $currentTime = time();
    $timestamp = strtotime($timestamp);
    $timeAgo = $currentTime - $timestamp;

    if ($timeAgo < 60) {
        return 'just now';
    } elseif ($timeAgo < 3600) {
        $minutes = floor($timeAgo / 60);
        return $minutes == 1 ? '1 minute ago' : $minutes . ' minutes ago';
    } elseif ($timeAgo < 86400) {
        $hours = floor($timeAgo / 3600);
        return $hours == 1 ? '1 hour ago' : $hours . ' hours ago';
    } else {
        $days = floor($timeAgo / 86400);
        return $days == 1 ? '1 day ago' : $days . ' days ago';
    }
}

function formatDateTime($datetime) {
    // Convert datetime string to DateTime object
    $dateTimeObj = new DateTime($datetime);

    // Format the date
    $formattedDate = $dateTimeObj->format('M d, Y');

    // Format the time
    $formattedTime = $dateTimeObj->format('g:i A');

    // Return the formatted datetime with time in the desired format
    return $formattedDate . ' <small class="text-muted">' . $formattedTime . '</small>';
}
?>

<style>
    .table-container {
        max-height: 600px; /* Set your desired max height */
        overflow: auto; /* Enable scrollbar */
    }

    .table-container table thead th {
        position: sticky;
        top: 0;
        /* z-index: 1; */
    }

    .table-container table thead th:last-child,
    .table-container table tbody td:last-child {
        position: sticky;
        padding-left: 16px;
        right: 0;
        background-color: #F3F6F9;
    }

    .table-container table thead th:last-child {
        z-index: 1;
    }


</style>

<div class="table-container table-responsive table-card">

    <table class="table align-middle table-nowrap">

        <thead class="text-muted table-light">
            <tr>
                <th class="text-uppercase">ID</th>
                <th class="text-uppercase">Username</th>
                <th class="text-uppercase">First Name</th>
                <th class="text-uppercase">Last Name</th>
                <th class="text-uppercase">Phone</th>
                <th class="text-uppercase">Email</th>
                <th class="text-uppercase">Super Admin</th>
                <!-- <th class="text-uppercase">Last Login</th> -->
                <th class="text-uppercase">Account Created</th>
                <th class="text-uppercase">Action</th>
            </tr>
        </thead>

        <tbody class="list form-check-all">
            <?php while ($row = $userData->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td><?= $row['id']?></td>
                    <td><?= $row['username']?></td>
                    <td><?= $row['first_name']?></td>
                    <td><?= $row['last_name']?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <?php if ($row['is_superadmin'] == 'yes') { ?>
                            <span class="badge bg-success-subtle text-success text-uppercase"><?= $row['is_superadmin'] ?></span>
                        <?php } else { ?>
                            <span class="badge bg-danger-subtle text-danger text-uppercase"><?= $row['is_superadmin'] ?></span>
                        <?php } ?>
                    </td>
                    <td><?= formatDateTime($row['created_at']) ?></td>
                    <td>
                        <ul class="list-inline hstack gap-0 mb-0">
                            <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="Edit" data-bs-original-title="Edit">
                                <a href="#editAdminModal-<?= $row['email'] ?>" class="text-primary d-inline-block edit-item-btn">
                                    <i class="ri-pencil-fill fs-16"></i>
                                </a>
                            </li>
                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="Remove" data-bs-original-title="Remove">
                                <a href="#deleteUserAdmin-<?= $row['email'] ?>" class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal">
                                    <i class="ri-delete-bin-5-fill fs-16"></i>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>

                <!-- Remove Modal -->
                <div class="modal fade flip" id="deleteUserAdmin-<?= $row['email'] ?>" tabindex="-1" aria-labelledby="deleteUserAdminLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body p-5 text-center">
                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                                </lord-icon>
                                <div class="mt-4 text-center">
                                    <h4>Are you sure you want to delete <?= $row['first_name']?> <?= $row['last_name']?> Account</h4>
                                    <p class="text-muted fs-15 mb-4">Deleting this user will remove all its information from the database.</p>
                                    <div class="hstack gap-2 justify-content-center remove">
                                        <button class="btn btn-link link-success fw-medium text-decoration-none" id="deleteRecord-close" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</button>
                                        <button class="btn btn-danger" onclick="delete_user_information('<?= $row['email'] ?>')">Yes, Delete It</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php   }   ?>
        </tbody>

    </table>
    <!-- <div class="noresult" style="display: none">
        <div class="text-center">
            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
            <h5 class="mt-2">Sorry! No Result Found</h5>
            <p class="text-muted mb-0">We've searched more than 150+ data We did not find any for you search.</p>
        </div>
    </div> -->
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-light p-3">
                <h5 class="modal-title">Edit Admin User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>

            <div class="modal-body">
        
            </div>            
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {


        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

    });

    function delete_user_information(email) {
        event.preventDefault(); // Prevent default form submission

        console.log("on click delete", email);

        // Submit the form
        $.ajax({
            url: "ajax/delete_user_admin.php",
            type: "POST",
            data: { user_email: email },
            success: function (response) {
                console.log(response);

                location.reload();

                Toastify({
                    text: response,
                    duration: 3000, // Display duration in milliseconds
                    gravity: "top", // Toast position (top, bottom, or center)
                    position: "right", // Toast position (left, right, or center)
                    background: "linear-gradient(to right, #00b09b, #96c93d)", // Background color
                }).showToast();

            }
        });
    }

    $('.edit-item-btn').on('click', function(e) {
        e.preventDefault();

        // Get the email from the href attribute
        var email = $(this).attr('href').split('-')[1];

        console.log(email);
        // Load the content with AJAX
        $.ajax({
            url: 'ajax/display_user_admin_modal.php',
            type: 'POST',
            data: { email: email },
            success: function(response) {
                // Add the response to the modal body
                $('#editAdminModal .modal-body').html(response);

                // Show the modal
                $('#editAdminModal').modal('show');
            }
        });
    });


</script>