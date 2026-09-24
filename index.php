<?php
include 'config/connect.php';

$banner_res = false;
if (isset($conn)) {
    $banner_res = mysqli_query($conn, "SELECT * FROM banners WHERE status = 0 ORDER BY display_order ASC, id DESC");
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
        "logo": "<?= $site; ?>
        /assets/images/logo/logo.png",
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
    
<!-- Hero Slider Section Start -->
<div id="heroCarousel" class="carousel slide carousel-fade hero-slider" data-bs-ride="carousel" data-bs-pause="false">
    <div class="carousel-indicators">
        <?php
        if ($banner_res && mysqli_num_rows($banner_res) > 0):
            $i = 0;
            mysqli_data_seek($banner_res, 0);
            while ($b_row = mysqli_fetch_assoc($banner_res)):
        ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" class="<?= ($i == 0) ? 'active' : '' ?>" aria-current="<?= ($i == 0) ? 'true' : 'false' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
            <?php
                $i++;
            endwhile;
        else:
            ?>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <?php endif; ?>
    </div>

    <div class="carousel-inner">
        <?php
        if ($banner_res && mysqli_num_rows($banner_res) > 0):
            $j = 0;
            mysqli_data_seek($banner_res, 0);
            while ($banner = mysqli_fetch_assoc($banner_res)):
                $bannerImg = !empty($banner['banner_path']) ? $banner['banner_path'] : 'assets/images/black.png';
        ?>
                <div class="carousel-item <?= ($j == 0) ? 'active' : '' ?>" data-bs-interval="5000">
                    <div class="slide-bg" style="background-image: url('admin/<?= htmlspecialchars($bannerImg) ?>');"></div>
                    <div class="carousel-caption">
                        <div class="container">
                            <h2 class="hero-title"><?= htmlspecialchars($banner['title']) ?></h2>
                            <p><?= htmlspecialchars($banner['description']) ?></p>
                            <div>
                                <a href="<?= !empty($banner['link_url']) ? htmlspecialchars($banner['link_url']) : 'products.php' ?>" class="btn-primary-custom">Explore Products</a>
                                <a href="contact.php" class="btn-outline-custom">Contact an Expert</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                $j++;
            endwhile;
        else:
            ?>
            <div class="carousel-item active" data-bs-interval="5000">
                <div class="slide-bg" style="background-image: url('assets/images/banner1.jpg');"></div>
                <div class="carousel-caption">
                    <div class="container">
                        <h2 class="hero-title">Premium Indian Spices <br><span style="color: #711b3c;">& Dry Fruits</span></h2>
                        <p>Bhagirath Enterprise exports the finest quality agricultural products worldwide with unmatched purity.</p>
                        <div>
                            <a href="products.php" class="btn-primary-custom">Explore Products</a>
                            <a href="contact.php" class="btn-outline-custom">Contact an Expert</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- About Us Section (Dynamic from about_sections table) -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-img-wrapper">
                    <?php 
                    // Database se image path fetch karna (agar khali ho toh default image dikhegi)
                    $aboutImg = !empty($about_data['image_url']) ? 'admin/' . $about_data['image_url'] : 'assets/images/about.jpg';
                    ?>
                    <img src="<?= htmlspecialchars($aboutImg); ?>" alt="<?= !empty($about_data['title']) ? htmlspecialchars($about_data['title']) : 'Bhagirath Enterprise Premium Quality'; ?>" onerror="this.src='https://images.unsplash.com/photo-1596040033229-a9821ebd058d?q=80&w=800&auto=format&fit=crop'">
                    <div class="about-experience">
                        <h3 class="mb-0">100%</h3>
                        <p class="mb-0 small">Authentic Quality</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <span class="text-uppercase" style="color: #711b3c; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Who We Are</span>
                
                <!-- Dynamic Title from database -->
                <h1 class="section-title mb-4 h2">
                    <?= !empty($about_data['title']) ? htmlspecialchars($about_data['title']) : 'Exporting the Finest Flavors & Agricultural Wealth of India'; ?>
                </h1>
                
                <!-- Dynamic Description/Content from database -->
                <div class="text-muted-custom mb-4">
                    <?php 
                    if (!empty($about_data['content'])) {
                        // Agar admin ne rich text / HTML tags ke sath content save kiya hai toh usko render karega
                        echo $about_data['content']; 
                    } else {
                        // Fallback text agar table khali ho
                        echo '<p>At <strong>Bhagirath Enterprise</strong>, we specialize in processing and exporting premium quality whole spices, dry fruits, and authentic Indian agricultural products.</p>';
                    }
                    ?>
                </div>
                
                <a href="about.php" class="btn btn-quote" style="background-color:#222222; border-color:#222222; color: white; padding: 10px 25px; border-radius: 5px;">Read More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Categories Section -->
<section class="section-padding bg-light-grey">
    <div class="container">
        <div class="text-center mb-5 d-flex flex-column">
            <span class="text-uppercase" style="color: #711b3c; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Shop By Category</span>
            <h2 class="section-title mx-auto">Our Premium Categories</h2>
            <p class="text-muted-custom mt-3 max-w-700 mx-auto" style="max-width: 600px;">Explore our diverse range of high-quality, farm-fresh agricultural categories, carefully sourced to meet global standards.</p>
        </div>

        <div class="row g-4">
            <?php
            if ($categories_res && mysqli_num_rows($categories_res) > 0):
                while ($cat = mysqli_fetch_assoc($categories_res)):
                    $catImg = !empty($cat['image']) ? 'admin/uploads/category/' . $cat['image'] : 'assets/images/black.png';
                    $catSlug = !empty($cat['slug_url']) ? $cat['slug_url'] : $cat['cate_id'];
            ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="service-card h-100 bg-white shadow-sm rounded overflow-hidden text-center">
                            <div class="service-img-container" style="height: 250px; overflow: hidden; background-color: #f8f9fa;">
                                <img src="<?= htmlspecialchars($catImg) ?>" alt="<?= htmlspecialchars($cat['categories']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='assets/images/black.png'">
                            </div>
                            <div class="card-body p-4">
                                <h3 class="service-title" style="color: #222222; font-weight: 700; font-size: 1.5rem;"><?= htmlspecialchars($cat['categories']) ?></h3>
                                <p class="text-muted-custom small mb-4">
                                    <?= htmlspecialchars(substr($cat['meta_desc'], 0, 80)) ?>...
                                </p>
                                <a href="products.php?category=<?= urlencode($catSlug) ?>" class="btn-quote-outline d-inline-block mt-2">View Category</a>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <div class="col-12 text-center text-muted">No categories available right now.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5 d-flex flex-column">
            <span class="text-uppercase" style="color: #711b3c; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Why Bhagirath Enterprise</span>
            <h2 class="section-title mx-auto">The Trusted Choice for Global Exports</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                    <h5 class="feature-title">Certified Quality</h5>
                    <p class="text-muted-custom small mb-0">Our products meet rigorous global food safety standards ensuring 100% purity and authenticity.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-globe"></i></div>
                    <h5 class="feature-title">Global Export</h5>
                    <p class="text-muted-custom small mb-0">Seamless international logistics and timely delivery to our clients across the globe.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-basket"></i></div>
                    <h5 class="feature-title">Farm Fresh Sourcing</h5>
                    <p class="text-muted-custom small mb-0">Ethically sourced directly from the finest Indian farms to preserve natural aroma and taste.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <h5 class="feature-title">Competitive Pricing</h5>
                    <p class="text-muted-custom small mb-0">Premium quality agricultural and food exports offered at the best international market rates.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Products Section -->
<section class="section-padding" style="background-color: #ffffff;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <span class="text-uppercase d-flex flex-column" style="color: #711b3c; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Our Produce</span>
                <h2 class="section-title mb-0">Premium Export Products</h2>
            </div>
            <div class="d-none d-md-block">
                <a href="products.php" class="btn btn-outline-dark" style="border-radius: 20px; font-weight: 600;">View All Products</a>
            </div>
        </div>

        <div class="row g-4">
            <?php
            if ($products_res && mysqli_num_rows($products_res) > 0):
                while ($prod = mysqli_fetch_assoc($products_res)):
                    $proImg = !empty($prod['pro_img']) ? 'admin/assets/img/uploads/' . $prod['pro_img'] : 'assets/images/black.png';
                    
                    // Slug check: Agar slug_url database mein khali hai toh fallback ke liye id use karega
                    $productSlug = !empty($prod['slug_url']) ? $prod['slug_url'] : $prod['id'];
            ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card h-100 shadow-sm border rounded overflow-hidden">
                            <span class="product-badge">Export Grade</span>
                            <div class="product-img-wrapper" style="height: 200px; overflow: hidden;">
                                <a href="product-details.php?slug=<?php echo urlencode($productSlug); ?>">
                                <img src="<?= htmlspecialchars($proImg) ?>" alt="<?= htmlspecialchars($prod['pro_name']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='assets/images/black.png'">
                                </a>
                            </div>
                            <div class="p-4">
                                <a href="product-details.php?slug=<?php echo urlencode($productSlug); ?>" style="text-decoration:none;"> 
                                <h3 class="product-title" style="font-size: 1.05rem; font-weight: 700; height: 48px; overflow: hidden;">
                                    <?= htmlspecialchars($prod['pro_name']) ?>
                                </h3>
                                </a>
                                <div class="mb-3">
                                    <a href="product-details.php?slug=<?php echo urlencode($productSlug); ?>" class="view-details-link">View Details <i class="bi bi-chevron-right" style="font-size: 0.8rem;"></i></a>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="tel:+918448211202" class="btn-call" title="Call for inquiry">
                                        <i class="bi bi-telephone-fill"></i>
                                    </a>
                                    <a href="contact.php?product=<?= urlencode($prod['pro_name']) ?>" class="btn btn-quote-full flex-grow-1 text-center py-2 text-decoration-none">Inquire Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <div class="col-12 text-center text-muted">No products found.</div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4 d-block d-md-none">
            <a href="products.php" class="btn btn-outline-dark" style="border-radius: 20px; font-weight: 600;">View All Products</a>
        </div>
    </div>
</section>

<!-- Dynamic Testimonials Section -->
<section class="section-padding" style="background-color: #fdfdfd; border-top: 1px solid #f0f0f0;">
    <div class="container">
        <div class="text-center mb-5 d-flex flex-column">
            <span class="text-uppercase" style="color: #711b3c; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Client Feedback</span>
            <h2 class="section-title mx-auto">What Our Trusted Partners Say</h2>
        </div>
        
        <div class="row g-4 justify-content-center">
            <?php 
            if ($test_res && mysqli_num_rows($test_res) > 0): 
                while($test = mysqli_fetch_assoc($test_res)):
                    $testImg = !empty($test['image']) ? 'admin/uploads/testimonials/' . $test['image'] : 'assets/images/clove.png';
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card p-4 bg-white shadow-sm rounded-4 h-100 position-relative transition-up">
                    <i class="bi bi-quote position-absolute" style="font-size: 5rem; color: rgba(113, 27, 60, 0.05); top: -10px; right: 20px; z-index: 0;"></i>
                    
                    <div class="position-relative z-1">
                        <div class="d-flex align-items-center mb-4">
                            <div class="test-img-wrap rounded-circle overflow-hidden me-3 shadow-sm" style="width: 65px; height: 65px; border: 3px solid #f8f9fa;">
                                <img src="<?= htmlspecialchars($testImg) ?>" alt="<?= htmlspecialchars($test['name']) ?>" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='assets/images/default-avatar.png'">
                            </div>
                            <div>
                                <h4 class="mb-0" style="color: #222222; font-weight: 700; font-size: 1.1rem;"><?= htmlspecialchars($test['name']) ?></h4>
                                <span class="text-muted small fw-semibold" style="color: #711b3c !important;"><?= htmlspecialchars($test['designation']) ?></span>
                            </div>
                        </div>
                        <div class="stars mb-2" style="color: #FFD700; font-size: 0.9rem;">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-muted-custom small mb-0" style="font-style: italic; line-height: 1.6;">
                            "<?= htmlspecialchars($test['message']) ?>"
                        </p>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="col-12 text-center text-muted">Client reviews will be updated shortly.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Dynamic Brands Slider Section -->
<section class="brands-slider-section py-5" style="background-color: #f8f9fa; border-top: 1px solid #eaeaea;">
    <div class="container">
        <h2 class="text-center mb-5" style="color: #222222; font-weight: 700; font-size: 1.5rem; letter-spacing: 1px;">OUR TRUSTED CLIENTS & PARTNERS</h2>
        
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
                    <div class="brand-slide"><h4 class="brand-logo">FSSAI</h4></div>
                    <div class="brand-slide"><h4 class="brand-logo">APEDA</h4></div>
                    <div class="brand-slide"><h4 class="brand-logo">SPICES BOARD</h4></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include ('includes/inquiry-form.php');?>

<!-- 6. FREQUENTLY ASKED QUESTIONS -->
<section class="section-padding" style="background-color: #fdfdfd;">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-lg-8 reveal">
                <div class="text-center mb-5">
                    <span class="sec-subtitle text-uppercase fw-bold" style="color: #E3000F; letter-spacing: 1px; font-size: 14px;">Clear Your Doubts</span>
                    <h2 class="sec-title" style="color: #17385A; font-weight: 700;">Frequently Asked Questions</h2>
                </div>

                <!-- Bootstrap 5 Accordion -->
                <div class="accordion faq-accordion shadow-sm" id="exportFaqAccordion">

                    <!-- FAQ Item 1 -->
                    <div class="accordion-item border-0 mb-3 rounded overflow-hidden">
                        <h3 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1" style="font-weight: 600; color: #17385A; background-color: #f8f9fa;">
                                Are your agricultural products certified for global export?
                            </button>
                        </h3>
                        <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#exportFaqAccordion">
                            <div class="accordion-body text-muted small">
                                Yes, absolutely. Bhagirath Enterprise strictly complies with global food safety standards. Our exports are backed by necessary quality checks and certifications to clear customs smoothly in your destination country.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="accordion-item border-0 mb-3 rounded overflow-hidden">
                        <h3 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2" style="font-weight: 600; color: #17385A; background-color: #f8f9fa;">
                                Do you handle B2B bulk orders and container shipments?
                            </button>
                        </h3>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#exportFaqAccordion">
                            <div class="accordion-body text-muted small">
                                Yes, our core expertise lies in B2B wholesale and bulk container shipments (FCL/LCL). We supply high volumes of dry fruits, whole spices, and other commodities tailored to your commercial needs.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="accordion-item border-0 mb-3 rounded overflow-hidden">
                        <h3 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3" style="font-weight: 600; color: #17385A; background-color: #f8f9fa;">
                                What is your Minimum Order Quantity (MOQ)?
                            </button>
                        </h3>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#exportFaqAccordion">
                            <div class="accordion-body text-muted small">
                                The Minimum Order Quantity (MOQ) varies depending on the specific product and the shipping method. Please reach out to our sales team at bhagirathenterprise7@gmail.com for exact product-wise MOQs.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="accordion-item border-0 mb-3 rounded overflow-hidden">
                        <h3 class="accordion-header" id="faqHeading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4" style="font-weight: 600; color: #17385A; background-color: #f8f9fa;">
                                Do you offer customized or private label packaging?
                            </button>
                        </h3>
                        <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#exportFaqAccordion">
                            <div class="accordion-body text-muted small">
                                Yes, we offer customized packaging solutions, including bulk PP bags, jute bags, vacuum packs, and private labeling for retail brands. Let us know your packaging requirements during the inquiry process.
                            </div>
                        </div>
                    </div>

                </div> <!-- End Accordion -->
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