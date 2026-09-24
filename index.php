<?php
require_once 'config/connect.php';

$banner_query = "SELECT * FROM banners WHERE status = 0 ORDER BY display_order ASC, id DESC";
$result_banners = $conn->query($banner_query);

$banners = [];
if ($result_banners && $result_banners->num_rows > 0) {
    while ($row = $result_banners->fetch_assoc()) {
        $banners[] = $row;
    }
}

$categories_res = false;
if (isset($conn)) {
    $categories_res = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1 ORDER BY id DESC LIMIT 3");
}

$products_res = false;
if (isset($conn)) {
    $products_res = mysqli_query($conn, "SELECT * FROM products WHERE status = 1 ORDER BY id DESC LIMIT 8");
}

// Fetch Testimonials
// $test_res = false;
// if (isset($conn)) {
//     $test_res = mysqli_query($conn, "SELECT * FROM testimonials WHERE status = 1 ORDER BY test_id DESC LIMIT 2");
// }

// Fetch About Section Data
$about_res = false;
$about_data = [];
if (isset($conn)) {
    $about_query = mysqli_query($conn, "SELECT * FROM about_sections ORDER BY section_order ASC LIMIT 1");
    if ($about_query && mysqli_num_rows($about_query) > 0) {
        $about_data = mysqli_fetch_assoc($about_query);
    }
}

$brands_array = [];
if (isset($conn)) {
    $brands_res = mysqli_query($conn, "SELECT * FROM brands ORDER BY id DESC");
    if ($brands_res && mysqli_num_rows($brands_res) > 0) {
        while ($brand = mysqli_fetch_assoc($brands_res)) {
            $brands_array[] = $brand;
        }
    }
}

$currentPage = basename($_SERVER['PHP_SELF']);

$seo_meta_query = mysqli_query($conn, "SELECT meta_title, meta_key, meta_desc FROM meta WHERE page_url = '$currentPage'");

if ($seo_meta_query && mysqli_num_rows($seo_meta_query) > 0) {
    $seo_data = mysqli_fetch_assoc($seo_meta_query);

    $pageTitle = $seo_data['meta_title'];
    $meta_keywords = $seo_data['meta_key'];
    $meta_description = $seo_data['meta_desc'];
} else {
    $pageTitle = "Bhagirath Enterprise";
    $meta_keywords = "export, agricultural products";
    $meta_description = "Bhagirath Enterprise Export Company.";
}

include("includes/header.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta_keywords); ?>">
    <link rel="icon" href="<?= htmlspecialchars($favicon); ?>" type="image/png">
    <!-- Organization & Local Business Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "Bhagirath Enterprise",
            "url": "<?= $site; ?>",
            "logo": "<?= $site; ?> /
                assets / images / logo / logo.png ",
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+91-8448211202",
                "contactType": "customer service",
                "areaServed": "IN",
                "availableLanguage": ["en", "hi"]
            },
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Office No-102, 1st Floor, Nitika Tower II, Block C-1, Pocket-4, Azadpur",
                "addressLocality": "Delhi",
                "postalCode": "110033",
                "addressCountry": "IN"
            },
            "sameAs": [
                "https://www.facebook.com",
                "https://www.linkedin.com"
            ]
        }
    </script>
</head>

