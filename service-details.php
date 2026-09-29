<?php
require_once 'config/connect.php'; 

$service = null;

// 1. Fetch Service by Slug or Name
if (isset($_GET['slug']) && !empty(trim($_GET['slug']))) {
    $slug = $conn->real_escape_string(trim($_GET['slug']));
    $plain_name = str_replace('-', ' ', $slug);

    $query = "SELECT * FROM services WHERE 
              slug_url = '$slug' 
              OR service_name = '$slug' 
              OR service_name LIKE '%$plain_name%' 
              LIMIT 1";
              
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $service = $result->fetch_assoc();
    }
} 

if (!$service && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $service_id = intval($_GET['id']);
    $query = "SELECT * FROM services WHERE id = $service_id LIMIT 1";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $service = $result->fetch_assoc();
    }
}

if (!$service) {
    die("<div class='container py-5 text-center'><h3>Service Not Found!</h3><p><a href='services.php' class='btn btn-primary'>Back to Services</a></p></div>");
}

$short_desc = isset($service['short_desc']) && $service['short_desc'] !== null ? $service['short_desc'] : '';
$long_desc = isset($service['long_desc']) && $service['long_desc'] !== null ? $service['long_desc'] : '<p>Detailed description is currently unavailable.</p>';
$service_name = isset($service['service_name']) ? $service['service_name'] : 'Service Details';

$contact_query = $conn->query("SELECT phone, wp_number, email FROM contacts LIMIT 1");
$contact_info = $contact_query ? $contact_query->fetch_assoc() : ['phone' => '', 'wp_number' => '', 'email' => ''];
$wa_number = preg_replace('/[^0-9]/', '', $contact_info['wp_number'] ?? '');
$wa_message = urlencode("Hi SS Bouncers, I want to know more about the " . $service_name . ".");

// 5. SEO Metadata
$pageTitle = !empty($service['meta_title']) ? htmlspecialchars($service['meta_title']) : htmlspecialchars($service_name) . " | SS Bouncers";
$meta_description = !empty($service['meta_desc']) ? htmlspecialchars($service['meta_desc']) : htmlspecialchars(mb_strimwidth(strip_tags($short_desc), 0, 150, "..."));
$meta_keywords = !empty($service['meta_key']) ? htmlspecialchars($service['meta_key']) : strtolower(str_replace(' ', ', ', $service_name)) . ", security services, bouncers";

// 6. Image Path
$imagePath = !empty($service['img_path']) ? $site . 'admin/assets/img/uploads/' . $service['img_path'] : 'assets/images/default-service-large.jpg';

include 'includes/header.php'; 
include 'includes/breadcrumb.php'; 
?>

