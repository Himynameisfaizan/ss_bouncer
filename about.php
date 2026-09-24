<?php
include 'config/connect.php';

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

$brands_array = [];
if (isset($conn)) {
    $brands_res = mysqli_query($conn, "SELECT * FROM brands ORDER BY id DESC");
    if ($brands_res && mysqli_num_rows($brands_res) > 0) {
        while ($brand = mysqli_fetch_assoc($brands_res)) {
            $brands_array[] = $brand;
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<?php include 'includes/breadcrumb.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta_keywords); ?>">
    <link rel="icon" href="<?= htmlspecialchars($favicon); ?>" type="image/png">
</head>
<body>
    
<!-- 1. ABOUT COMPANY SECTION (SEO H1 Tag applied here) -->
<section class="inner-about section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 reveal mb-4 mb-lg-0">
                <div class="about-image-collage position-relative">
                    <img src="https://images.unsplash.com/photo-1716816211590-c15a328a5ff0?w=500&auto=format&fit=crop&q=60" alt="Bhagirath Enterprise Export Facility" class="about-img-1 w-100 rounded shadow-lg" style="object-fit: cover; height: 350px;">
                    <img src="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?q=80&w=600&auto=format&fit=crop" alt="Premium Indian Spices and Dry Fruits" class="about-img-2 position-absolute border border-white border-5 rounded shadow" style="width: 250px; bottom: -30px; right: -20px;">
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5 reveal mt-5 mt-lg-0">
                <span class="sec-subtitle text-uppercase fw-bold" style="color: #E3000F; letter-spacing: 1px; font-size: 14px;">About Bhagirath Enterprise</span>
                <h1 class="sec-title mb-4" style="color: #17385A; font-weight: 700; font-size: 2.2rem; line-height: 1.3;">Exporting the Finest Agricultural Wealth of India to the World.</h1>
                <p class="about-desc mb-3" style="color: #555; line-height: 1.7;">
                    <strong>Bhagirath Enterprise</strong> has established itself as a premier global exporter of high-quality agricultural commodities. Operating from the heart of Delhi, India, we bridge the gap between India's rich, fertile farms and international markets, delivering excellence in every shipment.
                </p>
                <p class="about-desc mb-4" style="color: #555; line-height: 1.7;">
                    Specializing in the export of premium <strong>Whole Spices, Dry Fruits,</strong> and authentic agricultural products, we ensure that our global clientele receives 100% pure, unadulterated, and export-grade materials. Our stringent quality control, hygienic processing, and direct-from-farm sourcing make us a trusted partner in the international food trade.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 2. MISSION & VISION SECTION -->
<section class="mv-section section-padding" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="row g-4">
            <!-- Mission Card -->
            <div class="col-lg-6 reveal">
                <div class="mv-card bg-white p-5 rounded-4 shadow-sm h-100" style="border-top: 4px solid #E3000F;">
                    <div class="icon-wrap mb-4" style="width: 60px; height: 60px; background: rgba(227,0,15,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-bullseye mv-icon" style="font-size: 24px; color: #E3000F;"></i>
                    </div>
                    <h3 class="mv-title" style="color: #17385A; font-weight: 700; margin-bottom: 15px;">Our Mission</h3>
                    <p class="about-desc mb-0" style="color: #666; line-height: 1.6;">
                        To consistently deliver superior quality agricultural products to global markets while maintaining ethical sourcing practices. We aim to empower local Indian farmers and provide international consumers with safe, hygienic, and authentic flavors.
                    </p>
                </div>
            </div>
            <!-- Vision Card -->
            <div class="col-lg-6 reveal">
                <div class="mv-card bg-white p-5 rounded-4 shadow-sm h-100" style="border-top: 4px solid #17385A;">
                    <div class="icon-wrap mb-4" style="width: 60px; height: 60px; background: rgba(23,56,90,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-eye mv-icon" style="font-size: 24px; color: #17385A;"></i>
                    </div>
                    <h3 class="mv-title" style="color: #17385A; font-weight: 700; margin-bottom: 15px;">Our Vision</h3>
                    <p class="about-desc mb-0" style="color: #666; line-height: 1.6;">
                        To be the world's most reliable and sustainable partner in the agricultural export industry, recognized globally for our uncompromising quality standards, competitive pricing, and commitment to global food safety.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. WHY CHOOSE US -->
<section class="inner-wcu section-padding">
    <div class="container">
        <div class="row text-center mb-5 reveal">
            <div class="col-12">
                <span class="sec-subtitle text-uppercase fw-bold" style="color: #E3000F; letter-spacing: 1px; font-size: 14px;">The Bhagirath Enterprise Advantage</span>
                <h2 class="sec-title" style="color: #17385A; font-weight: 700;">Why Partner With Us?</h2>
            </div>
        </div>

        <div class="row align-items-center">
            <!-- Left Side Points -->
            <div class="col-lg-4 reveal">
                <div class="wcu-list-item d-flex align-items-start mb-4">
                    <div class="wcu-list-icon me-3 mt-1" style="color: #E3000F; font-size: 1.5rem;"><i class="fa-solid fa-leaf"></i></div>
                    <div class="wcu-list-content">
                        <h4 style="color: #17385A; font-weight: 600; font-size: 1.1rem;">Farm-Fresh Sourcing</h4>
                        <p class="small text-muted">We procure our dry fruits and spices directly from the most fertile and trusted agricultural regions.</p>
                    </div>
                </div>
                <div class="wcu-list-item d-flex align-items-start mb-4">
                    <div class="wcu-list-icon me-3 mt-1" style="color: #E3000F; font-size: 1.5rem;"><i class="fa-solid fa-certificate"></i></div>
                    <div class="wcu-list-content">
                        <h4 style="color: #17385A; font-weight: 600; font-size: 1.1rem;">Certified Quality</h4>
                        <p class="small text-muted">Strict adherence to global food safety standards, fully compliant with international export boards.</p>
                    </div>
                </div>
            </div>

            <!-- Center Image -->
            <div class="col-lg-4 text-center reveal mb-4 mb-lg-0">
                <div style="padding: 15px; border: 2px dashed #E3000F; border-radius: 50%; display: inline-block;">
                    <img src="https://images.unsplash.com/photo-1493946243886-c4d6f4614ff3?q=80&w=600&auto=format&fit=crop" alt="Global Export" style="width: 100%; max-width: 300px; border-radius: 50%; object-fit: cover; aspect-ratio: 1/1;">
                </div>
            </div>

            <!-- Right Side Points -->
            <div class="col-lg-4 reveal">
                <div class="wcu-list-item d-flex align-items-start mb-4">
                    <div class="wcu-list-icon me-3 mt-1" style="color: #E3000F; font-size: 1.5rem;"><i class="fa-solid fa-box-open"></i></div>
                    <div class="wcu-list-content">
                        <h4 style="color: #17385A; font-weight: 600; font-size: 1.1rem;">Premium Export Packaging</h4>
                        <p class="small text-muted">Moisture-proof, container-safe packaging that preserves aroma, taste, and product integrity during transit.</p>
                    </div>
                </div>
                <div class="wcu-list-item d-flex align-items-start mb-4">
                    <div class="wcu-list-icon me-3 mt-1" style="color: #E3000F; font-size: 1.5rem;"><i class="fa-solid fa-ship"></i></div>
                    <div class="wcu-list-content">
                        <h4 style="color: #17385A; font-weight: 600; font-size: 1.1rem;">Global Logistics</h4>
                        <p class="small text-muted">A robust supply chain and freight network ensuring safe, hassle-free, and timely delivery across borders.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. Dynamic Brands / Clients Slider Section -->
<section class="brands-slider-section py-5" style="background-color: #f8f9fa; border-top: 1px solid #eaeaea;">
    <div class="container">
        <h2 class="text-center mb-5" style="color: #17385A; font-weight: 700; font-size: 1.5rem; letter-spacing: 1px;">OUR TRUSTED CLIENTS & PARTNERS</h2>
        
        <div class="brand-slider-container">
            <div class="brand-slide-track">
                <?php if(!empty($brands_array)): ?>
                    <?php 
                    for($loop = 0; $loop < 2; $loop++):
                        foreach($brands_array as $brand):
                            $brandLogo = !empty($brand['logo_path']) ? $brand['logo_path'] : '';
                    ?>
                    <div class="brand-slide">
                        <?php if(!empty($brandLogo)): ?>
                            <img src="admin/<?= htmlspecialchars($brandLogo) ?>" alt="<?= htmlspecialchars($brand['brand_name']) ?>" title="<?= htmlspecialchars($brand['brand_name']) ?>">
                        <?php else: ?>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($brand['brand_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php 
                        endforeach; 
                    endfor; 
                    ?>
                <?php else: ?>
                    <div class="brand-slide"><h4 class="brand-logo" style="color: #999;">FSSAI</h4></div>
                    <div class="brand-slide"><h4 class="brand-logo" style="color: #999;">APEDA</h4></div>
                    <div class="brand-slide"><h4 class="brand-logo" style="color: #999;">SPICES BOARD</h4></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- 5. HOW WE WORK (WORKING PROCESS) -->
<section class="process-section">
    <div class="container">
        <div class="row text-center mb-4 reveal">
            <div class="col-12">
                <span class="sec-subtitle" style="color: #ffffff;">Our Supply Chain</span>
                <h2 class="sec-title" style="color: #ffffff;">The Export Process</h2>
            </div>
        </div>

        <div class="process-grid reveal">
            <!-- Step 1 -->
            <div class="process-step">
                <div class="process-icon"><i class="fa-solid fa-tractor"></i></div>
                <h4>1. Ethical Sourcing</h4>
                <p>Procuring premium raw materials straight from certified farmers.</p>
            </div>
            <!-- Step 2 -->
            <div class="process-step">
                <div class="process-icon"><i class="fa-solid fa-gears"></i></div>
                <h4>2. Processing & Grading</h4>
                <p>Hygienic sorting, cleaning, and processing in our modern facilities.</p>
            </div>
            <!-- Step 3 -->
            <div class="process-step">
                <div class="process-icon"><i class="fa-solid fa-microscope"></i></div>
                <h4>3. Quality Assurance</h4>
                <p>Rigorous lab testing to ensure export-grade purity and compliance.</p>
            </div>
            <!-- Step 4 -->
            <div class="process-step">
                <div class="process-icon"><i class="fa-solid fa-globe"></i></div>
                <h4>4. Secure Export</h4>
                <p>Customs clearance and container shipping to international destinations.</p>
            </div>
        </div>
    </div>
</section>




<!-- Simple CSS for smooth reveals on scroll (If not already in your CSS file) -->
<style>
    .reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease-out;
    }
    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }
    /* Adding connecting lines for process steps on desktop */
    @media (min-width: 992px) {
        .process-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 40px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: rgba(255, 255, 255, 0.2);
            border-top: 2px dashed rgba(255, 255, 255, 0.5);
            z-index: 0;
        }
        .process-step .process-icon {
            position: relative;
            z-index: 1;
        }
    }
</style>



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
<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>