<?php
session_start();
include '../../config.php';

// SESSION CHECK SET OR NOT
if (!isset($_SESSION['admin'])) {
    header('location:index.php');
}

$searchQuery = isset($_POST['search']) ? $_POST['search'] : '';
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$emailVerified = isset($_POST['emailVerified']) ? $_POST['emailVerified'] : '';
$fileSubmitted = isset($_POST['fileSubmitted']) ? $_POST['fileSubmitted'] : '';
$userTaxCount = isset($_POST['userTaxCount']) ? $_POST['userTaxCount'] : 10;
$sort = isset($_POST['sort']) ? $_POST['sort'] : 'users.created_at';
$direction = isset($_POST['direction']) ? $_POST['direction'] : 'desc';

$itemsPerPage = $userTaxCount;
$offset = ($page - 1) * $itemsPerPage;

$searchColumns = [
    'first_name', 'last_name', 'phone', 'email'
];

$searchConditions = [];
foreach ($searchColumns as $column) {
    if ($column === 'phone' || $column === 'email') {
        // Encrypt the search query for phone and email columns
        $searchConditions[] = "`users`.`$column` = '" . encrypt_decrypt('encrypt', $searchQuery) . "'";
    } else {
        $searchConditions[] = "`users`.`$column` LIKE '%$searchQuery%'";
    }
}

$whereClause = implode(' OR ', $searchConditions);

// QUERY TO GET USER DATA AND TAX INFORMATION
$query = "
    SELECT 
        users.id AS user_id,
        users.first_name AS user_first_name,
        users.last_name AS user_last_name,
        users.phone AS user_phone,
        users.email AS user_email,
        users.email_verified AS user_email_verified,
        users.lastlogin_at AS user_lastlogin_at,
        users.created_at AS user_created_at,
        tax_information.*
    FROM users
    LEFT JOIN tax_information ON users.email = tax_information.email
";

// Add the WHERE clause if there is a search query
if (!empty($whereClause)) {
    $query .= " WHERE ($whereClause)";
}

// Add the email verified condition if it is set
if (!empty($emailVerified)) {
    // Add AND only if there's already a condition in the WHERE clause
    $query .= empty($whereClause) ? " WHERE " : " AND ";
    $query .= "`email_verified` = '$emailVerified'";
}

// Add the email verified condition if it is set
if (!empty($fileSubmitted)) {
    // Add AND only if there's already a condition in the WHERE clause
    $query .= empty($whereClause) ? " WHERE " : " AND ";
    $query .= "`is_file_submit` = '$fileSubmitted'";
}

$query .= " ORDER BY $sort $direction LIMIT $itemsPerPage OFFSET $offset";

$userData = $db->prepare($query);
$userData->execute();

// Get total count for pagination
$queryTotal = "
    SELECT COUNT(*) AS total
    FROM users
    LEFT JOIN tax_information ON users.email = tax_information.email
";

// Add the WHERE clause if there is a search query
if (!empty($whereClause)) {
    $queryTotal .= " WHERE ($whereClause)";
}

// Add the email verified condition if it is set
if (!empty($emailVerified)) {
    // Add AND only if there's already a condition in the WHERE clause
    $queryTotal .= empty($whereClause) ? " WHERE " : " AND ";
    $queryTotal .= "`email_verified` = '$emailVerified'";
}

// Add the email verified condition if it is set
if (!empty($fileSubmitted)) {
    // Add AND only if there's already a condition in the WHERE clause
    $queryTotal .= empty($whereClause) ? " WHERE " : " AND ";
    $queryTotal .= "`is_file_submit` = '$fileSubmitted'";
}

$totalData = $db->query($queryTotal)->fetch(PDO::FETCH_ASSOC);
$totalItems = $totalData['total'];

// Calculate total pages
$totalPages = ceil($totalItems / $itemsPerPage);

$entriesStart = min($totalItems, ($page - 1) * $itemsPerPage + 1);
$entriesEnd = min($entriesStart + $itemsPerPage - 1, $totalItems);
$entriesInfo = "Showing <b>$entriesStart</b> to <b>$entriesEnd</b> of <b>$totalItems</b> entries";

$pagination = '<ul class="pagination justify-content-end" style="margin: 0;">';

