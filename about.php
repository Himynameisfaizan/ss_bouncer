<?php
require_once 'config/connect.php';

// Fetch SEO Metadata for About Page[cite: 2]
$currentPage = basename($_SERVER['PHP_SELF']);
$seo_meta_query = $conn->query("SELECT meta_title, meta_key, meta_desc FROM meta WHERE page_url = 'about.php'");
$seo_data = ($seo_meta_query && $seo_meta_query->num_rows > 0) ? $seo_meta_query->fetch_assoc() : null;

$pageTitle = $seo_data['meta_title'] ?? "About Us | SS Bouncers";
$meta_keywords = $seo_data['meta_key'] ?? "security services, bouncers, security agency";
$meta_description = $seo_data['meta_desc'] ?? "Learn more about SS Bouncers, our history, mission, and commitment to providing top-tier security.";

// Fetch About Us Content from Database[cite: 2]
$about_query = $conn->query("SELECT * FROM about_us LIMIT 1");
$about_data = ($about_query && $about_query->num_rows > 0) ? $about_query->fetch_assoc() : null;

// Include Header
include 'includes/header.php'; 
include 'includes/breadcrumb.php'; 

?>

<!-- ==================== DYNAMIC ABOUT SECTION ==================== -->
<section class="inner-about section-padding py-5 bg-white">
    <div class="container py-5">
        <div class="row align-items-center gy-5">
            <!-- Image Collage (Modern Style) -->
            <div class="col-lg-6 reveal">
                <div class="about-image-collage position-relative pe-lg-4 pb-lg-4">
                    <?php if($about_data && !empty($about_data['image_url'])): ?>
                        <img src="<?= $site ?>admin/<?= htmlspecialchars($about_data['image_url']) ?>" alt="<?= htmlspecialchars($about_data['title']) ?>" class="w-100 rounded-4 shadow-lg object-fit-cover" style="height: 450px;">
                    <?php else: ?>
                        <img src="assets/images/banner/1.jpg" alt="Security Team" class="w-100 rounded-4 shadow-lg object-fit-cover" style="height: 450px;">
                    <?php endif; ?>
                    
                    <!-- Floating Accent Element -->
                    <div class="experience-float bg-primary-dark text-white p-4 rounded-4 shadow-lg position-absolute bottom-0 right-0" style="right: -20px; bottom: -20px; border-bottom: 4px solid var(--secondary-color);">
                        <h2 class="display-5 fw-bold text-secondary-accent mb-0">150+</h2>
                        <span class="fs-6 text-uppercase tracking-wider">Expert Guards</span>
                    </div>
                </div>
            </div>
            
            <!-- Dynamic Content -->
            <div class="col-lg-6 ps-lg-5 reveal">
                <span class="sub-heading text-secondary-accent fw-bold text-uppercase tracking-wider mb-2 d-block">Who We Are</span>
                <h2 class="main-heading text-primary-dark fw-bold mb-4" style="font-size: 2.5rem;">
                    <?= $about_data ? htmlspecialchars($about_data['title']) : 'Premium Security Services You Can Trust.' ?>
                </h2>
                
                <div class="about-desc text-muted" style="line-height: 1.8; font-size: 1.05rem;">
                    <?php 
                        if($about_data && !empty($about_data['content'])) {
                            echo $about_data['content']; // Outputting raw HTML since DB contains HTML tags[cite: 2]
                        } else {
                            echo "<p>We are a premier security agency specializing in providing highly trained and professional security personnel for various sectors including corporate offices, industrial warehouses, educational institutes, and multispecialty hospitals.</p>";
                        }
                    ?>
                </div>
                
                <div class="d-flex align-items-center mt-4">
                    <img src="assets/images/signature.png" alt="Director Signature" height="50" class="opacity-75">
                    <div class="ms-3 border-start ps-3 border-2">
                        <h6 class="mb-0 text-primary-dark fw-bold">Mr. Shiraj Shaikh</h6>
                        <small class="text-muted">Managing Director, SS Bouncers</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== CORE PRINCIPLES (STATIC FROM DESIGN) ==================== -->
