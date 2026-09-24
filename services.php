<?php
include "admin/db-conn.php";
$pageTitle = "Our Services";
include("includes/header.php");
include("includes/breadcrumb.php");

$contact_query = mysqli_query($conn, "SELECT phone, wp_number FROM contacts LIMIT 1");
$contact_data = mysqli_fetch_assoc($contact_query);
$phone_number = !empty($contact_data['phone']) ? $contact_data['phone'] : '917200864976'; //[cite: 1]
$wp_number = !empty($contact_data['wp_number']) ? $contact_data['wp_number'] : '917200864976'; //[cite: 1]

$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

$total_records_query = mysqli_query($conn, "SELECT COUNT(id) AS total FROM services");
$total_records_data = mysqli_fetch_assoc($total_records_query);
$total_records = $total_records_data['total'];
$total_pages = ceil($total_records / $limit);

// Agar user total pages se bada number URL me pass kare
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
}

$offset = ($page - 1) * $limit;

// 3. Current page ki services fetch karna
$services_query = mysqli_query($conn, "SELECT * FROM services ORDER BY id ASC LIMIT $offset, $limit");
?>

<!-- Services Grid Section -->
<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Comprehensive <span>Security Solutions</span></h2>
            <p class="text-muted">We offer a wide range of professional security and housekeeping services customized
                for various sectors.</p>
        </div>

        <div class="row g-4">
            <?php
            if ($services_query && mysqli_num_rows($services_query) > 0) {
                $delay = 100;
                while ($service = mysqli_fetch_assoc($services_query)) {

                    // Image Path Handling
                    $raw_img = $service['img_path'];
                    if (!empty($raw_img)) {
                        if (strpos($raw_img, 'admin/') === 0) {
                            $img_src = $raw_img;
                        } elseif (strpos($raw_img, 'uploads/') === 0) {
                            $img_src = 'admin/' . $raw_img;
                        } else {
                            $img_src = 'admin/assets/img/uploads/' . $raw_img;
                        }
                    } else {
                        $img_src = 'assets/images/services/default.jpeg';
                    }

                    $service_title = htmlspecialchars($service['service_name']);
                    $short_desc = htmlspecialchars($service['short_desc']);
                    $whatsapp_msg = urlencode("Hello, I am interested in your " . $service['service_name'] . ".");
                    $details_link = "service-details.php?id=" . $service['id'];
            ?>
                    <!-- Service Card Item -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $delay; ?>">
                        <div class="service-card h-100 d-flex flex-column">
                            <a href="<?= $details_link; ?>" class="read-more-link">
                                <img src="<?= $img_src; ?>" alt="<?= $service_title; ?>" class="service-img" loading="lazy">
                            </a>
                            <div class="service-content d-flex flex-column flex-grow-1">
                                <div class="service-icon-box">
                                    <i class="fas fa-shield-alt service-icon"></i>
                                    <a href="<?= $details_link; ?>" class="read-more-link">
                                        <h4 class="service-title"><?= $service_title; ?></h4>
                                    </a>
                                </div>
                                <p class="text-muted small mb-4 flex-grow-1"><?= $short_desc; ?></p>

                                <div class="service-footer mt-auto">
                                    <a href="<?= $details_link; ?>" class="read-more-link">
                                        Read More <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                    <a href="https://wa.me/<?= $wp_number; ?>?text=<?= $whatsapp_msg; ?>" target="_blank"
                                        class="whatsapp-btn" title="Chat on WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    $delay = ($delay >= 300) ? 100 : $delay + 100;
                }
            } else {
                ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No services found.</p>
                </div>
            <?php } ?>
        </div>

        <!-- Bootstrap 5 Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="row mt-5" data-aos="fade-up">
                <div class="col-12">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">

                            <!-- Previous Page Link -->
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next Page Link -->
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>

                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include("includes/footer.php"); ?>