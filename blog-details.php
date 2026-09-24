<?php
include "admin/db-conn.php";
$pageTitle = "Blog Details";
include 'includes/header.php';
include 'includes/breadcrumb.php';

// 1. WhatsApp Number Fetch (Contacts Table se)
$contact_query = mysqli_query($conn, "SELECT wp_number FROM contacts LIMIT 1"); //
$contact_data = mysqli_fetch_assoc($contact_query);
$wp_number = !empty($contact_data['wp_number']) ? $contact_data['wp_number'] : '917200864976'; //

// 2. Fetch Blog Data based on ID or Slug
$blog = null;
if (isset($_GET['slug'])) {
    $slug = mysqli_real_escape_string($conn, $_GET['slug']);
    $query = mysqli_query($conn, "SELECT * FROM blogs WHERE slug_url = '$slug' AND status = 'published' LIMIT 1"); //
    $blog = mysqli_fetch_assoc($query);
} elseif (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM blogs WHERE id = $id AND status = 'published' LIMIT 1"); //[cite: 1]
    $blog = mysqli_fetch_assoc($query);
}

// Agar blog DB me nahi mila to list page par redirect kar do
if (!$blog) {
    echo "<script>window.location.href='blog.php';</script>";
    exit;
}

// 3. Blog Data Variables Setup
// Image path exactly wahi jo tumne manga hai
$raw_img = $blog['image']; //[cite: 1]
$img_src = !empty($raw_img) ? 'admin/assets/img/uploads/' . $raw_img : 'assets/images/blog/default.jpg';

$blog_title = htmlspecialchars($blog['title']); //[cite: 1]
$blog_author = !empty($blog['author']) ? htmlspecialchars($blog['author']) : 'Admin'; //[cite: 1]
$blog_date = date('F d, Y', strtotime($blog['created_at'])); //[cite: 1]

// HTML entities aur slashes ko decode karna (Rich Text styling ke liye)
$blog_content = htmlspecialchars_decode(stripslashes($blog['content'])); //[cite: 1]

// Current Page URL (Social Share buttons ke liye)
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$encoded_url = urlencode($current_url);
$encoded_title = urlencode($blog_title);
?>

<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="row">

            <!-- Main Content Area -->
            <div class="col-lg-8 mb-5 mb-lg-0" data-aos="fade-up">
                <img src="<?= $img_src; ?>" alt="<?= $blog_title; ?>" class="blog-details-img w-100 rounded mb-4"
                    style="object-fit: cover; max-height: 500px;">

                <div class="blog-meta mb-3 text-muted">
                    <span class="me-3"><i class="fas fa-user text-primary-custom"></i> By <?= $blog_author; ?></span>
                    <span class="me-3"><i class="fas fa-calendar-alt text-primary-custom"></i> <?= $blog_date; ?></span>
                    <!-- Database me category/comments nahi hai to inhe default rakha hai ya remove kar sakte ho -->
                    <span><i class="fas fa-folder text-primary-custom"></i> Updates</span>
                </div>

                <h2 class="fw-bold text-secondary mb-4"><?= $blog_title; ?></h2>

                <!-- Content Area -->
                <div class="post-content service-details-content">
                    <?= $blog_content; ?>
                </div>

                <!-- Share Tags -->
                <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top flex-wrap gap-3">
                    <div>
                        <span class="fw-bold text-dark me-2">Tags:</span>
                        <span class="badge bg-secondary">Security</span>
                        <span class="badge bg-secondary">Safety</span>
                        <span class="badge bg-secondary">Services</span>
                    </div>
                    <div>
                        <span class="fw-bold text-dark me-2">Share:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $encoded_url; ?>" target="_blank"
                            class="text-secondary fs-5 me-2 hover-primary"><i class="fab fa-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?= $encoded_url; ?>&text=<?= $encoded_title; ?>"
                            target="_blank" class="text-secondary fs-5 me-2 hover-primary"><i
                                class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $encoded_url; ?>&title=<?= $encoded_title; ?>"
                            target="_blank" class="text-secondary fs-5 hover-primary"><i
                                class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <!-- Sidebar Area -->
            <div class="col-lg-4" data-aos="fade-left">

                <!-- Search Widget -->
                <div class="sidebar-widget bg-white p-4 rounded shadow-sm mb-4">
                    <h4 class="sidebar-widget-title fw-bold mb-3">Search</h4>
                    <form action="blog.php" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search blog..."
                            required>
                        <button class="btn btn-primary-custom" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Recent Posts Widget -->
                <div class="sidebar-widget bg-white p-4 rounded shadow-sm mb-4">
                    <h4 class="sidebar-widget-title fw-bold mb-3">Recent Posts</h4>
                    <ul class="recent-post-list list-unstyled mb-0">
                        <?php
                        // Sidebar ke liye other recent posts fetch karna
                        $recent_query = mysqli_query($conn, "SELECT id, title, slug_url, image, created_at FROM blogs WHERE status = 'published' AND id != {$blog['id']} ORDER BY created_at DESC LIMIT 3"); //[cite: 1]
                        
                        if ($recent_query && mysqli_num_rows($recent_query) > 0) {
                            while ($recent = mysqli_fetch_assoc($recent_query)) {
                                $recent_img = !empty($recent['image']) ? 'admin/assets/img/uploads/' . $recent['image'] : 'assets/images/blog/default.jpg'; //[cite: 1]
                                $recent_title = htmlspecialchars($recent['title']); //[cite: 1]
                                $recent_date = date('M d, Y', strtotime($recent['created_at'])); //[cite: 1]
                                $recent_link = !empty($recent['slug_url']) ? 'blog-details.php?slug=' . urlencode($recent['slug_url']) : 'blog-details.php?id=' . $recent['id']; //[cite: 1]
                                ?>
                                <li class="d-flex mb-3 align-items-center">
                                    <img src="<?= $recent_img; ?>" alt="<?= $recent_title; ?>"
                                        class="recent-post-img rounded me-3"
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                    <div>
                                        <span class="small text-muted d-block mb-1"><i
                                                class="fas fa-calendar-alt text-primary-custom me-1"></i>
                                            <?= $recent_date; ?></span>
                                        <a href="<?= $recent_link; ?>"
                                            class="recent-post-title text-dark fw-bold text-decoration-none"
                                            style="font-size: 14px;"><?= $recent_title; ?></a>
                                    </div>
                                </li>
                                <?php
                            }
                        } else {
                            echo `"<li class="
                            text - muted
                            small
                            ">No other recent posts found.</li>"`;
                        }
                        ?>
                    </ul>
                </div>

                <!-- Contact Help Widget -->
                <div class="sidebar-widget help-widget bg-dark text-white p-4 rounded text-center shadow-sm">
                    <i class="fas fa-headset fs-1 text-primary-custom mb-3"></i>
                    <h4 class="fw-bold mb-3">Need Any Help?</h4>
                    <p class="text-white-50 mb-4">Contact our expert team to get a customized security plan.</p>
                    <a href="https://wa.me/<?= $wp_number; ?>?text=Hello, I am interested in your services after reading the blog."
                        target="_blank" class="btn btn-primary-custom w-100">
                        Chat on WhatsApp
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>