// Add "Previous" button
if ($page > 1) {
    $pagination .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page - 1) . '">Previous</a></li>';
}

// Display the current page and a few adjacent pages
$pagesToShow = 2; // Adjust the number of pages to show on each side of the current page

for ($i = max(1, $page - $pagesToShow); $i <= min($page + $pagesToShow, $totalPages); $i++) {
    $activeClass = ($i === $page) ? 'active' : '';
    $pagination .= '<li class="page-item ' . $activeClass . '"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
}

// Add "Next" button
if ($page < $totalPages) {
    $pagination .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page + 1) . '">Next</a></li>';
}

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

function formatDateTime2($datetime) {
    // Convert datetime string to DateTime object
    $dateTimeObj = new DateTime($datetime);

    // Format the date
    $formattedDate = $dateTimeObj->format('M d, Y');

    // Format the time
    $formattedTime = $dateTimeObj->format('g:i A');

    // Return the formatted datetime with time in the desired format
    return  '<small class="text-muted">' . $formattedDate . ' ' . $formattedTime . '</small>';
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
                <th class="text-uppercase" data-sort="users.id">
                    ID
                    <?php if ($sort === 'users.id'): ?>
                        <?php if ($direction === 'asc'): ?>
                            <i class="mdi mdi-sort-ascending"></i>
                        <?php else: ?>
                            <i class="mdi mdi-sort-descending"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="mdi mdi-sort"></i>
                    <?php endif; ?>
                </th>
                <th class="text-uppercase" data-sort="users.first_name">
                    First Name 
                    <?php if ($sort === 'users.first_name'): ?>
                        <?php if ($direction === 'asc'): ?>
                            <i class="mdi mdi-sort-ascending"></i>
                        <?php else: ?>
                            <i class="mdi mdi-sort-descending"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="mdi mdi-sort"></i>
                    <?php endif; ?>
                </th>
                <th class="text-uppercase" data-sort="users.last_name">
                    Last Name
                    <?php if ($sort === 'users.last_name'): ?>
                        <?php if ($direction === 'asc'): ?>
                            <i class="mdi mdi-sort-ascending"></i>
                        <?php else: ?>
                            <i class="mdi mdi-sort-descending"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="mdi mdi-sort"></i>
                    <?php endif; ?>
                </th>
                <th class="text-uppercase">Phone</th>
                <th class="text-uppercase">Email</th>
                <th class="text-uppercase" data-sort="users.created_at">
                    Account Created
                    <?php if ($sort === 'users.created_at'): ?>
                        <?php if ($direction === 'asc'): ?>
                            <i class="mdi mdi-sort-ascending"></i>
                        <?php else: ?>
                            <i class="mdi mdi-sort-descending"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="mdi mdi-sort"></i>
                    <?php endif; ?>
                </th>
                <th class="text-uppercase" data-sort="users.email_verified">
                    Email Verified
                    <?php if ($sort === 'users.email_verified'): ?>
                        <?php if ($direction === 'asc'): ?>
                            <i class="mdi mdi-sort-ascending"></i>
                        <?php else: ?>
                            <i class="mdi mdi-sort-descending"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="mdi mdi-sort"></i>
                    <?php endif; ?>
                </th>
                <th class="text-uppercase" data-sort="users.lastlogin_at">
                    Last Login
                    <?php if ($sort === 'users.lastlogin_at'): ?>
                        <?php if ($direction === 'asc'): ?>
                            <i class="mdi mdi-sort-ascending"></i>
                        <?php else: ?>
                            <i class="mdi mdi-sort-descending"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="mdi mdi-sort"></i>
                    <?php endif; ?>
                </th>
                <th class="text-uppercase" data-sort="tax_information.is_file_submit">
                    File Submitted
                    <?php if ($sort === 'tax_information.is_file_submit'): ?>
                        <?php if ($direction === 'asc'): ?>
                            <i class="mdi mdi-sort-ascending"></i>
                        <?php else: ?>
                            <i class="mdi mdi-sort-descending"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="mdi mdi-sort"></i>
                    <?php endif; ?>
                </th>
                <th class="text-uppercase" data-sort="tax_information.file_submit_date">
                    File Submitted Date
                    <?php if ($sort === 'tax_information.file_submit_date'): ?>
                        <?php if ($direction === 'asc'): ?>
                            <i class="mdi mdi-sort-ascending"></i>
                        <?php else: ?>
                            <i class="mdi mdi-sort-descending"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="mdi mdi-sort"></i>
                    <?php endif; ?>
                </th>
                <th class="text-uppercase">Action</th>
            </tr>
        </thead>

        <tbody class="list form-check-all">
            <?php while ($row = $userData->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td class="client_id"><?= $row['user_id']?></td>
                    <td class="client_first_name"><?= $row['user_first_name']?></td>
                    <td class="client_last_name"><?= $row['user_last_name']?></td>
                    <td class="client_phone"><?= encrypt_decrypt('decrypt', $row['user_phone'])?></td>
                    <td class="client_email"><?= encrypt_decrypt('decrypt', $row['user_email'])?></td>
                    <td class="client_created_at"><?= formatDateTime($row['user_created_at']) ?></td>
                    <td class="client_email_verified">
                        <?php if ($row['user_email_verified'] == 'yes') { ?>
                            <span class="badge bg-success-subtle text-success text-uppercase"><?= $row['user_email_verified'] ?></span>
                        <?php } else { ?>
                            <span class="badge bg-danger-subtle text-danger text-uppercase"><?= $row['user_email_verified'] ?></span>
                        <?php } ?>
                    </td>
                    <td class="client_email_verified_date"><?= !empty($row['user_lastlogin_at']) ? formatTimeAgo($row['user_lastlogin_at']) : '---'; ?></td>
                    <td class="client_file_submitted">
                        <?php if ($row['is_file_submit'] == 'Yes') { ?>
                            <span class="badge bg-success-subtle text-success text-uppercase"><?= $row['is_file_submit'] ?></span>
                        <?php } else if ($row['is_file_submit'] == 'No') { ?>
                            <span class="badge bg-danger-subtle text-danger text-uppercase"><?= $row['is_file_submit'] ?></span>
                        <?php } else { ?>
                            ---
                        <?php } ?>
                    </td>
                    <td class="client_file_submitted_date"><?= !empty($row['file_submit_date']) ? formatDateTime($row['file_submit_date']) : '---'; ?></td>
                    <td>
                        <ul class="list-inline hstack gap-2 mb-0">
                            <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="Add Notes" data-bs-original-title="Add Notes">
                                <a href="#showModal-<?= encrypt_decrypt('decrypt', $row['user_email'])?>" data-bs-toggle="modal" class="text-primary d-inline-block notes-item-btn">
                                    <i class="ri-add-circle-fill fs-16"></i>
                                </a>
                            </li>

                            <?php if ($row['is_file_submit'] === null): ?>
                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="No Data Yet" data-bs-original-title="No Data Yet">
                                    <a href="javascript: void(0);" class="text-muted d-inline-block">
                                        <i class="ri-eye-off-fill fs-16"></i>
                                    </a>
                                </li>
                            <?php else: ?>
                                <!-- <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="View" data-bs-original-title="View">
                                    <a href="view-tax-information.php?email_id=<?= $row['user_email'] ?>" class="text-muted d-inline-block">
                                        <i class="ri-eye-fill fs-16"></i>
                                    </a>
                                </li> -->
                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="View" data-bs-original-title="View">
                                    <a href="#viewTaxInformationModal-<?= $row['user_email'] ?>" class="text-muted d-inline-block view-tax-information">
                                        <i class="ri-eye-fill fs-16"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                    <?php if (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] === 'yes') : ?>
                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="Remove" data-bs-original-title="Remove">
                                <a class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal" href="#deleteUser-<?= encrypt_decrypt('decrypt', $row['user_email'])?>">
                                    <i class="ri-delete-bin-5-fill fs-16"></i>
                                </a>
                            </li>
                    <?php endif; ?>
                            
                        </ul>
                    </td>
                </tr>

                <!-- Add Notes Modal -->
                <div class="modal fade" id="showModal-<?= encrypt_decrypt('decrypt', $row['user_email'])?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-light p-3">
                                <h5 class="modal-title" id="exampleModalLabel"><?= $row['user_first_name']?> <?= $row['user_last_name']?> - Add Notes </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                            </div>
                            <div class="modal-body">
                                <form class="addNotesForm" method="POST" autocomplete="off">
                                    
                                    <input type="hidden" id="user_email_notes" name="user_email_notes" value="<?= $row['user_email'] ?>" />

                                    <div class="mb-3">
                                        <textarea class="form-control" id="addNotesText-<?= $row['id'] ?>" name="add_notes_text" rows="3" required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success mb-4" id="add_notes_button" onclick="add_notes(event, '<?= $row['id'] ?>', '<?= $row['user_email'] ?>', '<?= encrypt_decrypt('decrypt', $row['user_phone']) ?>', $('#addNotesText-<?= $row['id'] ?>').val()); return false;">Add</button>
                                    
                                    <div id="userNotesContainer-<?= $row['id'] ?>">

                                        <?php 
                                        $notes_query_sql = "
                                            SELECT 
                                                notes.admin_username AS notes_admin_username,
                                                notes.content AS notes_content,
                                                notes.created_at AS notes_created_at,
                                                notes.user_email as notes_user_email,
                                                admin.*
                                            FROM admin
                                            LEFT JOIN notes ON admin.username = notes.admin_username
                                            WHERE notes.user_email = ? ORDER BY notes_created_at desc"; // Add a WHERE clause to filter by user's email
                                        $notes_query_result = $db->prepare($notes_query_sql);
                                        $notes_query_result->execute([$row['user_email']]); // Pass the user's email as an array
                                        $user_notes = $notes_query_result->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        // Display user information and their associated notes
                                        foreach ($user_notes as $user_note) { 
                                            if (!empty($user_note['notes_content']) && $user_note['notes_user_email'] === $row['user_email']) { 
                                        ?>

                                        <div class="d-flex mb-2">
                                            <div class="flex-grow-1">
                                                <h5 class="fs-13"><?= $user_note['first_name'] ?> <?= $user_note['last_name'] ?> &nbsp; <?= !empty($user_note['notes_created_at']) ? formatDateTime2($user_note['notes_created_at']) : '---'; ?></h5>
                                                <p class="text-muted"><?= $user_note['notes_content'] ?></p>
                                            </div>
                                        </div>

                                        <?php } 
                                        }
                                        ?>

                                    </div>

                                    

                                    <!-- <div class="d-flex mb-2">
                                        <div class="flex-grow-1">
                                            <h5 class="fs-13">Lawrence Test <small class="text-muted ms-2">Feb 18 - 05:20PM</small></h5>
                                            <p class="text-muted">Tes Message</p>
                                        </div>
                                    </div> -->

                                
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remove Modal -->
                <div class="modal fade flip" id="deleteUser-<?= encrypt_decrypt('decrypt', $row['user_email'])?>" tabindex="-1" aria-labelledby="deleteUserLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body p-5 text-center">
                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                                </lord-icon>
                                <div class="mt-4 text-center">
                                    <h4>Are you sure you want to delete <?= $row['user_first_name']?> <?= $row['user_last_name']?> Information?</h4>
                                    <p class="text-muted fs-15 mb-4">Deleting this user will remove all its information from the database.</p>
                                    <div class="hstack gap-2 justify-content-center remove">
                                        <button class="btn btn-link link-success fw-medium text-decoration-none" id="deleteRecord-close" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</button>
                                        <button class="btn btn-danger" onclick="delete_user_information('<?= $row['user_email'] ?>')">Yes, Delete It</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php   }   ?>
        </tbody>
    </table>


    <!-- View Modal -->
    <div class="modal fade modal-lg" id="viewTaxInformationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">Client Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>

                <div class="modal-body">
            
                </div>            
            </div>
        </div>
    </div>


    <!-- <div class="noresult" style="display: none">
        <div class="text-center">
            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
            <h5 class="mt-2">Sorry! No Result Found</h5>
            <p class="text-muted mb-0">We've searched more than 150+ data We did not find any for you search.</p>
        </div>
    </div> -->
    
