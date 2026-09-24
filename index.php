<?php
include 'includes/header.php';
include 'admin/db-conn.php';
$contact_query = mysqli_query($conn, "SELECT phone, wp_number FROM contacts LIMIT 1");
$contact_data = mysqli_fetch_assoc($contact_query);

$phone_number = !empty($contact_data['phone']) ? $contact_data['phone'] : '917200864976';
$wp_number = !empty($contact_data['wp_number']) ? $contact_data['wp_number'] : '917200864976';

$services_query = mysqli_query($conn, "SELECT * FROM services ORDER BY id ASC LIMIT 6");

?>

<!-- Hero Section -->
<?php
// 1. Fetch Latest Banner from database
$banner_query = mysqli_query($conn, "SELECT banner_path, title, description FROM banners ORDER BY id DESC LIMIT 1");
$banner_data = mysqli_fetch_assoc($banner_query);

// 2. Setup Image Path
// Agar database me banner hai, to uska path banayenge, warna default image show karenge[cite: 1]
if (!empty($banner_data['banner_path'])) {
    $banner_img = 'admin/' . $banner_data['banner_path']; // Ensure path admin folder ko point kare
} else {
    $banner_img = 'assets/images/banner/1.jpeg'; // Fallback image
}

// 3. Dynamic Title & Subtitle (Optional, agar DB se dikhana ho)[cite: 1]
$hero_title = !empty($banner_data['title']) ? htmlspecialchars($banner_data['title']) : "Your Security, Our Priority";
$hero_desc = !empty($banner_data['description']) ? htmlspecialchars($banner_data['description']) : "Providing highly trained professionals for your safety.";
?>

<!-- Hero Section HTML with Inline Dynamic Background -->
<section class="hero-section" style="background: linear-gradient(rgba(11, 21, 40, 0.5), rgba(11, 21, 40, 0.7)), url('<?= $banner_img; ?>') no-repeat center center; background-size: cover; padding:10rem 0;">
    <div class="container text-center text-white">
        <!-- Text content with AOS Animation -->
        <h1 class="display-4 fw-bold mb-4" data-aos="fade-down"><?= $hero_title; ?></h1>
        <p class="lead mb-5" data-aos="fade-up" data-aos-delay="200"><?= $hero_desc; ?></p>
        
        <div data-aos="zoom-in" data-aos-delay="400">
            <a href="services.php" class="btn btn-primary-custom btn-lg me-3">Explore Services</a>
            <a href="contact.php" class="btn btn-outline-light btn-lg">Contact Us</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                <h2 class="section-title">About <span>Our Company</span></h2>
                <h5 class="mb-3 text-secondary">Sri Sai Security Services - Owned by Mr. R. Meen Barali</h5>
                <p class="text-muted mb-4">We are a premier security agency based in Thiruvanmiyur, Chennai. We
                    specialize in providing highly trained and professional security personnel for various sectors
                    including corporate offices, industrial warehouses, educational institutes, and multispecialty
                    hospitals.</p>

                <div class="row text-center mt-5">
                    <div class="col-4">
                        <h3 class="fw-bold text-dark">500+</h3>
                        <p class="text-muted small">Happy Clients</p>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold text-dark">24/7</h3>
                        <p class="text-muted small">Active Support</p>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold text-dark">150+</h3>
                        <p class="text-muted small">Expert Guards</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="assets/images/services/1.jpeg" alt="Security Team" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">What We <span>Offer Here</span></h2>
            <p class="text-muted">Comprehensive security and housekeeping solutions for every need.</p>
        </div>
        <div class="row g-4">

            <?php
            if (mysqli_num_rows($services_query) > 0) {
                $delay = 100;
                while ($service = mysqli_fetch_assoc($services_query)) {
                    // Dynamic image path setup (admin upload folder ke according adjust karein)
                    $img_src = !empty($service['img_path']) ? 'admin/assets/img/uploads/' . $service['img_path'] : 'assets/images/services/default.jpeg';
                    $service_title = htmlspecialchars($service['service_name']);
                    $short_desc = htmlspecialchars($service['short_desc']);
                    $whatsapp_msg = urlencode("Hello, I am interested in your " . $service['service_name'] . ".");
            ?>
                    <!-- Service Item -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $delay; ?>">
                        <div class="service-card">
                            <a href="service-details.php?id=<?= $service['id']; ?>">
                                <img src="<?= $img_src; ?>" alt="<?= $service_title; ?>" class="service-img"></a>
                            <div class="service-content">
                                <div class="service-icon-box">
                                    <i class="fas fa-shield-alt service-icon"></i>
                                    <a href="service-details.php?id=<?= $service['id']; ?>" class="read-more-link">
                                        <h4 class="service-title"><?= $service_title; ?></h4>
                                    </a>
                                </div>
                                <p class="text-muted small mb-4"><?= $short_desc; ?></p>

                                <div class="service-footer">
                                    <a href="service-details.php?id=<?= $service['id']; ?>" class="read-more-link">
                                        Read More <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                    <div class="px-2 d-flex align-items-center gap-4">
                                        <a href="tel:<?= $phone_number; ?>" target="_blank" class="phone-btn" title="Call Now">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        <a href="https://wa.me/<?= $wp_number; ?>?text=<?= $whatsapp_msg; ?>" target="_blank"
                                            class="whatsapp-btn" title="Chat on WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    $delay += 100; // Animation delay ko har card ke sath increment karna
                }
            } else {
                ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No services found.</p>
                </div>
            <?php } ?>

        </div>
        <div class="text-center mt-5">
            <a href="services.php" class="btn btn-primary-custom">Explore All Services</a>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<?php
