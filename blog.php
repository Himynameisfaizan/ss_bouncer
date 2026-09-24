<?php
include ('config/connect.php'); 

$pageTitle = "Blogs"; 
include 'includes/header.php';
include 'includes/breadcrumb.php';

$limit = 6; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM blogs WHERE status = 1");
$totalRow = mysqli_fetch_assoc($totalQuery);
$total_blogs = $totalRow['total'];

$grid_total_records = max(0, $total_blogs - 1);
$total_pages = ceil($grid_total_records / $limit);

$featuredQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 1 ORDER BY created_at DESC LIMIT 1");
$featuredBlog = mysqli_fetch_assoc($featuredQuery);

$gridQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 1 ORDER BY created_at DESC");

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
</head>
<body>
    
<section class="blog-page-section">
    <div class="container">
<!--         
        <?#php if($featuredBlog): 
            $f_date = date('M d, Y', strtotime($featuredBlog['created_at']));
            $f_excerpt = mb_substr(strip_tags($featuredBlog['description']), 0, 180) . '...';
            // Checking if image exists, else fallback
            $f_img = !empty($featuredBlog['image']) ? 'admin/assets/img/uploads/blogs/' . $featuredBlog['image'] : 'https://images.unsplash.com/photo-1606914501449-5a96b6ce24ca?q=80&w=1200';
        ?>
        <div class="row reveal">
            <div class="col-12">
                <div class="featured-blog">
                    <div class="featured-img-wrapper">
                        <span class="featured-category">Featured</span>
                        <a href="blog-details.php?slug=<?php echo $featuredBlog['slug']; ?>">
                            <img src="<?php echo $f_img; ?>" alt="<?php echo $featuredBlog['title']; ?>">
                        </a>
                    </div>
                    <div class="featured-content">
                        <div class="featured-meta">
                            <i class="fa-regular fa-calendar-days"></i> <?php echo $f_date; ?>
                            <i class="fa-regular fa-user"></i> <?php echo $featuredBlog['author']; ?>
                        </div>
                        <a href="blog-details.php?slug=<?php echo $featuredBlog['slug']; ?>" class="featured-title">
                            <?php echo $featuredBlog['title']; ?>
                        </a>
                        <p class="featured-excerpt">
                            <?php echo $f_excerpt; ?>
                        </p>
                        <a href="blog-details.php?slug=<?php echo $featuredBlog['slug']; ?>" class="btn-theme" style="background: var(--primary-green); color: white; padding: 12px 30px; border-radius: 30px; text-decoration: none; font-weight: 600; align-self: flex-start; transition: all 0.3s;" onmouseover="this.style.background='var(--accent-orange)'" onmouseout="this.style.background='var(--primary-green)'">Read Full Article <i class="fa-solid fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <#?php endif; ?> -->

        <!-- BLOG GRID -->
        <div class="row g-4 mt-2">
            <?php 
            if(mysqli_num_rows($gridQuery) > 0):
                while($blog = mysqli_fetch_assoc($gridQuery)): 
                    $date = date('M d, Y', strtotime($blog['created_at']));
                    $excerpt = mb_substr(strip_tags($blog['description']), 0, 100) . '...';
                    $img = !empty($blog['image']) ? 'admin/assets/img/uploads/blogs/' . $blog['image'] : 'https://images.unsplash.com/photo-1615486171448-4228965f7c32?q=80&w=800';
            ?>
            <div class="col-lg-4 col-md-6 reveal">
                <div class="blog-card">
                    <div class="blog-img-wrapper">
                        <span class="featured-category" style="top: 15px; left: 15px; font-size: 10px; padding: 4px 12px;">News</span>
                        <a href="blog-details.php?slug=<?php echo $blog['slug']; ?>">
                            <img src="<?php echo $img; ?>" alt="<?php echo $blog['title']; ?>">
                        </a>
                    </div>
                    <div class="blog-content">
                        <div class="featured-meta" style="font-size: 13px;">
                            <i class="fa-regular fa-calendar-days"></i> <?php echo $date; ?>
                            <i class="fa-regular fa-user"></i> <?php echo $blog['author']; ?>
                        </div>
                        <a href="blog-details.php?slug=<?php echo $blog['slug']; ?>" class="blog-title"><?php echo $blog['title']; ?></a>
                        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;"><?php echo $excerpt; ?></p>
                        <a href="blog-details.php?slug=<?php echo $blog['slug']; ?>" class="read-more-btn">Read More <i class="fa-solid fa-arrow-right-long"></i></a>
                    </div>
                </div>
            </div>
            <?php 
                endwhile; 
            else:
                // Show this if no other blogs exist
                if(!$featuredBlog) {
                    echo "<div class='col-12 text-center py-5'><h3 style='color: var(--text-muted);'>No Articles Found</h3></div>";
                }
            endif;
            ?>
        </div>

        <!-- DYNAMIC PAGINATION -->
        <?php if($total_pages > 1): ?>
        <div class="row reveal mt-5">
            <div class="col-12">
                <ul class="k2k-pagination">
                    <!-- Prev Button -->
                    <?php if($page > 1): ?>
                        <li class="prev"><a href="?page=<?php echo ($page-1); ?>"><i class="fa-solid fa-arrow-left me-2"></i> Prev</a></li>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next Button -->
                    <?php if($page < $total_pages): ?>
                        <li class="next"><a href="?page=<?php echo ($page+1); ?>">Next <i class="fa-solid fa-arrow-right ms-2"></i></a></li>
                    <?php endif; ?>
                </ul>
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
        }, { threshold: 0.1 });
        reveals.forEach(reveal => revealOnScroll.observe(reveal));
    });
</script>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>