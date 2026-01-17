<?php
session_start();
$conn = new mysqli("localhost","root","","hospital2");

if(!isset($_SESSION['signup_data'])){
    header("Location: signup.php");
    exit();
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    $user_otp = trim($_POST['otp']);
    $data = $_SESSION['signup_data'];

    if($user_otp == $data['otp']){
        // Check if user already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s",$data['email']);
        $check->execute();
        $r = $check->get_result();

        if($r->num_rows > 0){
            // User already exists → redirect to login with flash message
            unset($_SESSION['signup_data']);
            header("Location: login.php?already=1");
            exit();
        } else {
            // Insert user into DB after verification
            $stmt = $conn->prepare("INSERT INTO users(first_name,last_name,email,password,is_verified) VALUES(?,?,?,?,1)");
            $stmt->bind_param("ssss",$data['first_name'],$data['last_name'],$data['email'],$data['password']);
            if($stmt->execute()){
                unset($_SESSION['signup_data']);
                header("Location: login.php?verified=1");
                exit();
            } else {
                $error = "DB Insert failed!";
            }
        }
        $check->close();
    } else {
        $error = "Invalid OTP!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Verification</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f0f4f8;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.verify-container {
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    width: 350px;
    text-align: center;
}
.verify-container h2 { margin-bottom: 20px; color: #333; }
.verify-container input[type="text"] {
    width: 100%; padding: 12px 15px; margin-bottom: 20px;
    border-radius: 6px; border: 1px solid #ccc; font-size: 16px;
}
.verify-container button {
    width: 100%; padding: 12px; background: #007bff;
    color: #fff; border: none; border-radius: 6px; font-size: 16px;
    cursor: pointer; transition: 0.3s;
}
.verify-container button:hover { background: #0056b3; }
.error-message { color: #d9534f; margin-bottom: 15px; font-weight: bold; }
</style>
</head>
<body>
<div class="verify-container">
    <h2>Email Verification</h2>

    <?php if(isset($error) && $error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="otp" placeholder="Enter 6 digit code" required maxlength="6">
        <button type="submit">Verify</button>
    </form>
</div>
</body>
</html>