<body>

    <!-- ==================== HERO SLIDER ==================== -->
    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

            <!-- Indicators -->
            <div class="carousel-indicators">
                <?php
                if (!empty($banners)):
                    $i = 0;
                    foreach ($banners as $banner):
                ?>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i == 0 ? 'active' : '' ?>" aria-current="<?= $i == 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                <?php
                        $i++;
                    endforeach;
                endif;
                ?>
            </div>

            <div class="carousel-inner">
                <?php
                if (!empty($banners)):
                    $count = 0;
                    foreach ($banners as $banner):
                        $isActive = ($count == 0) ? 'active' : '';
                        $btn_link = !empty($banner['link_url']) ? $banner['link_url'] : 'services.php';
                ?>
                        <div class="carousel-item <?= $isActive ?>">
                            <div class="hero-bg-image" style="background-image: url('<?= $site ?>admin/<?= htmlspecialchars($banner['banner_path']) ?>');">
                                <div class="hero-overlay"></div>
                            </div>

                            <div class="container h-100">
                                <div class="row h-100 align-items-center justify-content-center text-center">
                                    <div class="col-lg-10">
                                        <?php if (!empty($banner['title'])): ?>
                                            <h1 class="hero-title text-white mb-4"><?= htmlspecialchars($banner['title']) ?></h1>
                                        <?php endif; ?>

                                        <?php if (!empty($banner['description'])): ?>
                                            <p class="hero-subtitle text-white-50 mb-5 mx-auto"><?= htmlspecialchars($banner['description']) ?></p>
                                        <?php endif; ?>

                                        <div class="hero-actions">
                                            <a href="<?= htmlspecialchars($btn_link) ?>" class="btn btn-premium btn-lg me-3">
                                                Explore Services
                                            </a>
                                            <a href="contact.php" class="btn btn-outline-light btn-lg">
                                                Contact Us
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                        $count++;
                    endforeach;
                else:
                    ?>
                    <!-- Fallback Static Slide -->
                    <div class="carousel-item active">
                        <div class="hero-bg-image" style="background-color: var(--primary-color);"></div>
                        <div class="container h-100">
                            <div class="row h-100 align-items-center justify-content-center text-center">
                                <div class="col-lg-8">
                                    <h1 class="hero-title text-white mb-4">WE PROVIDE VERIFIED & SECURED SERVICE</h1>
                                    <p class="hero-subtitle text-white-50 mb-5">Professional security guard and housekeeping services tailored for corporates, industries, and residential sectors.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($banners) && count($banners) > 1): ?>
                <button class="carousel-control-prev custom-carousel-btn" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next custom-carousel-btn" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            <?php endif; ?>
        </div>
    </section>

    <?php
    // Note: Ensure config/connect.php is already included at the top of the file

    // 1. Fetch About Us Data
    $about_query = "SELECT * FROM about_us";
    $result_about = $conn->query($about_query);
    $about_data = $result_about ? $result_about->fetch_assoc() : null;

    // 2. Fetch Services Data (Limit 6 for Homepage)
    $services_query = "SELECT * FROM services ORDER BY id ASC LIMIT 6";
    $result_services = $conn->query($services_query);

    // 3. Fetch Contact Data for Call/WhatsApp
    $contact_query = "SELECT phone, wp_number FROM contacts LIMIT 1";
    $result_contact = $conn->query($contact_query);
    $contact_info = $result_contact ? $result_contact->fetch_assoc() : ['phone' => '', 'wp_number' => ''];

    // Format WhatsApp Number (Remove spaces, +, etc. for wa.me link)
    $wa_number = preg_replace('/[^0-9]/', '', $contact_info['wp_number']);

    // Helper function to generate slug from service name
    function createSlug($string)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }
    ?>

    <!-- ==================== ABOUT US SECTION ==================== -->
    <section class="about-section py-5">
        <div class="container py-4">
            <div class="row align-items-center gy-5">
                <!-- About Image with Premium Frame -->
                <div class="col-lg-6">
                    <div class="about-image-wrapper position-relative">
                        <div class="about-experience-badge">
                            <span class="years">10+</span>
                            <span class="text">Years of<br>Excellence</span>
                        </div>
                        <?php if ($about_data && !empty($about_data['image_url'])): ?>
                            <img src="<?= $site ?>admin/uploads/<?= htmlspecialchars($about_data['image_url']) ?>" alt="About SS Bouncers" class="img-fluid about-img rounded shadow-lg">
                        <?php else: ?>
                            <img src="assets/images/banner/1.jpeg" alt="About SS Bouncers" class="img-fluid about-img rounded shadow-lg">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- About Content -->
                <div class="col-lg-6 ps-lg-5">
                    <div class="section-title mb-4">
                        <span class="sub-heading">About Our Company</span>
                        <h2 class="main-heading">Premium Security & Protection You Can Trust</h2>
                    </div>

                    <div class="about-content text-muted mb-4">
                        <?php
                        if ($about_data && !empty($about_data['content'])) {
                            // Cleaning HTML and truncating for homepage display
                            $clean_about = strip_tags($about_data['content']);
                            echo mb_strimwidth($clean_about, 0, 350, "...");
                        } else {
                            echo "We are a premier security agency specializing in providing highly trained and professional security personnel for various sectors.";
                        }
                        ?>
                    </div>

                    <div class="row mb-4 gy-3">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-circle-check text-secondary-accent me-3 fs-4"></i>
                                <span class="fw-bold text-primary-dark">Verified Guards</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-headset text-secondary-accent me-3 fs-4"></i>
                                <span class="fw-bold text-primary-dark">24/7 Active Support</span>
                            </div>
                        </div>
                    </div>

                    <a href="about.php" class="btn btn-premium mt-2">Discover More <i class="fa-solid fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== SERVICES SECTION ==================== -->
    <section class="services-section py-5 bg-light-custom">
        <div class="container py-4">
            <!-- Section Header -->
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <div class="section-title">
                        <span class="sub-heading">What We Offer</span>
                        <h2 class="main-heading">Comprehensive Security Solutions</h2>
                        <p class="text-muted mt-3">Professional security guard and housekeeping services tailored for corporates, industries, and residential sectors.</p>
                    </div>
                </div>
            </div>

            <!-- Services Grid -->
            <div class="row gy-4">
                <?php
                if ($result_services && $result_services->num_rows > 0):
                    while ($service = $result_services->fetch_assoc()):
                        // Generate Slug for URL
                        $service_slug = createSlug($service['service_name']);
                        $service_url = "service-details.php?slug=" . $service_slug;

                        // Pre-filled WhatsApp Message
                        $wa_message = urlencode("Hi SS Bouncers, I am interested in your " . $service['service_name'] . ".");
                ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="service-card h-100">
                                <!-- Image linked to details -->
                                <a href="<?= $service_url ?>" class="service-img-link overflow-hidden d-block">
                                    <!-- Note: Assuming service images are in admin/uploads/services/ -->
                                    <img src="<?= $site ?>admin/assets/img/uploads/<?= htmlspecialchars($service['img_path']) ?>" alt="<?= htmlspecialchars($service['service_name']) ?>" class="img-fluid service-img w-100">
                                </a>

                                <div class="service-content p-4">
                                    <div class="service-icon mb-3">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </div>

                                    <!-- Title linked to details -->
                                    <a href="<?= $service_url ?>" class="text-decoration-none">
                                        <h3 class="service-title mb-3"><?= htmlspecialchars($service['service_name']) ?></h3>
                                    </a>

                                    <p class="service-desc text-muted mb-4">
                                        <?= htmlspecialchars(mb_strimwidth($service['short_desc'], 0, 120, "...")) ?>
                                    </p>

                                    <!-- Card Footer: Details Link + Call/WA Actions -->
                                    <div class="service-footer d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                                        <a href="<?= $service_url ?>" class="read-more-link fw-bold">
                                            Read More <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </a>

                                        <!-- Action Buttons -->
                                        <div class="service-actions d-flex gap-2">
                                            <!-- Direct Call -->
                                            <a href="tel:<?= htmlspecialchars($contact_info['phone']) ?>" class="action-btn call-btn" title="Call Now">
                                                <i class="fa-solid fa-phone"></i>
                                            </a>
                                            <!-- Direct WhatsApp -->
                                            <a href="https://wa.me/<?= $wa_number ?>?text=<?= $wa_message ?>" target="_blank" class="action-btn wa-btn" title="WhatsApp Now">
                                                <i class="fa-brands fa-whatsapp"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    endwhile;
                else:
                    echo "<p class='text-center'>No services found.</p>";
                endif;
                ?>
            </div>

            <!-- View All Button -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="services.php" class="btn btn-outline-primary-custom btn-lg">View All Services</a>
                </div>
            </div>
        </div>
    </section>

