<?php 
// 1. Define Page Title
$pageTitle = "About Us"; 

// 2. Include Header
include 'includes/header.php'; 

// 3. Include Breadcrumb
include 'includes/breadcrumb.php'; 
?>

<!-- Section 1: Company Overview -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Side: Image -->
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                <div class="about-img-wrapper">
                    <img src="assets/images/services/2.jpeg" style="height:30rem; object-fit:cover; object-position: top;" alt="About Sri Sai Security" class="about-img img-fluid">
                </div>
            </div>
            
            <!-- Right Side: Content -->
            <div class="col-lg-6 ps-lg-5" data-aos="fade-left">
                <h2 class="section-title">Welcome To <span>Sri Sai Security Services</span></h2>
                <h5 class="fw-bold text-secondary mb-3">Owned by Mr. R. Meen Barali | Based in Chennai</h5>
                <p class="text-muted mb-4" style="line-height: 1.8;">
                    Established with a deep commitment to safeguarding people and property, <strong>Sri Sai Security Services</strong> is a premier security and facility management agency. Operating from Thiruvanmiyur, Chennai, we specialize in offering comprehensive protection to corporates, industries, educational institutes, and residential complexes.
                </p>
                <p class="text-muted mb-4" style="line-height: 1.8;">
                    Our team comprises highly trained, rigorously verified, and physically fit security personnel who are equipped to handle any emergency. We don't just provide guards; we provide peace of mind.
                </p>
                
                <!-- Small Stats -->
                <div class="row mt-4">
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-shield fs-1 text-primary-custom me-3"></i>
                            <div>
                                <h3 class="mb-0 fw-bold">150+</h3>
                                <span class="text-muted small">Expert Guards</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-handshake fs-1 text-primary-custom me-3"></i>
                            <div>
                                <h3 class="mb-0 fw-bold">500+</h3>
                                <span class="text-muted small">Happy Clients</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Mission, Vision & Values -->
<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="row g-4">
            <!-- Mission Card -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="vision-card">
                    <i class="fas fa-bullseye vision-icon"></i>
                    <h3 class="fw-bold mb-3">Our Mission</h3>
                    <p class="text-light opacity-75">To deliver uncompromised security solutions through continuous training, strict verification processes, and leveraging modern security protocols to ensure complete client satisfaction.</p>
                </div>
            </div>
            
            <!-- Vision Card -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="vision-card" style="background-color: #0d254b;"> <!-- Slight color variation for center card -->
                    <i class="fas fa-eye vision-icon"></i>
                    <h3 class="fw-bold mb-3">Our Vision</h3>
                    <p class="text-light opacity-75">To be recognized as the most trusted and reliable security agency in Tamil Nadu, setting industry benchmarks for excellence, integrity, and proactive risk management.</p>
                </div>
            </div>
            
            <!-- Value Card -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="vision-card">
                    <i class="fas fa-gem vision-icon"></i>
                    <h3 class="fw-bold mb-3">Our Values</h3>
                    <p class="text-light opacity-75">Integrity, vigilance, and helpfulness are the core pillars of our agency. We operate with absolute transparency and treat our clients' safety as our highest personal responsibility.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Why Trust Us (Core Values / Checklist) -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Side: Checklist -->
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                <h2 class="section-title">Why You Should <span>Trust Us</span></h2>
                <p class="text-muted mb-4">Security is not a product; it's a promise. Here is why businesses and communities across Chennai trust Sri Sai Security Services with their safety.</p>
                
                <ul class="list-custom">
                    <li><i class="fas fa-check-circle"></i> 100% Verified & Background-Checked Guards</li>
                    <li><i class="fas fa-check-circle"></i> 24/7 Rapid Response & Active Supervision</li>
                    <li><i class="fas fa-check-circle"></i> Tailored Security Plans for Corporates & Residences</li>
                    <li><i class="fas fa-check-circle"></i> Regular Physical Training & Mock Drills</li>
                    <li><i class="fas fa-check-circle"></i> Strict Adherence to Industry Safety Protocols</li>
                </ul>

                <a href="#quote" class="btn btn-primary-custom mt-4">Hire Our Guards</a>
            </div>

            <!-- Right Side: Secondary Image -->
            <div class="col-lg-6" data-aos="fade-left">
                <img src="assets/images/services/1.jpeg" alt="Security Trust" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</section>

<?php 
// 4. Include Footer
include 'includes/footer.php'; 
?>