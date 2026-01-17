<?php
session_start();
$success_message = '';
$error_message   = '';

// PHPMailer include & use statements
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'mailer/PHPMailer.php';
require 'mailer/SMTP.php';
require 'mailer/Exception.php';

// Handle form submission
if($_SERVER["REQUEST_METHOD"]=="POST"){

    $first_name = trim($_POST['firstName']);
    $last_name  = trim($_POST['lastName']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirmPassword'];

    if(!$first_name || !$last_name || !$email || !$password || !$confirm){
        $error_message="All fields required";
    }
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $error_message="Invalid email";
    }
    elseif($password!=$confirm){
        $error_message="Passwords not matched";
    }
    else{

        // Generate OTP
        $otp = rand(100000,999999);

        // Store user info + otp in session
        $_SESSION['signup_data'] = [
            'first_name'=>$first_name,
            'last_name'=>$last_name,
            'email'=>$email,
            'password'=>password_hash($password,PASSWORD_DEFAULT),
            'otp'=>$otp
        ];

        // Send OTP email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'akshopna123@gmail.com ';
            $mail->Password = 'fryw oxcf thpr rppn ';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('akshopna123@gmail.com ','HealthCare Plus');
            $mail->addAddress($email);
            $mail->Subject = "HealthCare Plus Email Verification";
            $mail->Body = "Hello $first_name,\n\nYour verification code is: $otp\n\nPlease enter this code to complete registration.";

            $mail->send();

            // Redirect to verify page
            header("Location: verify.php");
            exit();

        } catch (Exception $e) {
            $error_message = "Verification email could not be sent. Mailer Error: ".$mail->ErrorInfo;
        }
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - HealthCare Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="logo123.png" alt="HealthCare Plus Logo">
                </div>
                <nav>
                   <ul>
<?php if(isset($_SESSION['user_id'])): ?>
    <li><a href="index.php">Home</a></li>
    <li><a href="doctors.php">Doctors</a></li>
    <li><a href="services.php">Services</a></li>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="logout.php">Logout</a></li>
<?php else: ?>
    <li><a href="index.php">Home</a></li>
    <li><a href="login.php">Login</a></li>
    <li><a href="signup.php">Signup</a></li>
    <li><a href="doctors.php">Doctors</a></li>
    <li><a href="services.php">Services</a></li>
<?php endif; ?>
</ul>

                </nav>
            </div>
        </div>
    </header>
    
    <!-- Main Content - Signup Form -->
    <main class="auth-container">
        <div class="auth-form">
            <div class="form-header">
                <h2>Create an Account</h2>
                <p>Join HealthCare Plus today and take control of your health journey</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>
            
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                <div class="form-group">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" id="firstName" name="firstName" class="form-input" placeholder="Enter your first name" value="<?= isset($first_name) ? htmlspecialchars($first_name) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" id="lastName" name="lastName" class="form-input" placeholder="Enter your last name" value="<?= isset($last_name) ? htmlspecialchars($last_name) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email address" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                </div>
                
                <div class="form-group password-toggle">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Create a strong password">
                    <span class="toggle-password">
                        <i class="far fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-group password-toggle">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" placeholder="Re-enter your password">
                    <span class="toggle-password">
                        <i class="far fa-eye"></i>
                    </span>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms" class="form-checkbox" <?= isset($_POST['terms']) ? 'checked' : '' ?>>
                    <label for="terms">I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></label>
                </div>
                
                <button type="submit" class="btn-primary">Sign Up</button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Log In</a>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>HealthCare Plus</h3>
                    <p>Providing comprehensive healthcare solutions with compassion and excellence. Your health is our priority.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="doctors.php">Doctors</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Health Street, Medical City, HC 12345</li>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope"></i> info@healthcareplus.com</li>
                        <li><i class="fas fa-clock"></i> Mon-Fri: 8AM-8PM, Sat-Sun: 9AM-5PM</li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for the latest health tips and updates.</p>
                    <form>
                        <input type="email" placeholder="Your email address" style="padding: 10px; width: 100%; margin-bottom: 10px; border-radius: 5px; border: none;">
                        <button type="submit" class="btn-primary" style="width: 100%;">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2023 HealthCare Plus. All rights reserved. Created by Amena Khanom Shopna</p>
            </div>
        </div>
    </footer>

    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.toggle-password');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const passwordField = this.previousElementSibling;
                    const icon = this.querySelector('i');
                    
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        icon.classList.remove('far', 'fa-eye');
                        icon.classList.add('fas', 'fa-eye-slash');
                    } else {
                        passwordField.type = 'password';
                        icon.classList.remove('fas', 'fa-eye-slash');
                        icon.classList.add('far', 'fa-eye');
                    }
                });
            });
        });
    </script>
</body>
</html>