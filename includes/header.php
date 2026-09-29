<?php
// Note: Session aur DB Connection config/connect.php mein pehle hi ho jana chahiye
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Fetch Contact Details
$contact_query_hdr = $conn->query("SELECT * FROM contacts LIMIT 1");
$contact_details = $contact_query_hdr ? $contact_query_hdr->fetch_assoc() : null;

// 2. Fetch Active Header Logo
$logo_query_hdr = $conn->query("SELECT logo_path FROM logos WHERE location = 'header' AND is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
$logo_data = $logo_query_hdr ? $logo_query_hdr->fetch_assoc() : null;
$header_logo = $logo_data ? $site . 'admin/uploads/' . $logo_data['logo_path'] : 'assets/images/default-logo.png'; 

// 3. Fetch Services for Dropdown Menu
$services_query_hdr = $conn->query("SELECT id, service_name, slug_url FROM services WHERE status = 1 ORDER BY display_order ASC, service_name ASC");
$services_list = [];
if ($services_query_hdr && $services_query_hdr->num_rows > 0) {
    while($row = $services_query_hdr->fetch_assoc()) {
        $services_list[] = $row;
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : "SS Bouncers - Premium Security Services" ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <link rel="stylesheet" href="assets/css/contact.css">
    <link rel="stylesheet" href="assets/css/gallery.css">
    <link rel="stylesheet" href="assets/css/include.css">
    <link rel="stylesheet" href="assets/css/product.css">
    <link rel="stylesheet" href="assets/css/service.css">
</head>
<body>

    <!-- ==================== TOPBAR ==================== -->
    <div class="topbar py-2 d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-9 col-md-10 topbar-contact">
                    <ul class="list-inline mb-0 d-flex align-items-center gap-3">
                        <?php if(!empty($contact_details['phone'])): ?>
                        <li class="list-inline-item text-nowrap">
                            <i class="fa-solid fa-phone-volume accent-icon"></i> 
                            <a href="tel:<?= htmlspecialchars($contact_details['phone']) ?>"><?= htmlspecialchars($contact_details['phone']) ?></a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if(!empty($contact_details['email'])): ?>
                        <li class="list-inline-item text-nowrap">
                            <i class="fa-solid fa-envelope accent-icon"></i> 
                            <a href="mailto:<?= htmlspecialchars($contact_details['email']) ?>"><?= htmlspecialchars($contact_details['email']) ?></a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if(!empty($contact_details['address'])): ?>
                        <li class="list-inline-item topbar-address">
                            <i class="fa-solid fa-location-dot accent-icon mt-1"></i> 
                            <span class="address-text" title="<?= htmlspecialchars($contact_details['address']) ?>">
                                <?= htmlspecialchars($contact_details['address']) ?>
                            </span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- Social Links -->
                <div class="col-lg-3 col-md-2 text-end topbar-social">
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
                <!-- Logo -->
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                   <img src="<?= htmlspecialchars($header_logo) ?>" alt="SS Bouncers Logo" class="header-logo-img">
                </a>
                
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
                        
                        <li class="nav-item dropdown custom-dropdown">
                            <a class="nav-link dropdown-toggle <?= ($current_page == 'services.php' || $current_page == 'service-details.php') ? 'active' : '' ?>" href="services.php" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Services
                            </a>
                            <ul class="dropdown-menu shadow-sm" aria-labelledby="servicesDropdown">
                                <?php if(!empty($services_list)): ?>
                                    <?php foreach($services_list as $service): 
                                        $s_slug = !empty($service['slug_url']) ? $service['slug_url'] : preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($service['service_name'])));
                                    ?>
                                        <li><a class="dropdown-item" href="service-details.php?slug=<?= htmlspecialchars($s_slug) ?>"><?= htmlspecialchars($service['service_name']) ?></a></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="#">No Services Found</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($current_page == 'blog.php') ? 'active' : '' ?>" href="blog.php">Blogs</a>
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