<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - HealthCare Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
   <link rel="stylesheet" href="view_appointments.css">
</head>
<body>
    <?php require_once 'db.php'; ?>
    
    <!-- Check if user is logged in -->
    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    ?>

    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="logo123.png" alt="HealthCare Plus Logo">
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="signup.php">Signup</a></li>
                        <li><a href="doctors.php">Doctors</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="dashboard.php">Dashboard</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content - View All Appointments -->
    <main class="appointments-container">
        <div class="filters-section">
            <div class="filter-header">
                <h3>
                    <i class="fas fa-calendar-check"></i>
                    My Appointments
                </h3>
            </div>
            
           <div class="appointments-list">
    <?php
    // Query to get appointments for the logged-in user
    $sql = "SELECT a.*, d.name AS doctor_name FROM appointments a 
            JOIN doctors d ON a.doctor_id = d.id 
            WHERE a.user_id = ? 
            ORDER BY a.appointment_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Determine status class for styling
            $statusClass = '';
            switch ($row['status']) {
                case 'scheduled':
                    $statusClass = 'status-scheduled';
                    break;
                case 'completed':
                    $statusClass = 'status-completed';
                    break;
                case 'cancelled':
                    $statusClass = 'status-cancelled';
                    break;
            }

            echo '<div class="appointment-card">';
            echo '<div class="appointment-header">';
            echo '<div class="appointment-doctor"><strong>Doctor:</strong> ' . htmlspecialchars($row['doctor_name']) . '</div>';
            echo '<div class="appointment-date"><strong>Date:</strong> ' . date('F j, Y', strtotime($row['appointment_date'])) . '</div>';
            echo '<div class="appointment-time"><strong>Time:</strong> ' . date('g:i A', strtotime($row['appointment_time'])) . ' - ' . date('g:i A', strtotime($row['appointment_time'] . '+30 minutes')) . '</div>';
            echo '<div class="appointment-status ' . $statusClass . '"><strong>Status:</strong> ' . ucfirst($row['status']) . '</div>';
            echo '</div>'; // header

            // Additional info
            echo '<div class="appointment-details">';
            echo '<p><strong>Contact Number:</strong> ' . htmlspecialchars($row['contact_number']) . '</p>';
            echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>';
            echo '<p><strong>Appointment Type:</strong> ' . ucfirst($row['appointment_type']) . '</p>';
            if (!empty($row['notes'])) {
                echo '<p><strong>Notes:</strong> ' . htmlspecialchars($row['notes']) . '</p>';
            }
            echo '</div>';

            // Actions
            if ($row['status'] == 'scheduled') {
                echo '<div class="appointment-actions">';
                echo '<form method="POST" action="cancel_appointment.php">';
                echo '<input type="hidden" name="appointment_id" value="' . $row['id'] . '">';
                echo '<button type="submit" class="action-button cancel">';
                echo '<i class="fas fa-times"></i> Cancel';
                echo '</button>';
                echo '</form>';
                echo '</div>';
            }

            echo '</div>'; // card
        }
    } else {
        echo '<div class="no-appointments">';
        echo '<i class="fas fa-calendar-times"></i>';
        echo '<p>No appointments found</p>';
        echo '</div>';
    }

    $stmt->close();
    $conn->close();
    ?>
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
                <p>&copy; 2025 HealthCare Plus. All rights reserved. Created by Amena Khanom Shopna</p>
            </div>
        </div>
    </footer>
</body>
</html>