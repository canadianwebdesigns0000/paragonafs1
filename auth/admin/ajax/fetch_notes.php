<?php
session_start();
include '../../config.php';

// SESSION CHECK SET OR NOT
if (!isset($_SESSION['admin'])) {
    header('location:index.php');
    exit(); // Always exit after a redirect
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

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_email = $_POST['user_email'];

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
    $notes_query_result->execute([$user_email]); // Pass the user's email as an array
    $user_notes = $notes_query_result->fetchAll(PDO::FETCH_ASSOC);

// Display user information and their associated notes
foreach ($user_notes as $user_note) { 
    if (!empty($user_note['notes_content']) && $user_note['notes_user_email'] === $user_email) { 
?>

<div class="d-flex mb-2">
    <div class="flex-grow-1">
        <h5 class="fs-13"><?= $user_note['first_name'] ?> <?= $user_note['last_name'] ?> &nbsp; <?= !empty($user_note['notes_created_at']) ? formatDateTime2($user_note['notes_created_at']) : '---'; ?></h5>
        <p class="text-muted"><?= $user_note['notes_content'] ?></p>
    </div>
</div>

<?php
        } 
    } 
} 
?>