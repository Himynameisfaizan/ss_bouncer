<?php
require_once 'config/connect.php';

// Pagination setup based on your reference[cite: 7]
$limit = 6; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search and Category Filter Logic
$whereClause = "WHERE status = 1";
$search_query = "";
$category_filter = "";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = $conn->real_escape_string(trim($_GET['search']));
    $whereClause .= " AND (service_name LIKE '%$search_query%' OR short_desc LIKE '%$search_query%')";
} elseif (isset($_GET['category']) && !empty(trim($_GET['category']))) {
    $category_filter = $conn->real_escape_string(trim($_GET['category']));
    // Smart filtering based on keywords in service name since there's no category column
    $whereClause .= " AND service_name LIKE '%$category_filter%'";
}

// Total records count for pagination[cite: 7]
$total_sql = "SELECT COUNT(id) as total FROM services $whereClause";
$total_query = $conn->query($total_sql);
$total_row = $total_query->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Fetching services with Limit & Offset[cite: 7]
$query = "SELECT * FROM services $whereClause ORDER BY display_order ASC, id DESC LIMIT $offset, $limit";
$result = $conn->query($query);

// Fetch Contact Data for WhatsApp
$contact_query = $conn->query("SELECT phone, wp_number FROM contacts LIMIT 1");
$contact_info = $contact_query ? $contact_query->fetch_assoc() : ['phone' => '', 'wp_number' => ''];
$wa_number = preg_replace('/[^0-9]/', '', $contact_info['wp_number']);

// SEO Meta Data
$pageTitle = "Our Security Services | SS Bouncers";
$meta_description = "Explore premium security guard and housekeeping services by SS Bouncers. We provide verified and professional security for corporates, events, and residential areas.";

include 'includes/header.php'; 
include 'includes/breadcrumb.php'
?>

