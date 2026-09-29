<?php
require_once 'config/connect.php';

// URL se slug fetch karein
$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';

// Fetch specific blog using DB column blog_id[cite: 11]
$blogQuery = $conn->query("SELECT * FROM blogs WHERE slug = '$slug' AND status = 1");
$blog = $blogQuery ? $blogQuery->fetch_assoc() : null;

// Fallback to ID if no slug match (for robustness)
if (!$blog && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $blogQuery = $conn->query("SELECT * FROM blogs WHERE blog_id = $id AND status = 1");
    $blog = $blogQuery ? $blogQuery->fetch_assoc() : null;
}

if (!$blog) {
    echo "<script>window.location.href='blog.php';</script>";
    exit;
}

// Variables Setup
$publishDate = date('F d, Y', strtotime($blog['created_at']));
$authorName = !empty($blog['author']) ? $blog['author'] : 'SS Bouncers Team';
$mainImage = !empty($blog['image']) ? $site . 'admin/assets/img/uploads/blogs/' . $blog['image'] : 'assets/images/default-blog.jpg';
$currentURL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// SEO
$pageTitle = !empty($blog['meta_title']) ? $blog['meta_title'] : $blog['title'] . " | SS Bouncers";
$meta_description = !empty($blog['meta_desc']) ? $blog['meta_desc'] : mb_strimwidth(strip_tags($blog['description']), 0, 150, "...");
$meta_keywords = !empty($blog['meta_key']) ? $blog['meta_key'] : "security blog, bouncers, safety tips";

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<!-- ==================== BLOG DETAILS ==================== -->
<section class="single-blog-section py-5 bg-white">
    <div class="container py-4">
        <div class="row g-5">

            <!-- Main Content Area -->
            <div class="col-lg-8">
                <div class="blog-details-content bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light">
                    
                    <img src="<?php echo htmlspecialchars($mainImage); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="w-100 rounded-4 shadow-sm mb-4 object-fit-cover" style="max-height: 450px;">

                    <div class="blog-meta-top d-flex flex-wrap gap-4 pb-3 border-bottom mb-4">
                        <span class="text-muted"><i class="fa-solid fa-calendar-days text-secondary-accent me-2"></i> <?php echo $publishDate; ?></span>
                        <span class="text-muted"><i class="fa-solid fa-user text-secondary-accent me-2"></i> By <?php echo htmlspecialchars($authorName); ?></span>
                    </div>

                    <div class="blog-description-body text-muted" style="line-height: 1.8; font-size: 1.05rem;">
                        <?php echo $blog['description']; ?>
                    </div>

                    <!-- Share Options -->
                    <div class="share-box d-flex align-items-center gap-3 py-4 mt-5 border-top border-bottom">
                        <span class="fw-bold text-primary-dark">Share Article:</span>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentURL); ?>" target="_blank" class="share-icon bg-primary-dark text-white rounded-circle d-flex justify-content-center align-items-center shadow-sm"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($currentURL); ?>&text=<?php echo urlencode($blog['title']); ?>" target="_blank" class="share-icon bg-primary-dark text-white rounded-circle d-flex justify-content-center align-items-center shadow-sm"><i class="fa-brands fa-x-twitter"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($currentURL); ?>" target="_blank" class="share-icon bg-primary-dark text-white rounded-circle d-flex justify-content-center align-items-center shadow-sm"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($blog['title'] . " " . $currentURL); ?>" target="_blank" class="share-icon bg-success text-white rounded-circle d-flex justify-content-center align-items-center shadow-sm"><i class="fa-brands fa-whatsapp"></i></a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Sidebar Area -->
            <div class="col-lg-4">
                <div class="sidebar-wrapper sticky-top" style="top: 100px; z-index: 10;">

                    <!-- Search Widget -->
                    <div class="sidebar-widget bg-white p-4 rounded-4 shadow-sm border border-light mb-4">
                        <h4 class="text-primary-dark fw-bold mb-3 pb-2 border-bottom">Search</h4>
                        <form class="sidebar-search position-relative" action="blog.php" method="GET">
                            <input type="text" name="search" class="form-control custom-input pe-5" placeholder="Search insights..." required>
                            <button type="submit" class="position-absolute top-50 end-0 translate-middle-y bg-transparent border-0 text-secondary-accent me-3"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <!-- Dynamic Recent Posts Widget -->
                    <div class="sidebar-widget bg-white p-4 rounded-4 shadow-sm border border-light mb-4">
                        <h4 class="text-primary-dark fw-bold mb-4 pb-2 border-bottom">Recent Posts</h4>

                        <?php
                        $recentQuery = $conn->query("SELECT * FROM blogs WHERE status = 1 AND blog_id != '{$blog['blog_id']}' ORDER BY created_at DESC LIMIT 3");

                        if ($recentQuery && $recentQuery->num_rows > 0) {
                            while ($recentBlog = $recentQuery->fetch_assoc()):
                                $r_date = date('M d, Y', strtotime($recentBlog['created_at']));
                                $r_img = !empty($recentBlog['image']) ? $site . 'admin/assets/img/uploads/blogs/' . $recentBlog['image'] : 'assets/images/default-blog.jpg';
                        ?>
                                <div class="recent-post-item d-flex gap-3 mb-3 pb-3 border-bottom">
                                    <img src="<?php echo htmlspecialchars($r_img); ?>" alt="Thumb" class="rounded-3 object-fit-cover shadow-sm" style="width: 80px; height: 80px;">
                                    <div class="recent-post-info d-flex flex-column justify-content-center">
                                        <h6 class="fw-bold mb-1 lh-sm">
                                            <a href="blog-details.php?slug=<?php echo htmlspecialchars($recentBlog['slug']); ?>" class="text-decoration-none text-primary-dark blog-title-hover">
                                                <?php echo htmlspecialchars(mb_strimwidth($recentBlog['title'], 0, 50, "...")); ?>
                                            </a>
                                        </h6>
                                        <span class="text-muted small"><i class="fa-solid fa-calendar-days text-secondary-accent me-1"></i> <?php echo $r_date; ?></span>
                                    </div>
                                </div>
                        <?php
                            endwhile;
                        } else {
                            echo "<p class='text-muted small'>No recent posts available.</p>";
                        }
                        ?>
                    </div>

                    <!-- CTA Widget -->
                    <div class="sidebar-widget p-4 rounded-4 shadow-sm text-center position-relative overflow-hidden" style="background-color: var(--primary-color);">
                        <div class="position-absolute top-0 end-0 m-2 opacity-25">
                            <i class="fa-solid fa-shield-halved display-1"></i>
                        </div>
                        <div class="position-relative z-2">
                            <div class="bg-white rounded-circle d-inline-flex justify-content-center align-items-center mb-3 shadow" style="width: 70px; height: 70px;">
                                <i class="fa-solid fa-headset fs-2 text-secondary-accent"></i>
                            </div>
                            <h4 class="text-white fw-bold mb-3">Need Security Services?</h4>
                            <p class="text-white-50 small mb-4">Get a free quotation for your corporate or residential security requirements today.</p>
                            <a href="contact.php" class="btn btn-premium w-100">Request Quote <i class="fa-solid fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/inquiry-form.php'; ?>
<?php include 'includes/footer.php'; ?>