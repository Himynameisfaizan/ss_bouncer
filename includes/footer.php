<!-- Footer Section -->
<?php
include 'admin/db-conn.php';

// 1. Fetch Footer Logo
$footer_logo_query = mysqli_query($conn, "SELECT logo_path FROM logos WHERE location = 'footer' AND is_active = 1 ORDER BY id DESC LIMIT 1");
$footer_logo = mysqli_fetch_assoc($footer_logo_query);
$footer_logo_src = !empty($footer_logo['logo_path']) ? 'admin/' . $footer_logo['logo_path'] : 'assets/images/logo/logo.jpg';

// 2. Fetch Services for Quick Links (Limit 4)
$footer_services_query = mysqli_query($conn, "SELECT id, service_name FROM services ORDER BY id ASC LIMIT 4");

// 3. Fetch Contact Details
$footer_contact_query = mysqli_query($conn, "SELECT * FROM contacts LIMIT 1");
$footer_contact = mysqli_fetch_assoc($footer_contact_query);

// Fallback values agar DB me details na ho[cite: 1]
$f_name = !empty($footer_contact['name']) ? htmlspecialchars($footer_contact['name']) : 'Mr. R. Meen Barali';
$f_address = !empty($footer_contact['address']) ? htmlspecialchars($footer_contact['address']) : 'No.2, M.G. Road, Thiruvanmiyur, Chennai - 600041';
$f_phone = !empty($footer_contact['phone']) ? htmlspecialchars($footer_contact['phone']) : '+91 72008 64976';
$wp_number = !empty($footer_contact['wp_number']) ? htmlspecialchars($footer_contact['wp_number']) : '+91 72008 64976';
$f_email = !empty($footer_contact['email']) ? htmlspecialchars($footer_contact['email']) : 'srisaiss505@gmail.com';

// Map URL Setup (Ensure it's an embed link)
$f_map_src = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3887.6108380011115!2d80.25389767505176!3d12.99672451432701!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a5267f36527872d%3A0x804b2dc6c6b4d6d9!2s2%2C%20Mahatma%20Gandhi%20Rd%2C%20Subramaniam%20Colony%2C%20Thiruvanmiyur%2C%20Chennai%2C%20Greater%20Chennai%2C%20Tamil%20Nadu%20600041!5e0!3m2!1sen!2sin!4v1787312799695!5m2!1sen!2sin";
if (!empty($footer_contact['map']) && strpos($footer_contact['map'], 'embed') !== false) {
    $f_map_src = $footer_contact['map']; // Agar DB me iframe/embed link diya hai to replace hoga[cite: 1]
}

// 4. Dynamic Copyright Year
$current_year = date("Y");
?>