</div>

<div class="row mt-3">
    <div class="col-lg-6" style="margin: 10px 0 5px;">
        <?= $entriesInfo ?>
    </div>
    <div class="col-lg-6">
        <?= $pagination ?>
    </div>
</div>

<script>

    $(document).ready(function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });


    $(document).ready(function() {
        var currentPage = <?php echo json_encode($page); ?>; // Track the current page
        var user_tax_count = <?php echo json_encode($userTaxCount); ?>;
        var searchQuery = <?php echo json_encode($searchQuery); ?>;
        var selectedIfEmailVerified = <?php echo json_encode($emailVerified); ?>;
        var selectedIfFileSubmitted = <?php echo json_encode($fileSubmitted); ?>;
        var direction = <?php echo json_encode($direction); ?>; // Assuming $direction contains 'asc' or 'desc'

        $("th").click(function() {
            var dataSortValue = $(this).attr('data-sort');

            // If the data-sort attribute doesn't exist, don't do anything
            if (!dataSortValue) {
                return;
            }

            // Determine the new sorting direction
            if (direction === 'asc') {
                direction = 'desc';
            } else {
                direction = 'asc';
            }

            // Toggle the sorting icon
            $("th[data-sort] i").removeClass('mdi-sort-ascending mdi-sort-descending').addClass('mdi-sort');
            var icon = $(this).find('i');
            if (direction === 'asc') {
                icon.removeClass('mdi-sort mdi-sort-descending').addClass('mdi-sort-ascending');
            } else {
                icon.removeClass('mdi-sort mdi-sort-ascending').addClass('mdi-sort-descending');
            }

            console.log(direction);

            $.ajax({
                url: 'ajax/view_tax_information.php',
                type: 'post',
                data: {
                    sort: dataSortValue,
                    page: currentPage,
                    search: searchQuery,
                    emailVerified: selectedIfEmailVerified,
                    fileSubmitted: selectedIfFileSubmitted,
                    userTaxCount: user_tax_count,
                    direction: direction,
                },
                success: function(response) {
                    // handle response here
                    $('#taxInformationContainer').html(response);
                }
            });
        });
    });

    function add_notes(event, id, email, phone, notesText) {
        event.preventDefault(); // Prevent default form submission

        if (notesText.trim() === "") {
            alert("Please enter some notes.");
            return false; // Prevent form submission
        } else {
            // Submit the form
            $.ajax({
                url: "ajax/add_notes.php",
                type: "POST",
                data: { user_email_notes: email, add_notes_text: notesText },
                container: ".addNotesForm",
                messagePosition: "inline",
                success: function (response) {
                    console.log(response);

                    $('#addNotesText-' + id).val('');

                    // Display toast notification using Toastify.js
                    Toastify({
                        text: response,
                        duration: 3000, // Display duration in milliseconds
                        gravity: "top", // Toast position (top, bottom, or center)
                        position: "center", // Toast position (left, right, or center)
                        background: "linear-gradient(to right, #00b09b, #96c93d)", // Background color
                    }).showToast();

                    // Fetch and update the notes for the corresponding user
                    $.ajax({
                        url: "ajax/fetch_notes.php", // Adjust the URL to the script that fetches notes for a user
                        type: "POST",
                        data: { user_email: email },
                        success: function (newNotesHTML) {
                            console.log(newNotesHTML);
                            $('#userNotesContainer-' + id).empty().html(newNotesHTML);
                        }
                    });
                }
            });
        }

    }

    function delete_user_information(email) {
        event.preventDefault(); // Prevent default form submission

        console.log("on click delete", email);

        // Submit the form
        $.ajax({
            url: "ajax/delete_user_information.php",
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


    $('.view-tax-information').on('click', function(e) {
        e.preventDefault();

        // Get the email from the href attribute
        var email = $(this).attr('href').split('-')[1];

        console.log(email);
        // Load the content with AJAX

        // $('#viewTaxInformationModal').modal('show');

        // $.ajax({
        //     url: 'ajax/display_tax_Information_modal.php',
        //     type: 'POST',
        //     data: { email: email },
        //     success: function(response) {
        //         // Add the response to the modal body
        //         $('#viewTaxInformationModal .modal-body').html(response);

        //         // Show the modal
        //         $('#viewTaxInformationModal').modal('show');
        //     }
        // });

        $.post("ajax/display_tax_Information_modal.php", { email }, function (response) {
            $('#viewTaxInformationModal .modal-body').html(response);
            $('#viewTaxInformationModal').modal('show');
        });

    });
    
</script>