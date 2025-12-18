<?php
if (isset($_POST['error']) && isset($_POST['email'])) {
    error_log("Client " . $_POST['email'] . " did not upload: " . $_POST['error']);
}
?>