<section class="core-principles-section py-5 bg-light-custom">
    <div class="container py-5">
        <div class="row justify-content-center text-center mb-5 reveal">
            <div class="col-lg-8">
                <span class="sub-heading text-secondary-accent fw-bold text-uppercase tracking-wider">Our Core Principles</span>
                <h2 class="main-heading text-primary-dark fw-bold mt-2">The foundation of our commitment</h2>
                <p class="text-muted mt-3">To your safety, security, and peace of mind.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Mission -->
            <div class="col-lg-4 col-md-6 reveal">
                <div class="principle-card bg-primary-dark text-center p-5 rounded-4 shadow-sm h-100 position-relative overflow-hidden">
                    <div class="principle-hover-bg"></div>
                    <div class="icon-wrap mb-4 d-inline-flex align-items-center justify-content-center rounded-circle border border-secondary-accent" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-bullseye text-secondary-accent fs-1"></i>
                    </div>
                    <h3 class="text-white fw-bold mb-3 position-relative z-2">Our Mission</h3>
                    <p class="text-white-50 mb-0 position-relative z-2" style="line-height: 1.6;">
                        To deliver uncompromised security solutions through continuous training, strict verification processes, and leveraging modern security protocols to ensure complete client satisfaction.
                    </p>
                </div>
            </div>
            
            <!-- Vision -->
            <div class="col-lg-4 col-md-6 reveal">
                <div class="principle-card bg-primary-dark text-center p-5 rounded-4 shadow-sm h-100 position-relative overflow-hidden">
                    <div class="principle-hover-bg"></div>
                    <div class="icon-wrap mb-4 d-inline-flex align-items-center justify-content-center rounded-circle border border-secondary-accent" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-eye text-secondary-accent fs-1"></i>
                    </div>
                    <h3 class="text-white fw-bold mb-3 position-relative z-2">Our Vision</h3>
                    <p class="text-white-50 mb-0 position-relative z-2" style="line-height: 1.6;">
                        To be recognized as the most trusted and reliable security agency, setting industry benchmarks for excellence, integrity, and proactive risk management.
                    </p>
                </div>
            </div>
            
            <!-- Values -->
            <div class="col-lg-4 col-md-6 reveal">
                <div class="principle-card bg-primary-dark text-center p-5 rounded-4 shadow-sm h-100 position-relative overflow-hidden">
                    <div class="principle-hover-bg"></div>
                    <div class="icon-wrap mb-4 d-inline-flex align-items-center justify-content-center rounded-circle border border-secondary-accent" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-gem text-secondary-accent fs-1"></i>
                    </div>
                    <h3 class="text-white fw-bold mb-3 position-relative z-2">Our Values</h3>
                    <p class="text-white-50 mb-0 position-relative z-2" style="line-height: 1.6;">
                        Integrity, vigilance, and helpfulness are the core pillars of our agency. We operate with absolute transparency and treat our clients' safety as our highest personal responsibility.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== HOW WE WORK ==================== -->
<section class="process-section py-5">
    <div class="container py-5">
        <div class="row text-center mb-5 reveal">
            <div class="col-12">
                <span class="sub-heading text-secondary-accent fw-bold text-uppercase tracking-wider">Security Process</span>
                <h2 class="main-heading text-primary-dark fw-bold mt-2">How We Secure Your Premises</h2>
            </div>
        </div>

        <div class="row position-relative reveal z-1">
            <!-- Connecting Line -->
            <div class="process-line d-none d-lg-block"></div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 text-center">
                <div class="process-step-box position-relative z-2">
                    <div class="process-icon-box bg-white shadow mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 90px; height: 90px; border: 3px solid var(--secondary-color);">
                        <i class="fa-solid fa-clipboard-check text-primary-dark fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-primary-dark">1. Site Assessment</h4>
                    <p class="text-muted small">We analyze your property to identify vulnerabilities and security needs.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 text-center">
                <div class="process-step-box position-relative z-2">
                    <div class="process-icon-box bg-white shadow mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 90px; height: 90px; border: 3px solid var(--secondary-color);">
                        <i class="fa-solid fa-user-shield text-primary-dark fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-primary-dark">2. Custom Strategy</h4>
                    <p class="text-muted small">Developing a tailored security plan with the right mix of personnel.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 text-center">
                <div class="process-step-box position-relative z-2">
                    <div class="process-icon-box bg-white shadow mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 90px; height: 90px; border: 3px solid var(--secondary-color);">
                        <i class="fa-solid fa-users-gear text-primary-dark fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-primary-dark">3. Guard Deployment</h4>
                    <p class="text-muted small">Deploying highly trained, verified, and briefed security guards.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 text-center">
                <div class="process-step-box position-relative z-2">
                    <div class="process-icon-box bg-white shadow mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 90px; height: 90px; border: 3px solid var(--secondary-color);">
                        <i class="fa-solid fa-headset text-primary-dark fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-primary-dark">4. 24/7 Monitoring</h4>
                    <p class="text-muted small">Continuous supervision and rapid response support round the clock.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scroll Reveal Script[cite: 6] -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const reveals = document.querySelectorAll(".reveal");
        const revealOnScroll = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("active");
                    observer.unobserve(entry.target); // Trigger only once
                }
            });
        }, {
            threshold: 0.15
        });

        reveals.forEach(reveal => revealOnScroll.observe(reveal));
    });
</script>

<?php include 'includes/footer.php'; ?>