<?php
// Note: $conn should be active from config/connect.php included at the top

// Fetch Testimonials Data (Dynamic)
$testi_query = "SELECT * FROM testimonials ORDER BY display_order ASC, id DESC LIMIT 5";
$result_testi = $conn->query($testi_query);
?>

<!-- ==================== WHY CHOOSE US (STATIC) ==================== -->
<section class="why-choose-us-section py-5">
    <div class="container py-5">
        <div class="row align-items-center gy-5">
            
            <!-- Left Side: Content -->
            <div class="col-lg-6 pe-lg-5">
                <div class="section-title mb-4">
                    <span class="sub-heading">Why Choose Us</span>
                    <h2 class="main-heading">Your Safety Is Our Top Priority</h2>
                    <p class="text-muted mt-3">We bring trust and professional expertise directly to your premises, ensuring comprehensive protection for you and your assets.</p>
                </div>

                <div class="feature-list mt-4">
                    <!-- Feature 1 -->
                    <div class="feature-box d-flex mb-4">
                        <div class="feature-icon me-4">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div class="feature-content">
                            <h4 class="fw-bold text-primary-dark mb-2">24/7 Support</h4>
                            <p class="text-muted mb-0">Always active emergency response team ready to assist you around the clock.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="feature-box d-flex mb-4">
                        <div class="feature-icon me-4">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div class="feature-content">
                            <h4 class="fw-bold text-primary-dark mb-2">Verified Service</h4>
                            <p class="text-muted mb-0">Strict background checks and rigorous verification for all our security guards.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="feature-box d-flex">
                        <div class="feature-icon me-4">
                            <i class="fa-solid fa-medal"></i>
                        </div>
                        <div class="feature-content">
                            <h4 class="fw-bold text-primary-dark mb-2">Experienced Team</h4>
                            <p class="text-muted mb-0">Highly trained and disciplined personnel equipped for multi-sector security challenges.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Image / Visual -->
            <div class="col-lg-6">
                <div class="why-choose-img-wrapper position-relative">
                    <div class="premium-shape-bg"></div>
                    <!-- Ensure you have a good placeholder image or change the path -->
                    <img src="assets/images/banner/1.jpg" alt="Why Choose SS Bouncers" class="img-fluid rounded-3 shadow-lg position-relative w-100 object-fit-cover" style="height: 500px;">
                    
                    <!-- Floating Badge -->
                    <div class="satisfaction-badge d-flex align-items-center bg-white p-3 rounded shadow">
                        <div class="icon-box bg-secondary-accent text-primary-dark rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px; font-size: 20px;">
                            <i class="fa-solid fa-thumbs-up"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary-dark">100%</h5>
                            <span class="text-muted" style="font-size: 14px;">Client Satisfaction</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>


