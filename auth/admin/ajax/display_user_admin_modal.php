<?php
    session_start();
    include '../../config.php';
    error_reporting(0);
// SESSION CHECK SET OR NOT
if (!isset($_SESSION['admin'])) {
    header('location:index.php');
}

    $user_admin_email = $_POST['email'];

    // Query To Get User Data
    $userData = $db->prepare('SELECT * FROM admin WHERE email=?');
    $userData->execute(array($user_admin_email));
    $row = $userData->fetch(PDO::FETCH_ASSOC);
?>

<form class="editForm" method="POST" style="padding: 16px;">
    <div class="row g-3">

        <div class="col-lg-6">
            <div class="form-floating"> 
                <input type="text" class="form-control" name="edit_admin_username" value="<?= $row['username']?>" placeholder="Enter your username" required />
                <label>Username</label>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-floating">
                <select class="form-select" name="edit_admin_is_superadmin" aria-label="Floating label select example" required>
                    <option value="yes" <?= $row['is_superadmin'] == 'yes' ? 'selected' : '' ?>>Yes</option>
                    <option value="no" <?= $row['is_superadmin'] == 'no' ? 'selected' : '' ?>>No</option>
                </select>
                <label>Super Admin?</label>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="edit_admin_first_name" value="<?= $row['first_name']?>" placeholder="Enter your firstname" required>
                <label>First Name</label>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="edit_admin_last_name" value="<?= $row['last_name'] ?>" placeholder="Enter your Lastname" required>
                <label>Last Name</label>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="edit_admin_phone" value="<?= $row['phone'] ?>" placeholder="Enter your phone" required>
                <label>Phone</label>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-floating">
                <input type="email" class="form-control" name="edit_admin_email" value="<?= $row['email'] ?>" placeholder="Enter your email" required>
                <label>Email Address</label>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-floating">
                <input type="password" class="form-control" name="edit_admin_password" placeholder="Enter your password" required>
                <label>Password</label>
                <small>Leave empty if you don't want to change password.</small>
            </div>
        </div>

        <!-- <div class="col-lg-6">
            <div class="form-floating">
                <input type="password" class="form-control" id="passwordfloatingInput1" name="admin_cpassword" placeholder="Confirm password" required>
                <label for="passwordfloatingInput1">Confirm Password</label>
            </div>
        </div> -->

        <div class="col-lg-12">
            <div class="text-end">
                <button type="submit" onclick="update_user_admin(event, '<?= $row['id'] ?>')" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</form>


<script>
function update_user_admin(event, userId) {
    event.preventDefault();
    
    // Serialize form date
    var formData = $('.editForm').serialize();

    console.log(formData);

    // AJAX request
    $.ajax({
        url: 'ajax/update_user_admin.php', // PHP script to handle the update
        type: 'POST',
        data: formData,
        success: function(response) {
            console.log(response); // Log the response from the server
           // Display toast notification using Toastify.js

           $('#editAdminModal').modal('hide');

           Toastify({
                text: response,
                duration: 3000, // Display duration in milliseconds
                gravity: "top", // Toast position (top, bottom, or center)
                position: "center", // Toast position (left, right, or center)
                background: "linear-gradient(to right, #00b09b, #96c93d)", // Background color
            }).showToast();

            var searchQuery = '';

            $.ajax({
                url: 'ajax/view_user_admin.php',
                method: 'POST',
                data: {
                    search: searchQuery,
                },
                success: function (response) {
                    // console.log(response);
                    if (response.trim() === '') {
                        $('#userAdminInformationContainer').html('');
                        // $('.noresult').show();
                    } else {
                        $('#userAdminInformationContainer').html(response);
                        // $('.noresult').hide();
                    }
                }
            });
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText); // Log any errors

        }
    });
    
}
</script>