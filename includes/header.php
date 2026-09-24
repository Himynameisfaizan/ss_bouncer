<?php
// Database Connection (Apne credentials ke according change kar lena)
$host = 'localhost';
$dbname = 'ss_bouncer';
$username = 'root'; // Ya aapka db username
$password = ''; // Ya aapka db password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 1. Fetch Contact Details (Phone, Email, Address, Socials)
$stmt = $pdo->query("SELECT * FROM contacts LIMIT 1");
$contact_details = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Fetch Active Header Logo
// Query for active header logo. Fallback to a default if not found.
$stmt_logo = $pdo->query("SELECT logo_path FROM logos WHERE location = 'header' AND is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
$logo_data = $stmt_logo->fetch(PDO::FETCH_ASSOC);
$header_logo = $logo_data ? $logo_data['logo_path'] : 'default_logo.png'; // Agar active header logo na mile toh default


// 3. Fetch Services for Dropdown Menu
$stmt_services = $pdo->query("SELECT id, service_name FROM services ORDER BY service_name ASC");
$services_list = $stmt_services->fetchAll(PDO::FETCH_ASSOC);

// Current Page URL (For Active Link Logic)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SS Bouncers - Premium Security Services</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/include.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- ==================== TOPBAR ==================== -->
    <div class="topbar py-2 d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center">
                <!-- Contact Details (Dynamic) -->
                <div class="col-md-9 topbar-contact">
                    <ul class="list-inline mb-0 d-flex align-items-center gap-4">
                        <?php if(!empty($contact_details['phone'])): ?>
                        <li class="list-inline-item">
                            <i class="fa-solid fa-phone-volume accent-icon"></i> 
                            <a href="tel:<?= htmlspecialchars($contact_details['phone']) ?>"><?= htmlspecialchars($contact_details['phone']) ?></a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if(!empty($contact_details['email'])): ?>
                        <li class="list-inline-item">
                            <i class="fa-solid fa-envelope accent-icon"></i> 
                            <a href="mailto:<?= htmlspecialchars($contact_details['email']) ?>"><?= htmlspecialchars($contact_details['email']) ?></a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if(!empty($contact_details['address'])): ?>
                        <li class="list-inline-item">
                            <i class="fa-solid fa-location-dot accent-icon"></i> 
                            <span><?= htmlspecialchars($contact_details['address']) ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- Social Links (Dynamic) -->
                <div class="col-md-3 text-end topbar-social">
                    <?php if(!empty($contact_details['facebook'])): ?>
                        <a href="<?= htmlspecialchars($contact_details['facebook']) ?>" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($contact_details['instagram'])): ?>
                        <a href="<?= htmlspecialchars($contact_details['instagram']) ?>" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($contact_details['twitter'])): ?>
                        <a href="<?= htmlspecialchars($contact_details['twitter']) ?>" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($contact_details['linkdin'])): ?>
                        <a href="<?= htmlspecialchars($contact_details['linkdin']) ?>" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== HEADER / NAVBAR ==================== -->
    <header class="main-header sticky-top" id="header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <!-- Logo (Dynamic) -->
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                   <img src="admin/uploads/<?= htmlspecialchars($header_logo) ?>" alt="SS Bouncers Logo" class="header-logo-img">
                </a>
                
                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Links -->
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_page == 'index.php' || $current_page == '') ? 'active' : '' ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_page == 'about.php') ? 'active' : '' ?>" href="about.php">About Us</a>
                        </li>
                        
                        <!-- Services Dropdown (Dynamic) -->
                        <li class="nav-item dropdown custom-dropdown">
                            <a class="nav-link dropdown-toggle <?= ($current_page == 'services.php' || $current_page == 'service_details.php') ? 'active' : '' ?>" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Services
                            </a>
                            <ul class="dropdown-menu shadow-sm" aria-labelledby="servicesDropdown">
                                <?php if(!empty($services_list)): ?>
                                    <?php foreach($services_list as $service): ?>
                                        <!-- Assuming you will have a service_details.php page that takes an ID -->
                                        <li><a class="dropdown-item" href="service_details.php?id=<?= $service['id'] ?>"><?= htmlspecialchars($service['service_name']) ?></a></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="#">No Services Found</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($current_page == 'gallery.php') ? 'active' : '' ?>" href="gallery.php">Gallery</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_page == 'contact.php') ? 'active' : '' ?>" href="contact.php">Contact Us</a>
                        </li>
                    </ul>
                    <!-- CTA Button -->
                    <div class="d-flex align-items-center">
                        <a href="tel:<?= htmlspecialchars($contact_details['phone'] ?? '+917097059293') ?>" class="btn btn-premium shadow-sm">
                            <span>Get A Quote</span> <i class="fa-solid fa-arrow-right-long ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sticky Header Scroll Effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>