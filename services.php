<?php
include 'config/connect.php'; 

$limit = 6; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total records count karne ke liye
$total_query = mysqli_query($conn, "SELECT COUNT(id) as total FROM services");
$total_row = mysqli_fetch_assoc($total_query);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Fetching services with Limit & Offset
$query = "SELECT * FROM services ORDER BY id DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);

// --- STATIC SEO FOR MAIN SERVICES PAGE ---
$pageTitle = "EURASIASTONEINDIA - Global Bulk Supply";
$meta_description = "Explore premium agricultural export services, bulk supply, and logistics solutions by EURASIASTONEINDIA. We ensure 100% pure spices reach global markets safely.";
$meta_keywords = "agricultural export services, bulk spice supply, global export logistics, EURASIASTONEINDIA services, private labeling spices";

include 'includes/header.php'; 
include 'includes/breadcrumb.php'; 
?>

<!-- SERVICES LISTING SECTION -->
<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="row g-4">
            
            <?php 
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // Assuming images are stored in 'uploads/' folder
                    $imagePath = !empty($row['img_path']) ? 'admin/assets/img/uploads/' . $row['img_path'] : 'assets/images/default-service.jpg';
            ?>
            
            <!-- Service Card -->
            <div class="col-lg-4 col-md-6 reveal">
                <div class="card h-100 shadow-sm border-0 service-card-premium" style="border-radius: 10px; overflow: hidden; transition: 0.3s;">
                    <div class="img-wrapper" style="height: 220px; overflow: hidden;">
                        <a href="service-details.php?id=<?= $row['id'] ?>">
                            <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($row['service_name']) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                        </a>
                    </div>
                    <div class="card-body p-4 text-center">
                        <a href="service-details.php?id=<?= $row['id'] ?>" class="text-decoration-none">
                            <h4 class="card-title" style="color: #17385A; font-weight: 700;"><?= htmlspecialchars($row['service_name']) ?></h4>
                        </a>
                        <p class="card-text text-muted mb-4" style="font-size: 0.95rem;">
                            <?= htmlspecialchars(substr($row['short_desc'], 0, 100)) ?>...
                        </p>
                        <a href="service-details.php?id=<?= $row['id'] ?>" class="btn px-4 py-2" style="background-color: #E3000F; color: white; border-radius: 30px; font-weight: 600;">
                            Read More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <?php 
                } 
            } else {
                echo "<div class='col-12 text-center'><h3 class='text-muted'>No services found at the moment.</h3></div>";
            }
            ?>

        </div>

        <!-- PAGINATION SECTION -->
        <?php if($total_pages > 1): ?>
        <div class="row mt-5 reveal">
            <div class="col-12 d-flex justify-content-center">
                <nav aria-label="Service Pagination">
                    <ul class="pagination pagination-lg">
                        
                        <!-- Previous Button -->
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= ($page - 1) ?>" aria-label="Previous" style="color: #17385A;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>" style="<?= ($page == $i) ? 'background-color: #17385A; border-color: #17385A; color: white;' : 'color: #17385A;' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= ($page + 1) ?>" aria-label="Next" style="color: #17385A;">
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

<!-- Custom CSS for Service Card Hover Effect -->
<style>
    .service-card-premium:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .service-card-premium:hover .img-wrapper img {
        transform: scale(1.1);
    }
</style>

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