$gallery_query = mysqli_query($conn, "SELECT * FROM gallery ORDER BY ID DESC LIMIT 6");
?>

<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Our Gallery <span>Photo</span></h2>
            <p class="text-muted">Glimpses of our professional security personnel on duty.</p>
        </div>

        <div class="row g-4">
            <?php
            if (mysqli_num_rows($gallery_query) > 0) {
                $delay = 100;
                while ($gallery = mysqli_fetch_assoc($gallery_query)) {

                    // Image Path Handling
                    $raw_path = $gallery['image_path'];
                    if (strpos($raw_path, 'admin/') === 0) {
                        $img_src = $raw_path;
                    } elseif (strpos($raw_path, 'uploads/') === 0) {
                        $img_src = 'admin/' . $raw_path;
                    } else {
                        $img_src = 'admin/uploads/gallery/' . $raw_path;
                    }

                    $image_title = !empty($gallery['image_name']) ? htmlspecialchars($gallery['image_name']) : 'Security Service Gallery';
            ?>
                    <!-- Gallery Item -->
                    <div class="col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="<?= $delay; ?>">
                        <div class="gallery-card shadow-sm">
                            <!-- data-gallery="security-gallery" se sabhi images ek slider group me jud jati hain -->
                            <a href="<?= $img_src; ?>" class="glightbox" data-gallery="security-gallery"
                                data-title="<?= $image_title; ?>">
                                <img src="<?= $img_src; ?>" class="img-fluid gallery-img" alt="<?= $image_title; ?>"
                                    loading="lazy">
                                <div class="gallery-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php
                    $delay = ($delay >= 300) ? 100 : $delay + 100;
                }
            } else {
                ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No gallery photos available at the moment.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<section class="section-padding bg-light-gray">
    <div class="container">

        <!-- Section Heading Added -->
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Our Core <span>Principles</span></h2>
            <p class="text-muted">The foundation of our commitment to your safety, security, and peace of mind.</p>
        </div>

        <div class="row g-4">
            <!-- Mission Card -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="vision-card">
                    <div class="vision-icon-wrapper">
                        <i class="fas fa-bullseye vision-icon"></i>
                    </div>
                    <h3 class="fw-bold mb-3 text-white">Our Mission</h3>
                    <p class="text-light opacity-75 mb-0">To deliver uncompromised security solutions through continuous training, strict verification processes, and leveraging modern security protocols to ensure complete client satisfaction.</p>
                </div>
            </div>

            <!-- Vision Card -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <!-- Center card highlighted with a slightly different primary dark color -->
                <div class="vision-card active-card">
                    <div class="vision-icon-wrapper">
                        <i class="fas fa-eye vision-icon"></i>
                    </div>
                    <h3 class="fw-bold mb-3 text-white">Our Vision</h3>
                    <p class="text-light opacity-75 mb-0">To be recognized as the most trusted and reliable security agency in Tamil Nadu, setting industry benchmarks for excellence, integrity, and proactive risk management.</p>
                </div>
            </div>

            <!-- Value Card -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="vision-card">
                    <div class="vision-icon-wrapper">
                        <i class="fas fa-gem vision-icon"></i>
                    </div>
                    <h3 class="fw-bold mb-3 text-white">Our Values</h3>
                    <p class="text-light opacity-75 mb-0">Integrity, vigilance, and helpfulness are the core pillars of our agency. We operate with absolute transparency and treat our clients' safety as our highest personal responsibility.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Testimonial / Client Review Section -->
<?php
// Testimonials fetch karne ki query
$testimonials_query = mysqli_query($conn, "SELECT * FROM testimonials ORDER BY display_order ASC, id DESC");
$testimonials = [];
if ($testimonials_query && mysqli_num_rows($testimonials_query) > 0) {
    while ($row = mysqli_fetch_assoc($testimonials_query)) {
        $testimonials[] = $row;
    }
}
?>

<section class="testimonial-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title text-white">Client <span>Reviews</span></h2>
            <p class="text-white-50">See what our clients have to say about our security services.</p>
        </div>

        <div class="row">
            <div class="col-12" data-aos="fade-up" data-aos-delay="200">
                <?php if (!empty($testimonials)): ?>
                    <!-- Bootstrap Carousel -->
                    <div id="testimonialCarousel" class="carousel slide testimonial-carousel" data-bs-ride="carousel">

                        <!-- Indicators/Dots -->
                        <div class="carousel-indicators">
                            <?php foreach ($testimonials as $index => $item): ?>
                                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="<?= $index; ?>"
                                    class="<?= $index === 0 ? 'active' : ''; ?>"
                                    aria-current="<?= $index === 0 ? 'true' : 'false'; ?>"
                                    aria-label="Slide <?= $index + 1; ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Carousel Items -->
                        <div class="carousel-inner">
                            <?php foreach ($testimonials as $index => $item):
                                // Client image path handling
                                $photo = $item['client_photo'];
                                if (!empty($photo)) {
                                    if (strpos($photo, 'admin/') === 0) {
                                        $img_src = $photo;
                                    } elseif (strpos($photo, 'uploads/') === 0) {
                                        $img_src = 'admin/' . $photo;
                                    } else {
                                        $img_src = 'admin/uploads/testimonials/' . $photo;
                                    }
                                } else {
                                    // Default profile avatar agar photo upload na ho
                                    $img_src = 'https://ui-avatars.com/api/?name=' . urlencode($item['client_name']) . '&background=random&color=fff';
                                }

                                // Role & Company formatting
                                $role_parts = array_filter([$item['client_title'], $item['client_company']]);
                                $role_text = implode(', ', $role_parts);

                                // Rating stars (1 to 5)
                                $rating = !empty($item['rating']) ? (int) $item['rating'] : 5;
                            ?>
                                <!-- Review Item -->
                                <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>" data-bs-interval="4000">
                                    <div class="testimonial-card">
                                        <i class="fas fa-quote-left quote-icon-large"></i>

                                        <p class="client-feedback">
                                            <?= htmlspecialchars(trim($item['testimonial_text'], '" ')); ?>
                                        </p>

                                        <img src="<?= $img_src; ?>" alt="<?= htmlspecialchars($item['client_name']); ?>"
                                            class="client-img">

                                        <h4 class="client-name"><?= htmlspecialchars($item['client_name']); ?></h4>

                                        <?php if (!empty($role_text)): ?>
                                            <p class="client-role"><?= htmlspecialchars($role_text); ?></p>
                                        <?php endif; ?>

                                        <!-- Rating Stars -->
                                        <div class="rating-stars">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                                <?php if ($s <= $rating): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                <?php else: ?>
                    <div class="text-center text-white-50">
                        <p>No reviews available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Improved Quote Section -->
<section id="quote" class="section-padding bg-light-gray">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Info -->
            <div class="col-lg-5 mb-5 mb-lg-0" data-aos="fade-right">
                <h2 class="section-title">Why <span>Choose Us</span></h2>
                <p class="text-muted mb-5">We bring trust and professional expertise directly to your premises, ensuring
                    comprehensive protection.</p>

                <div class="d-flex mb-4">
                    <div class="bg-white rounded-circle shadow-sm p-3 me-4 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-check-circle fs-3 text-primary-custom"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark">24/7 Support</h5>
                        <p class="text-muted mb-0">Always active emergency response team.</p>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="bg-white rounded-circle shadow-sm p-3 me-4 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-shield-alt fs-3 text-primary-custom"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark">Verified Service</h5>
                        <p class="text-muted mb-0">Strict background checks for all guards.</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="bg-white rounded-circle shadow-sm p-3 me-4 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-medal fs-3 text-primary-custom"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark">Experienced Team</h5>
                        <p class="text-muted mb-0">Highly trained for multi-sector security.</p>
                    </div>
                </div>
            </div>

            <!-- Right Form Card -->
            <div class="col-lg-7" data-aos="fade-left">
                <div class="quote-card p-4 p-md-5">
                    <h3 class="mb-4 fw-bold">Get A <span>Free Quote</span></h3>
                    <form action="#" method="POST">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <input type="text" class="form-control p-3" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control p-3" placeholder="Email Address" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control p-3" placeholder="Phone Number" required>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control p-3">
                                    <option selected disabled>Select Service Type</option>
                                    <option>Security Guard Service</option>
                                    <option>Corporate Security</option>
                                    <option>Housekeeping Service</option>
                                    <option>Other Services</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <textarea class="form-control p-3" rows="4"
                                    placeholder="How can we help you?"></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary-custom w-100 fs-5 py-3">Send
                                    Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog / Recent News Section -->
<?php
// Published blogs ko fetch karne ki query (Latest 3 posts)
$blogs_query = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 'published' ORDER BY id DESC LIMIT 3");
?>

<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Recent <span>News & Blog</span></h2>
            <p class="text-muted">Stay updated with our latest security insights and company news.</p>
        </div>

        <div class="row g-4">
            <?php
            if ($blogs_query && mysqli_num_rows($blogs_query) > 0) {
                $delay = 100;
                while ($blog = mysqli_fetch_assoc($blogs_query)) {

                    // Image path handling
                    $raw_img = $blog['image'];
                    if (!empty($raw_img)) {
                        if (strpos($raw_img, 'admin/') === 0) {
                            $img_src = $raw_img;
                        } elseif (strpos($raw_img, 'uploads/') === 0) {
                            $img_src = 'admin/' . $raw_img;
                        } else {
                            $img_src = 'admin/assets/img/uploads/' . $raw_img;
                        }
                    } else {
                        // Fallback image agar DB me image null ho
                        $img_src = 'assets/images/blog/default.jpg';
                    }

                    // Content se plain text snippet create karna (HTML tags remove karke)
                    $clean_text = strip_tags($blog['content']);
                    $short_desc = (strlen($clean_text) > 110) ? substr($clean_text, 0, 110) . '...' : $clean_text;

                    // Detail page link (Slug ya ID ke through)
                    $blog_link = !empty($blog['slug_url']) ? 'blog-details.php?slug=' . urlencode($blog['slug_url']) : 'blog-details.php?id=' . $blog['id'];
                    $blog_title = htmlspecialchars($blog['title']);
            ?>
                    <!-- Blog Card Item -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $delay; ?>">
                        <div class="blog-card border h-100 d-flex flex-column">
                            <a href="<?= $blog_link; ?>" class="d-block overflow-hidden">
                                <img src="<?= $img_src; ?>" alt="<?= $blog_title; ?>" class="blog-image w-100" loading="lazy">
                            </a>
                            <div class="blog-content d-flex flex-column flex-grow-1">
                                <h5 class="fw-bold mb-3">
                                    <a href="<?= $blog_link; ?>" class="text-dark text-decoration-none">
                                        <?= $blog_title; ?>
                                    </a>
                                </h5>
                                <p class="text-muted small mb-4 flex-grow-1"><?= $short_desc; ?></p>
                                <div class="mt-auto">
                                    <a href="<?= $blog_link; ?>" class="text-primary-custom fw-bold text-decoration-none">
                                        Read More <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    $delay += 100;
                }
            } else {
                ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No recent news or blogs available right now.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>