<!-- ==================== SERVICES LISTING SECTION ==================== -->
<section class="section-padding py-5 bg-light-custom">
    <div class="container-ng py-4">
        <div class="row g-5">
            
            <!-- LEFT SIDEBAR: Search & Categories -->
            <div class="col-lg-3">
                <div class="sidebar-wrapper sticky-top" style="top: 100px; z-index: 10;">
                    
                    <!-- Search Widget -->
                    <div class="sidebar-widget bg-white p-4 rounded-4 shadow-sm border border-light mb-4">
                        <h4 class="widget-title text-primary-dark fw-bold mb-3 pb-2 border-bottom">Search Services</h4>
                        <form action="services.php" method="GET" class="search-form position-relative">
                            <input type="text" name="search" class="form-control custom-search-input pe-5" placeholder="Search here..." value="<?= htmlspecialchars($search_query) ?>" required>
                            <button type="submit" class="search-btn text-secondary-accent position-absolute top-50 end-0 translate-middle-y me-3 bg-transparent border-0">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Categories Widget -->
                    <div class="sidebar-widget bg-white p-4 rounded-4 shadow-sm border border-light">
                        <h4 class="widget-title text-primary-dark fw-bold mb-3 pb-2 border-bottom">Categories</h4>
                        <ul class="category-list list-unstyled mb-0">
                            <!-- 'All' category clears the filter -->
                            <li>
                                <a href="services.php" class="<?= (empty($search_query) && empty($category_filter)) ? 'active' : '' ?>">All Services <i class="fa-solid fa-angle-right"></i></a>
                            </li>
                            <li>
                                <a href="?category=Corporate" class="<?= ($category_filter == 'Corporate' || $category_filter == 'Office') ? 'active' : '' ?>">Corporate Security <i class="fa-solid fa-angle-right"></i></a>
                            </li>
                            <li>
                                <a href="?category=Residential" class="<?= ($category_filter == 'Residential' || $category_filter == 'House' || $category_filter == 'Appartment') ? 'active' : '' ?>">Residential Security <i class="fa-solid fa-angle-right"></i></a>
                            </li>
                            <li>
                                <a href="?category=Industrial" class="<?= ($category_filter == 'Industrial' || $category_filter == 'Warehouse' || $category_filter == 'Godown') ? 'active' : '' ?>">Industrial Security <i class="fa-solid fa-angle-right"></i></a>
                            </li>
                            <li>
                                <a href="?category=Event" class="<?= ($category_filter == 'Event' || $category_filter == 'Bouncer') ? 'active' : '' ?>">Event Bouncers <i class="fa-solid fa-angle-right"></i></a>
                            </li>
                            <li>
                                <a href="?category=Housekeeping" class="<?= ($category_filter == 'Housekeeping') ? 'active' : '' ?>">Housekeeping <i class="fa-solid fa-angle-right"></i></a>
                            </li>
                        </ul>
                    </div>
                    
                </div>
            </div>

            <!-- RIGHT SIDE: Services Grid -->
            <div class="col-lg-9">
                
                <?php if(!empty($search_query) || !empty($category_filter)): ?>
                    <div class="mb-4 pb-2 border-bottom">
                        <h5 class="text-muted">Showing results for: <span class="text-primary-dark fw-bold">"<?= htmlspecialchars(!empty($search_query) ? $search_query : $category_filter) ?>"</span> (<?= $total_records ?> found)</h5>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php 
                    if($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // Slug validation
                            $slug = !empty($row['slug_url']) ? $row['slug_url'] : preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($row['service_name'])));
                            $service_url = "service-details.php?slug=" . $slug;
                            
                            $imagePath = !empty($row['img_path']) ? $site . 'admin/assets/img/uploads/' . $row['img_path'] : 'assets/images/default-service.jpg';
                            
                            // WhatsApp Message
                            $wa_message = urlencode("Hi SS Bouncers, I want to know more about the " . $row['service_name'] . ".");
                    ?>
                    
                    <!-- Premium Service Card -->
                    <div class="col-md-6 reveal">
                        <div class="card h-100 shadow-sm border-0 service-card-premium bg-white rounded-4 overflow-hidden">
                            <!-- Image Section -->
                            <div class="img-wrapper position-relative" style="height: 240px; overflow: hidden;">
                                <a href="<?= $service_url ?>" class="d-block h-100">
                                    <img src="<?= $imagePath ?>" class="card-img-top w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($row['service_name']) ?>">
                                </a>
                                <!-- Overlay Badge -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-secondary-accent text-primary-dark fw-bold px-3 py-2 rounded-pill shadow-sm">Premium</span>
                                </div>
                            </div>
                            
                            <!-- Content Section -->
                            <div class="card-body p-4 d-flex flex-column">
                                <a href="<?= $service_url ?>" class="text-decoration-none">
                                    <h4 class="card-title text-primary-dark fw-bold mb-3 service-title-hover"><?= htmlspecialchars($row['service_name']) ?></h4>
                                </a>
                                <p class="card-text text-muted mb-4 flex-grow-1" style="font-size: 0.95rem; line-height: 1.6;">
                                    <?= htmlspecialchars(mb_strimwidth($row['short_desc'], 0, 110, "...")) ?>
                                </p>
                                
                                <!-- Read More Link -->
                                <a href="<?= $service_url ?>" class="read-more-link fw-bold text-primary-dark mb-4 d-inline-block">
                                    Read Full Details <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                                    <!-- Request Call (Auto-selects service in contact form) -->
                                    <a href="contact.php?service=<?= $slug ?>" class="btn btn-outline-primary-custom flex-grow-1 me-2 py-2 text-center" style="font-size: 14px;">
                                        Request To Call
                                    </a>
                                    <!-- Direct WhatsApp -->
                                    <a href="https://wa.me/<?= $wa_number ?>?text=<?= $wa_message ?>" target="_blank" class="btn btn-whatsapp py-2 px-3" title="Chat on WhatsApp">
                                        <i class="fa-brands fa-whatsapp fs-5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php 
                        } 
                    } else {
                        echo "<div class='col-12 text-center py-5'>
                                <i class='fa-solid fa-folder-open text-muted opacity-50 display-1 mb-3'></i>
                                <h3 class='text-primary-dark fw-bold'>No services found.</h3>
                                <p class='text-muted'>Try adjusting your search or selecting a different category.</p>
                                <a href='services.php' class='btn btn-premium mt-3'>View All Services</a>
                              </div>";
                    }
                    ?>
                </div>

                <!-- ==================== PAGINATION ==================== -->
                <?php if($total_pages > 1): ?>
                <div class="row mt-5 reveal">
                    <div class="col-12">
                        <nav aria-label="Service Pagination">
                            <ul class="pagination justify-content-center custom-pagination">
                                
                                <!-- Previous -->
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <?php $prev_url = "?page=" . ($page - 1) . (!empty($search_query) ? "&search=$search_query" : "") . (!empty($category_filter) ? "&category=$category_filter" : ""); ?>
                                    <a class="page-link" href="<?= $prev_url ?>" aria-label="Previous">
                                        <i class="fa-solid fa-angle-left"></i>
                                    </a>
                                </li>

                                <!-- Numbers -->
                                <?php for($i = 1; $i <= $total_pages; $i++): 
                                    $page_url = "?page=" . $i . (!empty($search_query) ? "&search=$search_query" : "") . (!empty($category_filter) ? "&category=$category_filter" : "");
                                ?>
                                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= $page_url ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Next -->
                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                    <?php $next_url = "?page=" . ($page + 1) . (!empty($search_query) ? "&search=$search_query" : "") . (!empty($category_filter) ? "&category=$category_filter" : ""); ?>
                                    <a class="page-link" href="<?= $next_url ?>" aria-label="Next">
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>
                                </li>
                                
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php endif; ?>

            </div>
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