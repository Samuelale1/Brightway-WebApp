<?php
require '../includes/db.php'; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = $_POST['email'];
    $phone_no = $_POST['phone'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone_no, address, role) VALUES (?, ?, ?, ?, ?,'customer')");
    $stmt->bind_param("sssss", $name, $email, $password,$phone_no,$address);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Redirecting to login...'); 
        window.location.href = 'login.php';</script>";
    } else {
        $error = addslashes($stmt->error); 
        echo "<script>alert('Registration failed: $error');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brightway Register page</title>
    <link rel="icon" type="image/x-icon" href="..\assets\images\others\Brightway-logo.png">
    <style>
        body{
            background-color: #FAF5ED;
            color:#083A75;
            font-family:"inknuk-antiqua","sans-serif";
        }
        .parent{
            position: relative;
        }
        form{
            border: 2px solid #EC6408;
            width: 400px;
            height: 400px;
            text-align: center;
            position: absolute;
            top:200px;
            left: 600px;  
            background-color: #EEB353;  
            border-radius: 20px;
            outline: none;
        }
        
        input{
            border-radius: 5px;
            padding:10px;
            background-color: #FAF5ED; 
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
        <form   method="POST" class="form">
                <h2>Register</h2>
                 <input type="text" name="name" required placeholder="Full Name" size="46" ><br><br>
                <input type="email" name="email" required placeholder="Email" size="46"><br><br>
                <input type="tel" name="phone" required placeholder="Phone" size="46" ><br><br>
                <input type="text" name="address" required placeholder="address" size="46" ><br><br>
                <input type="password" name="password" required placeholder="Password" minlenth="8" size="46"><br><br>
                 <button type="submit">Register</button>
         </form>
     </div>   
   

</body>
</html>




