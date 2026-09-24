<?php
// Note: Agar aapne header.php mein DB connection ($pdo) bana liya hai, 
// toh wahi connection yaha reuse hoga. 

// 1. Fetch Contact Details & Copyright
$stmt_contact = $pdo->query("SELECT * FROM contacts LIMIT 1");
$contact_details = $stmt_contact->fetch(PDO::FETCH_ASSOC);

// 2. Fetch Active Footer Logo
$stmt_footer_logo = $pdo->query("SELECT logo_path FROM logos WHERE location = 'header' AND is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
$footer_logo_data = $stmt_footer_logo->fetch(PDO::FETCH_ASSOC);
$footer_logo = $footer_logo_data ? $footer_logo_data['logo_path'] : 'default_footer_logo.png';

// 3. Fetch About Us Content (and truncate for 3-4 lines)
$stmt_about = $pdo->query("SELECT content FROM about_us LIMIT 1");
$about_data = $stmt_about->fetch(PDO::FETCH_ASSOC);
$about_snippet = "";
if($about_data) {
    // HTML tags remove karna taaki layout kharab na ho
    $clean_text = strip_tags($about_data['content']); 
    // Kareeb 150-160 characters (3-4 lines) tak limit karna
    $about_snippet = mb_strimwidth($clean_text, 0, 160, "..."); 
}

// 4. Fetch Services for Footer Links (Limit to 5-6 services so footer doesn't get too long)
$stmt_footer_services = $pdo->query("SELECT id, service_name FROM services ORDER BY service_name ASC LIMIT 5");
$footer_services = $stmt_footer_services->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- ==================== FOOTER ==================== -->
    <footer class="main-footer pt-5 pb-3">
        <div class="container pt-4">
            <div class="row gy-4">
                
                <!-- Column 1: About & Logo -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-widget pe-lg-4">
                        <a href="index.php" class="d-inline-block mb-4">
                            <img src="admin/uploads/<?= htmlspecialchars($footer_logo) ?>" alt="SS Bouncers Footer Logo" class="footer-logo-img">
                        </a>
                        <p class="footer-about-text mb-4">
                            <?= htmlspecialchars($about_snippet) ?>
                        </p>
                        <!-- Social Links -->
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
                                <?php foreach($footer_services as $service): ?>
                                    <li><a href="service_details.php?id=<?= $service['id'] ?>"><?= htmlspecialchars($service['service_name']) ?></a></li>
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