<?php
require 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header("Location: dashboard/admin.php");
                break;
            case 'salesperson':
                header("Location: dashboard/salesperson.php");
                break;
            case 'customer':
                header("Location: dashboard/customer.php");
                break;
        }
        exit();
    } else {
        echo "<script> alert('Invalid Password') </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brightway</title>
    <link rel="icon" type="image/x-icon" href="images/Brightway-logo.png">
    <link rel="stylesheet" href="assets/Css/Style.css">
    <style>
        *{
            margin: 0;
            padding:0;

        }
        body{
            background-color: #FAF5ED;
            color:#083A75;
            font-family:"inknuk-antiqua","sans-serif";
            box-sizing: border-box;
        }
        
        .parent {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%; /* optional, helps with responsiveness */
            height: 100vh; /* full screen height */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-image: radial-gradient(ellipse, #EC6408, #EEB353);
            height: 250px;
            width: 430px;
            border-radius: 20px;
            justify-content: center;
        }
        h2{
            padding-bottom: 10px;
        }

            
        input{
            border-radius: 5px;
            padding:10px;
            background-color: #FAF5ED; 
            width: 300px;
        }
        input::placeholder{
           color: #083A75; 
           outline: none;
             
        }
        input:focus{
            outline : none;
            border: none;
        }
        button{
            background-color: #EEB353;
            padding: 9px;
            border-radius:5px;
            outline:none;
            border:2px solid #EC6408;
        }
        button:hover{
            border:1px solid #EC6408;
            color:rgb(238, 223, 199);
        }
    </style>
</head>
<body>
    <div class="parent">
        <form method="POST">
            <h2 class="h2">Login</h2>
             <input type="email" name="email" required placeholder="Email"><br><br>
            <input type="password" name="password" required placeholder="Password"><br><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>



