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

$query .= " ORDER BY $sort $ASC LIMIT $itemsPerPage OFFSET $offset";

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

function calculateAge($birthdate) {

    $today = date("Y-m-d");

    $diff = date_diff(date_create($birthdate), date_create($today));
    
    $age = $diff->format('%y');

    return $age;
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
                <th class="text-uppercase">First Name</th>
                <th class="text-uppercase">Last Name</th>
                <th class="text-uppercase">E-Mail Address</th>
                <th class="text-uppercase">Phone</th>
                <th class="text-uppercase">Tags</th>
                <th class="text-uppercase">Owner</th>
                <th class="text-uppercase">Source</th>
                <th class="text-uppercase">Job Name</th>
                <th class="text-uppercase">Street Address</th>
                <th class="text-uppercase">City</th>
                <th class="text-uppercase">State/Region</th>
                <th class="text-uppercase">Postal Code</th>
                <th class="text-uppercase">Country</th>
                <th class="text-uppercase">Note</th>
                <th class="text-uppercase">Companies</th>
            </tr>
        </thead>

        <tbody class="list form-check-all">
            <?php while ($row = $userData->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td class=""><?= $row['user_first_name']?></td>
                    <td class=""><?= $row['user_last_name']?></td>
                    <td class="client_email"><?= encrypt_decrypt('decrypt', $row['user_email'])?></td>
                    <td class="client_phone">+1<?= encrypt_decrypt('decrypt', $row['user_phone'])?></td>
                    <td class="">Cold Lead</td>
                    <td class="">Limaweb</td>
                    <td class="">Direct traffic</td>
                    <td class="">&nbsp;</td>
                    <td class=""><?= $row['ship_address']?></td>
                    <td class=""><?= $row['locality']?></td>
                    <td class=""><?= $row['state']?></td>
                    <td class=""><?= $row['postcode']?></td>
                    <td class=""><?= $row['country']?></td>
                    <td class=""><?= encrypt_decrypt('decrypt', $row['birth_date']) ?> <?= !empty($row['gender']) ? $row['gender'] : '' ?> <?= !empty($row['marital_status']) ? $row['marital_status']  : '' ?></td>
                    <td class="">&nbsp;</td>
                </tr>

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