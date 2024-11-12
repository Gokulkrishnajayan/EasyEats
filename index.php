<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyEats - Landing Page</title>
    <link rel="stylesheet" href="css/index.css"> <!-- Link to your CSS -->
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="#">EasyEats</a>
        </div>
        
        <!-- Hamburger Menu -->
        <div class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <ul class="nav-links">
            <!-- Login Dropdown -->
            <li class="dropdown">
                <a href="#">Login</a>
                <div class="dropdown-content">
                    <a href="user/login.php">User Login</a>
                    <a href="admin/login.php">Admin Login</a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Landing Section -->
    <section class="landing-section">
        <div class="landing-text">
            <h1>Welcome to EasyEats</h1>
            <p>Your one-stop destination for delicious food.</p>
        </div>
    </section>

    <script>
        // Toggle the menu on mobile
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    </script>

</body>  
</html>
