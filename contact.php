<?php
require_once 'config/connect.php'; 
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_inquiry'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $subject = $conn->real_escape_string($_POST['interest']);
    $message = $conn->real_escape_string($_POST['message']);

    $insertQuery = "INSERT INTO inquiries (name, email, phone, subject, message, status) VALUES ('$name', '$email', '$phone', '$subject', '$message', 'new')";
    
    if($conn->query($insertQuery)) {
        $msg = "<div class='alert alert-success mt-3 shadow-sm border-0'><i class='fa-solid fa-circle-check me-2'></i> Thank you! Your request has been sent successfully. Our security experts will contact you soon.</div>";
    } else {
        $msg = "<div class='alert alert-danger mt-3 shadow-sm border-0'><i class='fa-solid fa-circle-exclamation me-2'></i> Oops! Something went wrong. Please call us directly.</div>";
    }
}

$contactQuery = $conn->query("SELECT * FROM contacts ORDER BY id DESC LIMIT 1");
$contactInfo = $contactQuery ? $contactQuery->fetch_assoc() : null;

$siteAddress = !empty($contactInfo['address']) ? $contactInfo['address'] : 'Padmanagar opposite venkateshwara swamy temple, Hyderabad, Telangana - 500085';
$sitePhone = !empty($contactInfo['phone']) ? $contactInfo['phone'] : '+91 7097059293';
$siteEmail = !empty($contactInfo['email']) ? $contactInfo['email'] : 'sirajshaik225@gmail.com';
$siteWorkingHours = !empty($contactInfo['working_hours']) ? $contactInfo['working_hours'] : '24/7 Support Available';

$currentPage = basename($_SERVER['PHP_SELF']);
$seo_meta_query = $conn->query("SELECT meta_title, meta_key, meta_desc FROM meta WHERE page_url = '$currentPage'");
$seo_data = ($seo_meta_query && $seo_meta_query->num_rows > 0) ? $seo_meta_query->fetch_assoc() : null;

$pageTitle = $seo_data['meta_title'] ?? "Contact Us | SS Bouncers";
$meta_keywords = $seo_data['meta_key'] ?? "contact ss bouncers, hire security guards, bouncer agency hyderabad";
$meta_description = $seo_data['meta_desc'] ?? "Get in touch with SS Bouncers for premium security guard and housekeeping services. We provide verified and professional staff.";

include 'includes/header.php'; 
include 'includes/breadcrumb.php'; 
?>

