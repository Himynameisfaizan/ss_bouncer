<?php
require_once 'config/connect.php'; 

// Pagination logic
$limit = 6; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total records
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM blogs WHERE status = 1");
$totalRow = $totalQuery->fetch_assoc();
$total_blogs = $totalRow['total'];
$total_pages = ceil($total_blogs / $limit);

// Fetch grid posts
$gridQuery = $conn->query("SELECT * FROM blogs WHERE status = 1 ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

// SEO Meta Data
$currentPage = basename($_SERVER['PHP_SELF']);
$seo_meta_query = $conn->query("SELECT meta_title, meta_key, meta_desc FROM meta WHERE page_url = '$currentPage'");
$seo_data = ($seo_meta_query && $seo_meta_query->num_rows > 0) ? $seo_meta_query->fetch_assoc() : null;

$pageTitle = $seo_data['meta_title'] ?? "Our Blog | SS Bouncers";
$meta_keywords = $seo_data['meta_key'] ?? "security blog, bouncer tips, safety news";
$meta_description = $seo_data['meta_desc'] ?? "Stay updated with the latest security insights, safety tips, and news from SS Bouncers.";

include 'includes/header.php'; 
include 'includes/breadcrumb.php'; 
?>

<!-- ==================== BLOG GRID SECTION ==================== -->
<section class="blog-page-section py-5 bg-light-custom">
    <div class="container py-5">

        <div class="row justify-content-center text-center mb-5 reveal">
            <div class="col-lg-8">
                <span class="sub-heading text-secondary-accent fw-bold text-uppercase tracking-wider">Latest News</span>
                <h2 class="main-heading text-primary-dark fw-bold mt-2">Read Our Articles</h2>
                <p class="text-muted mt-3">Expert advice, industry updates, and safety protocols from our security professionals.</p>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <?php 
            if($gridQuery && $gridQuery->num_rows > 0):
                while($blog = $gridQuery->fetch_assoc()): 
                    $date = date('d M, Y', strtotime($blog['created_at']));
                    $excerpt = mb_substr(strip_tags($blog['description']), 0, 100) . '...';
                    $img = !empty($blog['image']) ? $site . 'admin/assets/img/uploads/blogs/' . $blog['image'] : 'assets/images/default-blog.jpg';
            ?>
            <div class="col-lg-4 col-md-6 reveal">
                <div class="blog-card bg-white rounded-4 shadow-sm border-0 h-100 d-flex flex-column overflow-hidden position-relative">
                    
                    <div class="blog-img-wrapper position-relative" style="height: 220px; overflow: hidden;">
                        <!-- Category Badge -->
                        <span class="badge bg-secondary-accent text-primary-dark position-absolute top-0 start-0 m-3 p-2 px-3 fw-bold rounded-pill z-2 shadow-sm">Insights</span>
                        
                        <a href="blog-details.php?slug=<?php echo htmlspecialchars($blog['slug']); ?>" class="d-block h-100">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="w-100 h-100 object-fit-cover blog-card-img">
                        </a>
                    </div>
                    
                    <div class="blog-content p-4 d-flex flex-column flex-grow-1">
                        <!-- Meta Info -->
                        <div class="blog-meta d-flex align-items-center text-muted mb-3" style="font-size: 0.85rem;">
                            <span class="me-3"><i class="fa-solid fa-calendar-days text-secondary-accent me-1"></i> <?php echo $date; ?></span>
                            <span><i class="fa-solid fa-user text-secondary-accent me-1"></i> <?php echo htmlspecialchars($blog['author']); ?></span>
                        </div>
                        
                        <h4 class="fw-bold mb-3">
                            <a href="blog-details.php?slug=<?php echo htmlspecialchars($blog['slug']); ?>" class="text-decoration-none text-primary-dark blog-title-hover">
                                <?php echo htmlspecialchars($blog['title']); ?>
                            </a>
                        </h4>
                        
                        <p class="text-muted mb-4 flex-grow-1" style="line-height: 1.6;">
                            <?php echo $excerpt; ?>
                        </p>
                        
                        <a href="blog-details.php?slug=<?php echo htmlspecialchars($blog['slug']); ?>" class="read-more-link fw-bold text-primary-dark text-uppercase mt-auto">
                            Read More <i class="fa-solid fa-arrow-right ms-1 transition-icon"></i>
                        </a>
                    </div>
                    
                </div>
            </div>
            <?php 
                endwhile; 
            else:
                echo "<div class='col-12 text-center py-5'>
                        <i class='fa-regular fa-newspaper display-1 text-muted opacity-25 mb-3'></i>
                        <h3 class='text-primary-dark'>No Articles Found</h3>
                      </div>";
            endif;
            ?>
        </div>

        <!-- ==================== PAGINATION ==================== -->
        <?php if($total_pages > 1): ?>
        <div class="row reveal mt-5 pt-4 border-top">
            <div class="col-12">
                <nav aria-label="Blog Pagination">
                    <ul class="pagination justify-content-center custom-pagination mb-0">
                        <!-- Prev -->
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link px-4" href="?page=<?php echo ($page-1); ?>"><i class="fa-solid fa-arrow-left me-2"></i> Prev</a>
                        </li>
                        <!-- Numbers -->
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <!-- Next -->
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link px-4" href="?page=<?php echo ($page+1); ?>">Next <i class="fa-solid fa-arrow-right ms-2"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- Scroll Animation Script -->
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