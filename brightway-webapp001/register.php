<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
    $stmt->bind_param("sss", $name, $email, $password);
    
    if ($stmt->execute()) {
        echo "Registration successful! <a href='login.php' >Login now</a>";
    } else {
        echo "Error: " . $stmt->error;
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
            height: 300px;
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
        <form   method="POST">
                <h2>Register</h2>
                 <input type="text" name="name" required placeholder="Full Name" size="46" ><br><br>
                <input type="email" name="email" required placeholder="Email" size="46"><br><br>
                <input type="password" name="password" required placeholder="Password" minlenth="8" size="46"><br><br>
                 <button type="submit">Register</button>
         </form>
     </div>   
   

</body>
</html>




