<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sri Sai Security Services</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/include.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <link rel="stylesheet" href="assets/css/services.css">
    <link rel="stylesheet" href="assets/css/blog.css">

</head>

<body>

    <!-- Top Bar -->
    <div class="bg-dark-blue text-light py-2 top-bar d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center">
                <!-- Contact Info -->
                <div class="col-md-7">
                    <span class="me-4"><i class="fas fa-phone-alt text-primary-custom me-2"></i><a style="color:white; text-decoration:none;" href="tel:+917200864976"> +91 7200864976</a></span>
                    <span><i class="fas fa-envelope text-primary-custom me-2"></i><a style="color:white; text-decoration:none;" href="mailto:srisaiss505@gmail.com"> srisaiss505@gmail.com</a></span>
                </div>
                <!-- Address & Translate Dropdown -->
                <div class="col-md-5 d-flex justify-content-end align-items-center">
                    <span class="me-3"><i class="fas fa-map-marker-alt text-primary-custom me-2"></i> Thiruvanmiyur,
                        Chennai</span>

                    <!-- Google Translate Div -->
                    <div id="google_translate_element"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <?php
    // Current page ka naam fetch karne ke liye (e.g., 'about.php')
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>

    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-1">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/images/logo/logo.jpg" alt="Sri Sai Security Logo" class="me-3"
                    style="width: 80px; height: 80px; object-fit: contain; border-radius: 50%;">
                <div>
                    <span class="d-block fs-4 text-dark fw-bold">Sri Sai</span>
                    <span class="d-block fs-6 text-muted fw-normal">Security Services</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>"
                            href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>"
                            href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <!-- Agar user services ya service details page par hai, toh Services menu active rahega -->
                        <a class="nav-link <?php echo ($currentPage == 'services.php' || $currentPage == 'service-details.php') ? 'active' : ''; ?>"
                            href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <!-- Agar user blog ya blog details page par hai, toh Blog menu active rahega -->
                        <a class="nav-link <?php echo ($currentPage == 'blog.php' || $currentPage == 'blog-details.php') ? 'active' : ''; ?>"
                            href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>"
                            href="contact.php">Contact Us</a>
                    </li>
                </ul>
                <a href="contact.php" class="btn btn-primary-custom"><i class="fas fa-headset me-2"></i> Get a Quote</a>
            </div>
        </div>
    </nav>