<!-- ==================== TESTIMONIALS (DYNAMIC) ==================== -->
<section class="testimonials-section py-5">
    <div class="container py-5">
        
        <!-- Section Header (Centered) -->
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <div class="section-title">
                    <span class="sub-heading text-secondary-accent">Client Reviews</span>
                    <h2 class="main-heading text-white">What Our Clients Say</h2>
                    <p class="text-white-50 mt-3">See what our clients have to say about our premium security services and our commitment to excellence.</p>
                </div>
            </div>
        </div>

        <!-- Testimonials Carousel -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner text-center px-lg-5">
                        
                        <?php 
                        if($result_testi && $result_testi->num_rows > 0): 
                            $count = 0;
                            while($testi = $result_testi->fetch_assoc()): 
                                $isActive = ($count == 0) ? 'active' : '';
                        ?>
                        <div class="carousel-item <?= $isActive ?>">
                            <div class="testimonial-content">
                                <!-- Quote Icon -->
                                <i class="fa-solid fa-quote-left display-4 text-secondary-accent mb-4 opacity-50"></i>
                                
                                <!-- Testimonial Text -->
                                <p class="testimonial-text lead fst-italic text-white mb-4 px-md-5">
                                    "<?= htmlspecialchars($testi['testimonial_text']) ?>"
                                </p>
                                
                                <!-- Client Info -->
                                <div class="client-info mt-4">
                                    <div class="client-initial rounded-circle bg-secondary-accent text-primary-dark d-inline-flex align-items-center justify-content-center mb-3 fw-bold fs-4" style="width: 60px; height: 60px;">
                                        <?= strtoupper(substr($testi['client_name'], 0, 1)) ?>
                                    </div>
                                    <h5 class="text-secondary-accent fw-bold mb-1"><?= htmlspecialchars($testi['client_name']) ?></h5>
                                    
                                    <?php if(!empty($testi['client_company'])): ?>
                                        <span class="text-white-50 small"><?= htmlspecialchars($testi['client_company']) ?></span>
                                    <?php endif; ?>
                                    
                                    <!-- Star Ratings -->
                                    <div class="star-rating mt-2">
                                        <?php 
                                        $rating = !empty($testi['rating']) ? (int)$testi['rating'] : 5;
                                        for($i = 1; $i <= 5; $i++) {
                                            if($i <= $rating) {
                                                echo '<i class="fa-solid fa-star text-secondary-accent"></i> ';
                                            } else {
                                                echo '<i class="fa-regular fa-star text-secondary-accent"></i> ';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            $count++;
                            endwhile; 
                        else:
                        ?>
                        <!-- Fallback if no testimonials exist -->
                        <div class="carousel-item active">
                            <p class="text-white">No reviews available at the moment.</p>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                    
                    <!-- Custom Carousel Controls -->
                    <?php if($result_testi && $result_testi->num_rows > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev" style="width: 50px;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next" style="width: 50px;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    
                    <!-- Carousel Indicators (Dots) -->
                    <div class="carousel-indicators position-relative mt-5 mb-0">
                        <?php for($i = 0; $i < $result_testi->num_rows; $i++): ?>
                            <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i == 0 ? 'active' : '' ?>" aria-current="<?= $i == 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    </div>
</section>

<?php
// Note: Ensure $conn and $site are available from your connect.php

// 1. Fetch Client Logos (Brands)
$brand_query = "SELECT * FROM brands ORDER BY id DESC";
$result_brands = $conn->query($brand_query);

// 2. Fetch Latest 3 Published Blogs
$blog_query = "SELECT * FROM blogs WHERE status = 1 ORDER BY created_at DESC LIMIT 3";
$result_blogs = $conn->query($blog_query);

// 3. Fetch Map URL from Contacts
$contact_query2 = "SELECT map FROM contacts LIMIT 1";
$result_contact2 = $conn->query($contact_query2);
$contact_data = $result_contact2 ? $result_contact2->fetch_assoc() : null;
$map_url = !empty($contact_data['map']) ? $contact_data['map'] : '';
?>

<!-- ==================== CLIENT LOGOS (DYNAMIC) ==================== -->
<section class="client-logos-section py-5 border-top border-bottom">
    <div class="container py-3">
        <div class="text-center mb-4">
            <h5 class="text-muted fw-bold text-uppercase tracking-wider">Trusted by Leading Companies</h5>
        </div>
        
        <div class="logo-slider-container overflow-hidden position-relative">
            <div class="logo-slider-track d-flex align-items-center">
                <?php 
                if($result_brands && $result_brands->num_rows > 0): 
                    $brands = [];
                    while($brand = $result_brands->fetch_assoc()){
                        $brands[] = $brand;
                    }
                    // Loop twice for infinite scroll effect
                    for($i=0; $i<2; $i++):
                        foreach($brands as $brand):
                ?>
                <div class="logo-slide mx-4">
                    <img src="<?= $site ?>admin/<?= htmlspecialchars($brand['logo_path']) ?>" alt="<?= htmlspecialchars($brand['brand_name']) ?>" class="img-fluid client-logo-img grayscale-hover">
                </div>
                <?php 
                        endforeach;
                    endfor;
                else: 
                ?>
                    <p class="text-center w-100 text-muted">No client logos found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ==================== RECENT NEWS & BLOG (DYNAMIC) ==================== -->
<section class="blog-section py-5 bg-light-custom">
    <div class="container py-5">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <div class="section-title">
                    <span class="sub-heading">Recent News & Blog</span>
                    <h2 class="main-heading">Stay Updated With Security Insights</h2>
                    <p class="text-muted mt-3">Read our latest articles on safety, corporate security, and housekeeping tips.</p>
                </div>
            </div>
        </div>

        <div class="row gy-4">
            <?php 
            if($result_blogs && $result_blogs->num_rows > 0):
                while($blog = $result_blogs->fetch_assoc()):
                    // Truncate content for excerpt
                    $blog_excerpt = mb_strimwidth(strip_tags($blog['description']), 0, 100, "...");
                    $blog_date = date('d M, Y', strtotime($blog['created_at']));
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="blog-card card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="blog-img-wrapper position-relative overflow-hidden">
                        <!-- Assuming blog images are in admin/uploads/blogs/ -->
                        <img src="<?= $site ?>admin/assets/img/uploads/blogs/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="card-img-top blog-img">
                        <div class="blog-date badge bg-secondary-accent text-primary-dark position-absolute top-0 start-0 m-3 p-2 rounded-3 shadow">
                            <span class="fw-bold"><?= $blog_date ?></span>
                        </div>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <h4 class="card-title fw-bold text-primary-dark mb-3">
                            <a href="blog-details.php?slug=<?= htmlspecialchars($blog['slug']) ?>" class="text-decoration-none text-primary-dark blog-title-link">
                                <?= htmlspecialchars($blog['title']) ?>
                            </a>
                        </h4>
                        <p class="card-text text-muted mb-4 flex-grow-1">
                            <?= $blog_excerpt ?>
                        </p>
                        <a href="blog-details.php?slug=<?= htmlspecialchars($blog['slug']) ?>" class="read-more-link fw-bold mt-auto">
                            Read More <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="col-12 text-center"><p>No blogs published yet.</p></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ==================== INQUIRY & MAP SECTION ==================== -->
<section class="inquiry-section py-5">
    <div class="container py-5">
        <div class="row bg-white rounded-4 shadow-lg overflow-hidden">
            
            <!-- Left Side: Google Map -->
            <div class="col-lg-6 p-0 map-wrapper">
                <?php if(!empty($map_url)): ?>
                    <iframe src="<?= htmlspecialchars($map_url) ?>" width="100%" height="100%" style="border:0; min-height: 400px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light" style="min-height: 400px;">
                        <span class="text-muted">Map not available</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Side: Inquiry Form -->
            <div class="col-lg-6 p-5">
                <div class="section-title mb-4">
                    <span class="sub-heading">Get A Free Quote</span>
                    <h2 class="main-heading fs-3">Request Security Service</h2>
                </div>
                
                <!-- Send data to submit_inquiry.php using POST -->
                <form action="submit_inquiry.php" method="POST" class="inquiry-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control custom-input" name="name" id="name" placeholder="Full Name" required>
                                <label for="name">Full Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control custom-input" name="email" id="email" placeholder="Email Address" required>
                                <label for="email">Email Address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control custom-input" name="phone" id="phone" placeholder="Phone Number" required>
                                <label for="phone">Phone Number</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control custom-input" name="subject" id="subject" placeholder="Subject" required>
                                <label for="subject">Subject</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <textarea class="form-control custom-input" name="message" id="message" placeholder="How can we help you?" style="height: 120px" required></textarea>
                                <label for="message">How can we help you?</label>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-premium w-100 py-3 rounded-3">
                                Send Message <i class="fa-solid fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</section>

<!-- ==================== FAQ SECTION (STATIC) ==================== -->
<section class="faq-section py-5 bg-light-custom">
    <div class="container py-5">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <div class="section-title">
                    <span class="sub-heading">FAQ's</span>
                    <h2 class="main-heading">Frequently Asked Questions</h2>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion custom-accordion" id="faqAccordion">
                    
                    <!-- FAQ 1 -->
                    <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button rounded-3 fw-bold text-primary-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Are your security guards verified and trained?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted pt-0 pb-4 px-4">
                                Yes, absolutely. All our security personnel undergo a strict background verification process and receive comprehensive training before deployment. We ensure they are disciplined, professional, and capable of handling various security situations effectively.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 2 -->
                    <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed rounded-3 fw-bold text-primary-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Do you provide 24/7 security services?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted pt-0 pb-4 px-4">
                                Yes, we offer round-the-clock security solutions tailored to your needs. Whether you need day shifts, night shifts, or 24/7 continuous monitoring, our teams are always available to protect your premises.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 3 -->
                    <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed rounded-3 fw-bold text-primary-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                In which areas do you provide security and housekeeping services?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted pt-0 pb-4 px-4">
                                We primarily provide our premium security guard and housekeeping services across Hyderabad and surrounding regions. Please contact our support team for specific location inquiries.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const reveals = document.querySelectorAll(".reveal");
            const revealOnScroll = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("active");
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.15
            });

            reveals.forEach(reveal => revealOnScroll.observe(reveal));
        });
    </script>

    <?php include('includes/footer.php'); ?>