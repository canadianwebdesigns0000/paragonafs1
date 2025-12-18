<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        #login-form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 350px;
            width: 100%;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #3a7bdb;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #2e63a9;
        }

        button:active {
            background-color: #24558a;
        }
    </style>
</head>
<body>
    <form id="login-form">
        <input type="text" id="username" placeholder="Username" required>
        <input type="password" id="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <script>    
        document.getElementById("login-form").addEventListener("submit", function(event) {
            event.preventDefault();
            
            const username = document.getElementById("username").value;
            const password = document.getElementById("password").value;

            // Replace with your own logic to validate user credentials
            if (username === "paragon" && password === "paragon") {
                // Redirect to the Tax Information page if the credentials are correct
                localStorage.setItem('loggedin', 'true');
                window.location.href = "allclients.php";
            } else {
                // Display an error message if the credentials are incorrect
                alert("Invalid username or password.");
            }
        });
    </script>
</body>
</html>