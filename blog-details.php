<?php
// Database connection include karein
include('config/connect.php');

// URL se slug fetch karein
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';

// Database se specific blog fetch karein
$blogQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE slug = '$slug' AND status = 1");
$blog = mysqli_fetch_assoc($blogQuery);

// Agar blog nahi milta (invalid slug) toh wapas blog page par bhej dein
if (!$blog) {
    echo "<script>window.location.href='blog.php';</script>";
    exit;
}

// Dynamic Variables Setup
$pageTitle = $blog['title'];
$publishDate = date('F d, Y', strtotime($blog['created_at']));
$authorName = !empty($blog['author']) ? $blog['author'] : 'Admin Team';
$mainImage = !empty($blog['image']) ? 'admin/assets/img/uploads/blogs/' . $blog['image'] : 'https://images.unsplash.com/photo-1606914501449-5a96b6ce24ca?q=80&w=1200';

// Current Page URL for Social Sharing
$currentURL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= htmlspecialchars($blog['meta_title']); ?></title>
    <meta name="description" content="<?= htmlspecialchars(strip_tags($blog['meta_desc'])); ?>">
    <meta name="keywords" content="<?= htmlspecialchars($blog['meta_key']); ?>">
    
    <link rel="icon" href="<?= htmlspecialchars($favicon); ?>" type="image/x-icon">
    <!-- Blog Posting Schema Markup (JSON-LD) -->
    <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "<?= htmlspecialchars($blog['title']); ?>",
        "image": "<?= $site; ?>/admin/assets/img/uploads/blogs/<?= htmlspecialchars($blog['image']); ?>",
        "author": {
            "@type": "Person",
            "name": "<?= htmlspecialchars($blog['author'] ?? 'Admin'); ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "Bhagirath Enterprise",
            "logo": {
            "@type": "ImageObject",
            "url": "<?= $site; ?>/assets/images/logo/logo.png"
            }
        },
        "datePublished": "<?= htmlspecialchars($blog['created_at']); ?>",
        "description": "<?= htmlspecialchars(strip_tags(substr($blog['description'], 0, 150))); ?>"
        }
    </script>
</head>
<body>
    
<section class="single-blog-section">
    <div class="container">
        <div class="row">

            <!-- Main Content Area -->
            <div class="col-lg-8 pe-lg-5">
                <div class="blog-details-content">

                    <img src="<?php echo $mainImage; ?>" alt="<?php echo $blog['title']; ?>">

                    <div class="blog-meta-top">
                        <span><i class="fa-regular fa-calendar-days"></i> <?php echo $publishDate; ?></span>
                        <span><i class="fa-regular fa-user"></i> By <?php echo $authorName; ?></span>
                        <span><i class="fa-regular fa-folder-open"></i> News & Insights</span>
                    </div>

                    <h1><?php echo $blog['title']; ?></h1>

                    <div class="blog-description py-4">
                        <?php echo $blog['description']; ?>
                    </div>

                    <!-- Share Options -->
                    <div class="share-box">
                        <span>Share this article:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentURL); ?>" target="_blank" class="share-btn bg-fb"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($currentURL); ?>&text=<?php echo urlencode($blog['title']); ?>" target="_blank" class="share-btn bg-tw"><i class="fa-brands fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($currentURL); ?>" target="_blank" class="share-btn bg-in"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($blog['title'] . " " . $currentURL); ?>" target="_blank" class="share-btn bg-wa"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>

                </div>
            </div>

            <!-- Sidebar Area -->
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="blog-sidebar">

                    <!-- Search Widget -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Search</h4>
                        <form class="sidebar-search" action="blog.php" method="GET">
                            <input type="text" name="search" placeholder="Search insights...">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <!-- Categories Widget -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Categories</h4>
                        <ul class="sidebar-cats">
                            <li><a href="blog.php">Export Trends <span>(12)</span></a></li>
                            <li><a href="blog.php">Farming Practices <span>(08)</span></a></li>
                            <li><a href="blog.php">Health Benefits <span>(15)</span></a></li>
                            <li><a href="blog.php">Quality & Testing <span>(05)</span></a></li>
                            <li><a href="blog.php">Company News <span>(03)</span></a></li>
                        </ul>
                    </div>

                    <!-- Dynamic Recent Posts Widget -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Recent Posts</h4>

                        <?php
                        // Fetch 3 Recent Blogs excluding the current one
                        $recentQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 1 AND blog_id != '{$blog['blog_id']}' ORDER BY created_at DESC LIMIT 3");

                        if (mysqli_num_rows($recentQuery) > 0) {
                            while ($recentBlog = mysqli_fetch_assoc($recentQuery)):
                                $r_date = date('M d, Y', strtotime($recentBlog['created_at']));
                                $r_img = !empty($recentBlog['image']) ? 'admin/assets/img/uploads/blogs/' . $recentBlog['image'] : 'https://images.unsplash.com/photo-1615486171448-4228965f7c32?q=80&w=200';
                        ?>
                                <div class="recent-post-item">
                                    <img src="<?php echo $r_img; ?>" alt="<?php echo $recentBlog['title']; ?>">
                                    <div class="recent-post-info">
                                        <h4><a href="blog-details.php?slug=<?php echo $recentBlog['slug']; ?>"><?php echo $recentBlog['title']; ?></a></h4>
                                        <span><?php echo $r_date; ?></span>
                                    </div>
                                </div>
                        <?php
                            endwhile;
                        } else {
                            echo "<p style='color: #666; font-size: 13px;'>No recent posts available.</p>";
                        }
                        ?>
                    </div>

                    <!-- CTA Widget -->
                    <div class="sidebar-widget text-center" style="background: var(--primary-green); color: white;">
                        <i class="fa-solid fa-box-open" style="font-size: 40px; color: var(--accent-orange); margin-bottom: 15px;"></i>
                        <h4 style="font-weight: 800; margin-bottom: 15px;">Looking for Bulk Spices?</h4>
                        <p style="font-size: 0.95rem; opacity: 0.9; margin-bottom: 20px;">Get a free quotation for your international export requirements today.</p>
                        <a href="contact.php" class="btn-theme" style="background: var(--accent-orange); color: white; padding: 10px 20px; border-radius: 30px; text-decoration: none; font-weight: 700; display: inline-block;">Request Quote</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Premium Inquiry Section -->
<?php include ('includes/inquiry-form.php'); ?>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>