<style>
    /* ================== SERVICE DETAILS CSS ================== */
    .service-detail-img-wrapper img {
        transition: transform 0.8s ease;
    }

    .service-detail-img-wrapper:hover img {
        transform: scale(1.05);
    }

    .premium-badge {
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.1);
    }

    .long-desc-content {
        font-size: 1.05rem;
        line-height: 1.8;
    }

    .long-desc-content p {
        margin-bottom: 1.5rem;
    }

    .long-desc-content h2, 
    .long-desc-content h3, 
    .long-desc-content h4 {
        color: var(--primary-color);
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .long-desc-content ul, 
    .long-desc-content ol {
        padding-left: 0;
        margin-bottom: 1.8rem;
        list-style: none;
    }

    .long-desc-content ul li {
        position: relative;
        padding-left: 28px;
        margin-bottom: 12px;
    }

    .long-desc-content ul li::before {
        content: '\f111';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        left: 0;
        top: 5px;
        font-size: 8px;
        color: var(--secondary-color);
    }

    .sidebar-contact-item {
        position: relative;
        transition: var(--transition-smooth);
        background-color: var(--bg-white);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .sidebar-contact-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        border-color: var(--secondary-color);
    }

    .sidebar-contact-item:hover .icon-box {
        transform: scale(1.1);
        transition: var(--transition-smooth);
    }
</style>

<!-- ==================== SERVICE DETAILS SECTION ==================== -->
<section class="section-padding py-5" style="background-color: #f8f9fa;">
    <div class="container py-4">
        <div class="row g-5">
            
            <!-- Left Column: Content -->
            <div class="col-lg-8">
                <!-- Main Image -->
                <div class="service-detail-img-wrapper position-relative rounded-4 overflow-hidden shadow-sm mb-5">
                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($service_name) ?>" class="img-fluid w-100 object-fit-cover" style="max-height: 450px;">
                    <div class="premium-badge position-absolute top-0 start-0 m-4 bg-primary-dark text-white px-4 py-2 rounded-pill shadow-lg">
                        <i class="fa-solid fa-shield-halved text-secondary-accent me-2"></i> Verified Service
                    </div>
                </div>
                
                <!-- Content Box -->
                <div class="service-content-box bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light">
                    <h2 class="text-primary-dark fw-bold mb-4">Overview</h2>
                    
                    <?php if(!empty($short_desc)): ?>
                    <!-- Short Description -->
                    <div class="lead text-muted mb-4 border-start border-4 border-secondary-accent ps-4 fst-italic">
                        <?= htmlspecialchars($short_desc) ?>
                    </div>
                    <hr class="my-4 opacity-10">
                    <?php endif; ?>
                    
                    <!-- Long Description (HTML content from DB) -->
                    <div class="long-desc-content text-muted">
                        <?= $long_desc ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-wrapper sticky-top" style="top: 100px; z-index: 10;">

                    <!-- Additional Services List Widget -->
                    <div class="sidebar-widget bg-white p-4 rounded-4 shadow-sm border border-light mb-4" style="border-top: 5px solid var(--primary-color) !important;">
                        <h4 class="text-primary-dark fw-bold mb-4 pb-2 border-bottom">Other Services</h4>
                        <ul class="list-unstyled mb-0 category-list">
                            <?php
                            $sidebar_services = $conn->query("SELECT service_name, slug_url FROM services WHERE status = 1 ORDER BY display_order ASC LIMIT 5");
                            if($sidebar_services && $sidebar_services->num_rows > 0) {
                                while($s = $sidebar_services->fetch_assoc()) {
                                    $s_slug = !empty($s['slug_url']) ? $s['slug_url'] : preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($s['service_name'])));
                                    $isActive = ($service_name == $s['service_name']) ? 'active' : '';
                            ?>
                            <li class="mb-2">
                                <a href="service-details.php?slug=<?= htmlspecialchars($s_slug) ?>" class="d-flex justify-content-between align-items-center text-decoration-none text-muted p-2 rounded transition-hover <?= $isActive ?>" style="<?= $isActive ? 'background-color: var(--primary-color); color: var(--secondary-color) !important;' : '' ?>">
                                    <?= htmlspecialchars($s['service_name']) ?> <i class="fa-solid fa-angle-right"></i>
                                </a>
                            </li>
                            <?php 
                                }
                            } 
                            ?>
                            <li class="mt-3 text-center">
                                <a href="services.php" class="fw-bold text-secondary-accent text-decoration-none">View All Services <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            </li>
                        </ul>
                    </div>

                    <!-- Direct Contact Widget -->
                    <div class="sidebar-widget bg-white p-4 rounded-4 shadow-sm border border-light">
                        <h5 class="text-primary-dark fw-bold mb-4 pb-2 border-bottom">Need Immediate Help?</h5>
                        
                        <!-- Call Option -->
                        <?php if(!empty($contact_info['phone'])): ?>
                        <div class="d-flex align-items-center mb-4 p-3 rounded-3 sidebar-contact-item">
                            <div class="icon-box me-3 rounded-circle d-flex align-items-center justify-content-center bg-light shadow-sm text-primary-dark" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                <i class="fa-solid fa-phone-volume"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted small text-uppercase tracking-wider">Call 24/7</h6>
                                <a href="tel:<?= htmlspecialchars($contact_info['phone']) ?>" class="text-decoration-none text-primary-dark fw-bold fs-5 stretched-link"><?= htmlspecialchars($contact_info['phone']) ?></a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- WhatsApp Option -->
                        <?php if(!empty($contact_info['wp_number'])): ?>
                        <div class="d-flex align-items-center p-3 rounded-3 sidebar-contact-item" style="border-left: 4px solid #25D366;">
                            <div class="icon-box me-3 rounded-circle d-flex align-items-center justify-content-center bg-light shadow-sm" style="width: 50px; height: 50px; font-size: 1.5rem; color: #25D366;">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted small text-uppercase tracking-wider">WhatsApp</h6>
                                <a href="https://wa.me/<?= $wa_number ?>?text=<?= $wa_message ?>" target="_blank" class="text-decoration-none fw-bold fs-5 stretched-link" style="color: #128C7E;">Chat with Us</a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/inquiry-form.php'; ?>
<?php include 'includes/footer.php'; ?>