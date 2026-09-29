<?php
global $conn, $site;

$stmt_contact = $conn->query("SELECT * FROM contacts LIMIT 1");
$contact_details = $stmt_contact ? $stmt_contact->fetch_assoc() : null;

$stmt_footer_logo = $conn->query("SELECT logo_path FROM logos WHERE location = 'header' AND is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
$footer_logo_data = $stmt_footer_logo ? $stmt_footer_logo->fetch_assoc() : null;
$footer_logo = $footer_logo_data ? $site . 'admin/uploads/' . $footer_logo_data['logo_path'] : 'assets/images/default-logo.png';

$stmt_about = $conn->query("SELECT content FROM about_us LIMIT 1");
$about_data = $stmt_about ? $stmt_about->fetch_assoc() : null;
$about_snippet = "";
if($about_data) {
    $clean_text = strip_tags($about_data['content']); 
    $about_snippet = mb_strimwidth($clean_text, 0, 160, "..."); 
}

$stmt_footer_services = $conn->query("SELECT id, service_name, slug_url FROM services WHERE status = 1 ORDER BY display_order ASC, service_name ASC LIMIT 5");
$footer_services = [];
if ($stmt_footer_services && $stmt_footer_services->num_rows > 0) {
    while($row = $stmt_footer_services->fetch_assoc()) {
        $footer_services[] = $row;
    }
}
?>

    <!-- ==================== FOOTER ==================== -->
    <footer class="main-footer pt-5 pb-3">
        <div class="container pt-4">
            <div class="row gy-4">
                
                <!-- Column 1: About & Logo -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-widget pe-lg-4">
                       <div class="bg-white w-50 d-flex justify-content-center align-items-center border-3 my-4">
                         <a href="index.php" class="d-inline-block mb-4">
                            <img src="<?= htmlspecialchars($footer_logo) ?>" alt="SS Bouncers Footer Logo" class="footer-logo-img">
                        </a>
                       </div>
                        <p class="footer-about-text mb-4">
                            <?= htmlspecialchars($about_snippet) ?>
                        </p>
                        <div class="footer-social">
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

                <!-- Column 2: Our Services -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4 class="footer-heading mb-4">Our Services</h4>
                        <ul class="footer-links list-unstyled">
                            <?php if(!empty($footer_services)): ?>
                                <?php foreach($footer_services as $service): 
                                    $s_slug = !empty($service['slug_url']) ? $service['slug_url'] : preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($service['service_name'])));
                                ?>
                                    <li><a href="service-details.php?slug=<?= htmlspecialchars($s_slug) ?>"><?= htmlspecialchars($service['service_name']) ?></a></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><a href="#">Security Services</a></li>
                            <?php endif; ?>
                            <li><a href="services.php" class="view-all-link">View All Services <i class="fa-solid fa-arrow-right-long ms-1"></i></a></li>
                        </ul>
                    </div>
                </div>

                <!-- Column 3: Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4 class="footer-heading mb-4">Quick Links</h4>
                        <ul class="footer-links list-unstyled">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="gallery.php">Gallery</a></li>
                            <li><a href="blog.php">Our Blog</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Column 4: Contact Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4 class="footer-heading mb-4">Contact Info</h4>
                        <ul class="footer-contact-list list-unstyled">
                            <?php if(!empty($contact_details['address'])): ?>
                            <li class="d-flex mb-3">
                                <i class="fa-solid fa-location-dot mt-1 me-3 accent-icon"></i>
                                <span><?= htmlspecialchars($contact_details['address']) ?></span>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(!empty($contact_details['phone'])): ?>
                            <li class="d-flex mb-3">
                                <i class="fa-solid fa-phone-volume mt-1 me-3 accent-icon"></i>
                                <a href="tel:<?= htmlspecialchars($contact_details['phone']) ?>"><?= htmlspecialchars($contact_details['phone']) ?></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(!empty($contact_details['email'])): ?>
                            <li class="d-flex mb-3">
                                <i class="fa-solid fa-envelope mt-1 me-3 accent-icon"></i>
                                <a href="mailto:<?= htmlspecialchars($contact_details['email']) ?>"><?= htmlspecialchars($contact_details['email']) ?></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(!empty($contact_details['working_hours'])): ?>
                            <li class="d-flex">
                                <i class="fa-solid fa-clock mt-1 me-3 accent-icon"></i>
                                <span><?= htmlspecialchars($contact_details['working_hours']) ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

            </div>
            
            <!-- Copyright Section -->
            <div class="footer-bottom mt-5 pt-4 border-top">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="mb-0 copyright-text">
                            <?= !empty($contact_details['copyright']) ? htmlspecialchars($contact_details['copyright']) : '&copy; ' . date('Y') . ' SS Bouncers. All Rights Reserved.' ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <ul class="list-inline mb-0 footer-bottom-links">
                            <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                            <li class="list-inline-item ms-3"><a href="#">Terms & Conditions</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Link (Make sure this exists before body close) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>