<!-- ==================== CONTACT INFO & FORM ==================== -->
<section class="contact-page-section py-5 bg-white">
    <div class="container py-5">
        <div class="row g-5">
            
            <!-- Left Side: Contact Information -->
            <div class="col-lg-5 reveal">
                <div class="contact-info-wrapper pe-lg-4">
                    <span class="sub-heading text-secondary-accent fw-bold text-uppercase tracking-wider mb-2 d-block">Get In Touch</span>
                    <h2 class="main-heading text-primary-dark fw-bold mb-4" style="font-size: 2.5rem;">Protecting What Matters Most.</h2>
                    <p class="text-muted mb-5" style="line-height: 1.8; font-size: 1.05rem;">Require professional security personnel or housekeeping staff? Reach out to us for a customized plan. Our experts are ready to assist you 24/7.</p>
                    
                    <!-- Location Card -->
                    <div class="contact-info-card d-flex p-4 rounded-4 bg-light-custom mb-4 shadow-sm border border-light">
                        <div class="icon-box bg-white text-primary-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0 me-4" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-primary-dark mb-2">Head Office</h5>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($siteAddress); ?></p>
                        </div>
                    </div>

                    <!-- Phone Card -->
                    <div class="contact-info-card d-flex p-4 rounded-4 bg-light-custom mb-4 shadow-sm border border-light">
                        <div class="icon-box bg-white text-primary-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0 me-4" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-primary-dark mb-2">Call Us 24/7</h5>
                            <a href="tel:<?php echo htmlspecialchars($sitePhone); ?>" class="text-decoration-none fw-bold fs-5 text-secondary-accent"><?php echo htmlspecialchars($sitePhone); ?></a>
                            <p class="text-muted mb-0 small mt-1"><?php echo htmlspecialchars($siteWorkingHours); ?></p>
                        </div>
                    </div>

                    <!-- Email Card -->
                    <div class="contact-info-card d-flex p-4 rounded-4 bg-light-custom shadow-sm border border-light">
                        <div class="icon-box bg-white text-primary-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0 me-4" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-primary-dark mb-2">Email Us</h5>
                            <a href="mailto:<?php echo htmlspecialchars($siteEmail); ?>" class="text-decoration-none text-muted"><?php echo htmlspecialchars($siteEmail); ?></a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Side: Contact Form -->
            <div class="col-lg-7 reveal">
                <div class="premium-form-box bg-white p-4 p-md-5 rounded-4 shadow-lg border border-light position-relative overflow-hidden">
                    <div class="form-highlight-border"></div>
                    <h3 class="fw-bold text-primary-dark mb-3">Request a Free Callback</h3>
                    <p class="text-muted mb-4 pb-2 border-bottom">Fill out the details below and our security manager will contact you.</p>
                    
                    <!-- Alert Message -->
                    <?php echo $msg; ?>
                    
                    <form action="contact.php" method="POST" class="mt-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control custom-input" name="name" id="name" placeholder="Your Name" required>
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control custom-input" name="phone" id="phone" placeholder="Phone Number" required>
                                    <label for="phone">Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="email" class="form-control custom-input" name="email" id="email" placeholder="Email Address" required>
                                    <label for="email">Email Address</label>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <select class="form-select custom-input" name="interest" id="interest" required>
                                        <?php $selectedService = isset($_GET['service']) ? $_GET['service'] : ''; ?>
                                        <option value="" disabled <?php echo ($selectedService == '') ? 'selected' : ''; ?>>Select a Service</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                        
                                        <!-- Dynamic Services from Database -->
                                        <?php 
                                        $servicesQuery = $conn->query("SELECT service_name, slug_url FROM services WHERE status = 1 ORDER BY service_name ASC");
                                        if ($servicesQuery && $servicesQuery->num_rows > 0) {
                                            while($srv = $servicesQuery->fetch_assoc()):
                                                // Check match with slug OR name for smart auto-select
                                                $slugCheck = !empty($srv['slug_url']) ? $srv['slug_url'] : $srv['service_name'];
                                                $isSelected = ($selectedService == $slugCheck) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo htmlspecialchars($srv['service_name']); ?>" <?php echo $isSelected; ?>>
                                            <?php echo htmlspecialchars($srv['service_name']); ?>
                                        </option>
                                        <?php 
                                            endwhile;
                                        }
                                        ?>
                                    </select>
                                    <label for="interest">Interested In</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea class="form-control custom-input" name="message" id="message" placeholder="Your Requirements" style="height: 130px" required></textarea>
                                    <label for="message">Tell us about your security requirements...</label>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" name="submit_inquiry" class="btn btn-premium w-100 py-3 rounded-3 fs-5">
                                    Send Message <i class="fa-solid fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==================== GOOGLE MAP ==================== -->
<section class="map-section border-top border-bottom reveal">
    <div class="container-fluid p-0">
        <div class="map-container" style="height: 450px;">
            <?php 
                if (!empty($contactInfo['map'])) {
                    $mapData = trim($contactInfo['map']);
                    if (strpos($mapData, '<iframe') !== false) {
                        $mapIframe = preg_replace('/width="[^"]+"/', 'width="100%"', $mapData);
                        $mapIframe = preg_replace('/height="[^"]+"/', 'height="100%"', $mapIframe);
                        echo $mapIframe;
                    } else {
                        echo '<iframe src="' . htmlspecialchars($mapData) . '" width="100%" height="100%" style="border:0; filter: grayscale(20%) contrast(1.1);" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                    }
                } else {
                    echo '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15225.99264104033!2d78.4326574!3d17.4357777!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb9158f201b205%3A0x11bbe7be7792411b!2sHyderabad%2C%20Telangana!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0; filter: grayscale(20%) contrast(1.1);" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                }
            ?>
        </div>
    </div>
</section>

<!-- Scroll Reveal Script -->
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
        }, { threshold: 0.15 });

        reveals.forEach(reveal => revealOnScroll.observe(reveal));
    });
</script>

<?php include 'includes/footer.php'; ?>