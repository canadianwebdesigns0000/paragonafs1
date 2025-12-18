<?php
if (isset($_POST['inputName']) && isset($_POST['inputValue'])) {
    error_log("Client " . $_POST['email'] . " submitted input: " . $_POST['inputName'] . " with value: " . $_POST['inputValue']);
}
?>