<footer class="footer">
    <div class="container pb-5">
        <div class="row g-4">

            <!-- 1. About Company -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="d-flex align-items-center mb-4">
                    <!-- Dynamic Footer Logo -->
                    <img src="assets/images/logo/logo.jpg   " alt="Sri Sai Security Logo" class="me-3 bg-white"
                        style="width: 80px; height: 80px; object-fit: contain; border-radius: 50%; padding: 5px;">
                    <div>
                        <span class="d-block fs-4 text-white fw-bold">Sri Sai</span>
                        <span class="d-block fs-6 text-white-50 fw-normal">Security Services</span>
                    </div>
                </div>
                <p class="mb-4 text-white-50">Providing verified, secured, and top-tier security and housekeeping
                    services to safeguard your assets and ensure peace of mind.</p>
            </div>

            <!-- 2. Services Links (Dynamic from DB) -->
            <div class="col-lg-2 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <h5>Services</h5>
                <?php
                if ($footer_services_query && mysqli_num_rows($footer_services_query) > 0) {
                    while ($f_service = mysqli_fetch_assoc($footer_services_query)) {
                ?>
                        <a href="service-details.php?id=<?= $f_service['id']; ?>" class="footer-link">
                            <?= htmlspecialchars($f_service['service_name']); ?>
                        </a>
                    <?php
                    }
                } else {
                    ?>
                    <a href="services.php" class="footer-link">Corporate Security</a>
                    <a href="services.php" class="footer-link">Industrial Security</a>
                    <a href="services.php" class="footer-link">Warehouse Security</a>
                    <a href="services.php" class="footer-link">Housekeeping</a>
                <?php } ?>
            </div>

            <!-- 3. Quick Links -->
            <div class="col-lg-2 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <h5>Quick Links</h5>
                <a href="about.php" class="footer-link">About Us</a>
                <a href="blog.php" class="footer-link">Our Blog</a>
                <a href="index.php#quote" class="footer-link">Client Reviews</a>
                <a href="contact.php" class="footer-link">Contact Us</a>
            </div>

            <!-- 4. Contact Us & Map (Dynamic from Contacts table) -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="400">
                <h5>Contact Us</h5>
                <p class="mb-2"><i class="fas fa-user text-primary-custom me-2"></i> <?= $f_name; ?></p>
                <p class="mb-2"><i class="fas fa-map-marker-alt text-primary-custom me-2"></i> <?= $f_address; ?></p>
                <p class="mb-2"><i class="fas fa-phone-alt text-primary-custom me-2"></i>
                    <a href="tel:<?= $f_phone; ?>"
                        class="text-white-50 text-decoration-none hover-primary"><?= $f_phone; ?></a>
                </p>
                <p class="mb-3"><i class="fas fa-envelope text-primary-custom me-2"></i>
                    <a href="mailto:<?= $f_email; ?>"
                        class="text-white-50 text-decoration-none hover-primary"><?= $f_email; ?></a>
                </p>

                <!-- Small Footer Location Map -->
                <div class="mt-3 rounded overflow-hidden shadow-sm" style="height: 160px; border: 2px solid #1a2a47;">
                    <iframe src="<?= $f_map_src; ?>" width="100%" height="100%" style="border:0;" allowfullscreen=""
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

        </div>
    </div>

    <!-- Dynamic Copyright Year -->
    <div class="bg-dark text-center py-3">
        <p class="mb-0 text-white-50">Copyright &copy; <?= $current_year; ?> Sri Sai Security Services. All Rights
            Reserved.</p>
    </div>
</footer>

<!-- ==============================================
     FLOATING WHATSAPP & CALL BUTTONS
=============================================== -->
<div class="floating-contact-wrap">
    <!-- Dynamic Call Button -->
    <a href="tel:<?= $f_phone; ?>" class="float-btn phone-float-btn" title="Call Us Now">
        <i class="fas fa-phone-alt"></i>
    </a>

    <!-- Dynamic WhatsApp Button -->
    <a href="https://wa.me/<?= $wp_number; ?>?text=Hello,%20I%20am%20looking%20for%20Security%20Services." target="_blank" class="float-btn wp-float-btn" title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .floating-contact-wrap {
        position: fixed;
        bottom: 30px;
        right: 30px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        z-index: 9999;
    }

    .float-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 30px;
        text-decoration: none;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease-in-out;
    }

    .float-btn:hover {
        transform: translateY(-5px) scale(1.05);
        color: white;
    }

    /* Phone Button Style */
    .phone-float-btn {
        background-color: #0d6efd;
        /* Theme Primary Color */
    }

    .phone-float-btn:hover {
        background-color: #0b5ed7;
        box-shadow: 0px 6px 15px rgba(13, 110, 253, 0.5);
    }

    /* WhatsApp Button Style with Pulse Animation */
    .wp-float-btn {
        background-color: #25D366;
        /* Official WhatsApp Green */
        animation: pulse-animation 2s infinite;
    }

    .wp-float-btn:hover {
        background-color: #1ebe57;
        animation: none;
        box-shadow: 0px 6px 15px rgba(37, 211, 102, 0.5);
    }

    /* Cool Pulse Animation */
    @keyframes pulse-animation {
        0% {
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
        }
    }

    /* Responsive for smaller screens */
    @media (max-width: 768px) {
        .floating-contact-wrap {
            bottom: 20px;
            right: 20px;
            gap: 10px;
        }

        .float-btn {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }
    }
</style>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script type="text/javascript"
    src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<!-- Google Translate Script -->
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        }, 'google_translate_element');
    }
</script>

<script>
    // Initialize AOS animations
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
</script>

</body>

</html>