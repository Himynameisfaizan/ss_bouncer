<?php
include "admin/db-conn.php";
$pageTitle = "Our Blog & News";
include 'includes/header.php';
include 'includes/breadcrumb.php';

// Pagination Configuration
$limit = 6; // Ek page par 6 blogs show honge
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// Total published blogs count karna
$total_records_query = mysqli_query($conn, "SELECT COUNT(id) AS total FROM blogs WHERE status = 'published'"); //
$total_records_data = mysqli_fetch_assoc($total_records_query);
$total_records = $total_records_data['total'];
$total_pages = ceil($total_records / $limit);

// Agar url me page number total pages se jyada ho jaye
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
}

$offset = ($page - 1) * $limit;

// Current page ke hisaab se blogs fetch karna
$blogs_query = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT $offset, $limit"); //
?>

<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Latest <span>Insights</span></h2>
            <p class="text-muted">Stay updated with the latest security tips, company news, and industry trends.</p>
        </div>

        <div class="row g-4">
            <?php
            if ($blogs_query && mysqli_num_rows($blogs_query) > 0) {
                $delay = 100;
                while ($blog = mysqli_fetch_assoc($blogs_query)) {

                    // Image path handling
                    $raw_img = $blog['image']; //[cite: 1]
                    if (!empty($raw_img)) {
                        if (strpos($raw_img, 'admin/') === 0) {
                            $img_src = $raw_img;
                        } elseif (strpos($raw_img, 'uploads/') === 0) {
                            $img_src = 'admin/' . $raw_img;
                        } else {
                            $img_src = 'admin/assets/img/uploads/' . $raw_img;
                        }
                    } else {
                        $img_src = 'assets/images/blog/default.jpg'; // Fallback image
                    }

                    // Content snippet create karna
                    // htmlspecialchars_decode aur stripslashes se raw HTML handle hoga aur strip_tags tags remove karega
                    $clean_text = strip_tags(htmlspecialchars_decode(stripslashes($blog['content']))); //[cite: 1]
                    $short_desc = (strlen($clean_text) > 110) ? substr($clean_text, 0, 110) . '...' : $clean_text;

                    // Title aur Date formatting
                    $blog_title = htmlspecialchars($blog['title']); //[cite: 1]
                    $blog_date = date('M d, Y', strtotime($blog['created_at'])); //[cite: 1]
                    $author = !empty($blog['author']) ? htmlspecialchars($blog['author']) : 'Admin'; //[cite: 1]
            
                    // Blog detail page link
                    $blog_link = !empty($blog['slug_url']) ? 'blog-details.php?slug=' . urlencode($blog['slug_url']) : 'blog-details.php?id=' . $blog['id']; //[cite: 1]
                    ?>
                    <!-- Blog Card -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $delay; ?>">
                        <div class="blog-card border-0 h-100 d-flex flex-column shadow-sm">
                            <a href="<?= $blog_link; ?>" class="d-block overflow-hidden">
                                <img src="<?= $img_src; ?>" alt="<?= $blog_title; ?>" class="blog-image w-100"
                                    style="object-fit: cover; height: 250px;">
                            </a>
                            <div class="blog-content d-flex flex-column flex-grow-1 bg-white p-4">
                                <div class="blog-meta mb-3 text-muted small d-flex gap-3">
                                    <span><i class="fas fa-calendar-alt text-primary-custom"></i> <?= $blog_date; ?></span>
                                    <span><i class="fas fa-user text-primary-custom"></i> <?= $author; ?></span>
                                </div>
                                <h5 class="fw-bold mb-3">
                                    <a href="<?= $blog_link; ?>" class="text-dark text-decoration-none"><?= $blog_title; ?></a>
                                </h5>
                                <p class="text-muted small mb-4 flex-grow-1"><?= $short_desc; ?></p>
                                <div class="mt-auto">
                                    <a href="<?= $blog_link; ?>" class="text-primary-custom fw-bold text-decoration-none">
                                        Read More <i class="fas fa-arrow-right ms-1"></i>
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
                    <p class="text-muted">No blogs published yet. Check back later!</p>
                </div>
            <?php } ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="row mt-5" data-aos="fade-up">
                <div class="col-12">
                    <ul class="pagination pagination-custom justify-content-center">

                        <!-- Previous Button -->
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?= ($page <= 1) ? '#' : '?page=' . ($page - 1); ?>">
                                <i class="fas fa-angle-left"></i> Prev
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?= ($page >= $total_pages) ? '#' : '?page=' . ($page + 1); ?>">
                                Next <i class="fas fa-angle-right"></i>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>