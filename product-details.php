<?php
include('config/connect.php');

// Check karein ki URL mein slug hai ya id
if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $product_slug = mysqli_real_escape_string($conn, $_GET['slug']);
    // Slug ya id dono se match karne ka check taaki purane links bhi na tutein
    $productQuery = mysqli_query($conn, "SELECT * FROM products WHERE (slug_url = '$product_slug' OR id = '$product_slug') AND status = 1");
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $productQuery = mysqli_query($conn, "SELECT * FROM products WHERE id = '$product_id' AND status = 1");
} else {
    $productQuery = false;
}

$product = ($productQuery) ? mysqli_fetch_assoc($productQuery) : null;

// Agar product nahi mila, toh products page par redirect kar do
if (!$product) {
    echo "<script>window.location.href='products.php';</script>";
    exit;
}

// Global variable for product ID (agar related products ya gallery me aage use ho raha ho)
$product_id = $product['id'];

// Fetch Global Contact Info for Call Buttons
$contactQuery = mysqli_query($conn, "SELECT phone FROM contacts LIMIT 1");
$contactInfo = mysqli_fetch_assoc($contactQuery);
$sitePhone = !empty($contactInfo['phone']) ? $contactInfo['phone'] : '+91-8448211202';

// Dynamic Page Title
$pageTitle = $product['pro_name'];

include 'includes/header.php';
include 'includes/breadcrumb.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | Bhagirath Enterprise</title>
    <meta name="description" content="<?php echo htmlspecialchars($product['meta_desc']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($product['meta_key']); ?>">
    <link rel="icon" href="assets/images/logo/favicon.png" type="image/png">

    <script type="application/ld+json">
        {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "<?= htmlspecialchars($product['pro_name']); ?>",
        "image": [
            "<?= $site; ?>/admin/assets/img/uploads/<?= htmlspecialchars($product['pro_img']); ?>"
        ],
        "description": "<?= htmlspecialchars(strip_tags($product['meta_desc'])); ?>",
        "brand": {
            "@type": "Brand",
            "name": "<?= htmlspecialchars($product['brand_name'] ?? 'Bhagirath Enterprise'); ?>"
        },
        "offers": {
            "@type": "Offer",
            "url": "<?php echo $site; ?>/product-details.php?slug=<?= htmlspecialchars($product['slug_url'] ?? $product['id']); ?>",
            "priceCurrency": "INR",
            "price": "<?= htmlspecialchars($product['selling_price'] ?? '0.00'); ?>",
            "availability": "https://schema.org/InStock",
            "itemCondition": "https://schema.org/NewCondition"
        }
        }
    </script>
</head>
<body>
    

<section class="pd-section">
    <div class="container">

        <div class="row">
            <!-- Left Column: Image Gallery -->
            <div class="col-lg-5 mb-5 mb-lg-0 reveal py-5">
                <div class="pd-image-gallery">
                    <div class="pd-main-img">
                        <img id="mainImage" src="admin/assets/img/uploads/<?php echo $product['pro_img']; ?>" alt="<?php echo $product['pro_name']; ?>">
                    </div>

                    <div class="pd-thumbnails">
                        <!-- <div class="pd-thumb active" onclick="changeImage(this, 'uploads/<?php echo $product['pro_img']; ?>')">
                            <img src="uploads/<?php echo $product['pro_img']; ?>" alt="Thumb">
                        </div> -->

                        <?php
                        $galleryQuery = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id = '$product_id'");
                        while ($galleryImg = mysqli_fetch_assoc($galleryQuery)):
                        ?>
                            <div class="pd-thumb" onclick="changeImage(this, 'uploads/<?php echo $galleryImg['image_path']; ?>')">
                                <img src="uploads/<?php echo $galleryImg['image_path']; ?>" alt="Additional Thumb">
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Product Info -->
            <div class="col-lg-7 ps-lg-5 reveal py-5">
                <span class="pd-category"><?php echo $product['brand_name']; ?></span>
                <p style="font-size: 13px; color: #888;">
                    <i class="fa-solid fa-shield-check text-success"></i> 100% Secure & Verified Supplier
                </p>
                <!-- <h2 class="pd-title"><?php echo $product['pro_name']; ?></h2> -->

                <!-- Short Description from DB -->
                <div class="pd-overview">
                    <?php echo $product['short_desc']; ?>
                </div>

                <!-- Action Buttons (Dynamic Contact Link & Phone) -->
                <div class="pd-action-btns">
                    <a href="contact.php?product=<?php echo urlencode($product['pro_name']); ?>" class="btn-lg-quote">
                        Request a Quote <i class="fa-solid fa-file-invoice ms-2"></i>
                    </a>

                    <a href="tel:<?php echo $sitePhone; ?>" class="btn-lg-call">
                        <i class="fa-solid fa-phone me-2"></i> Call for Enquiry
                    </a>
                </div>

            </div>
        </div>

        <!-- Tabs Section for Deep Details -->
        <div class="row pd-tabs-section">
            <div class="col-12">

                <ul class="nav nav-tabs custom-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Full Description</button>
                    </li>
                </ul>

                <div class="tab-content" id="productTabsContent">
                    <!-- Long Description from DB -->
                    <div class="tab-pane fade show active" id="desc" role="tabpanel">
                        <?php echo $product['description']; ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- RELATED PRODUCTS SECTION -->
<section class="related-products" style="padding: 0 0 100px 0; background-color: #ffffff;">
    <div class="container">
        <!-- Section Title with Updated Brand Colors -->
        <div class="text-center mb-5 reveal">
            <h2 style="font-size: 2rem; font-weight: 800; color: #222222;">Explore Related Products</h2>
            <div style="width: 60px; height: 3px; background: #711b3c; margin: 15px auto;"></div>
        </div>

        <div class="row g-4 reveal">
            <?php
            $relatedQuery = mysqli_query($conn, "SELECT * FROM products WHERE status = 1 AND id != '$product_id' ORDER BY RAND() LIMIT 4");
            while ($related = mysqli_fetch_assoc($relatedQuery)):
                $shortDesc = !empty($related['short_desc']) ? $related['short_desc'] : (!empty($related['meta_desc']) && $related['meta_desc'] != $related['pro_name'] ? $related['meta_desc'] : 'Premium quality agricultural export product sourced directly from Indian farms.');
            ?>
                <div class="col-lg-3 col-md-6">
                    <div class="product-card h-100 d-flex flex-column" style="border: 1px solid #f0f0f0; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.03); background: #ffffff;">

                        <!-- Product Image -->
                        <a href="product-details.php?slug=<?php echo $related['slug_url']; ?>" style="text-decoration:none;">
                            <div style="height: 200px; overflow: hidden; background: #f8f9fa; padding: 10px;">
                                <img src="admin/assets/img/uploads/<?php echo $related['pro_img']; ?>" style="width: 100%; height: 100%; object-fit: contain;" alt="<?php echo htmlspecialchars($related['pro_name']); ?>" onerror="this.src='assets/images/black.png'">
                            </div>
                        </a>

                        <!-- Product Content -->
                        <div style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1;">
                            <!-- Product Title -->
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 8px;">
                                <a href="product-details.php?slug=<?php echo $related['slug_url']; ?>" style="color: #222222; text-decoration: none;">
                                    <?php echo htmlspecialchars($related['pro_name']); ?>
                                </a>
                            </h3>

                            <!-- Product Short Description (Limited to exactly 2 lines) -->
                            <p class="text-muted mb-4" style="font-size: 0.85rem; line-height: 1.5; display: -webkit-box; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 3em;">
                                <?php echo htmlspecialchars(strip_tags($shortDesc)); ?>
                            </p>

                            <!-- Action Buttons (Left: View Details, Right: Request Quote) -->
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f0f0; padding-top: 15px; margin-top: auto;">
                                <a href="product-details.php?slug=<?php echo $related['slug_url']; ?>" style="color: #711b3c; text-decoration: none; font-weight: 600; font-size: 13px;">
                                    View Details <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                <a href="contact.php?product=<?php echo urlencode($related['pro_name']); ?>" style="background-color: #222222; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; text-decoration: none;">
                                    Request Quote
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php include ('includes/inquiry-form.php'); ?>

<script>
    // JS for changing main image when thumbnail is clicked
    function changeImage(element, imageSrc) {
        document.getElementById('mainImage').src = imageSrc;

        let thumbs = document.querySelectorAll('.pd-thumb');
        thumbs.forEach(thumb => thumb.classList.remove('active'));

        element.classList.add('active');
    }

    // Scroll Animation
    document.addEventListener("DOMContentLoaded", function() {
        const reveals = document.querySelectorAll(".reveal");
        const revealOnScroll = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("active");
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        reveals.forEach(reveal => revealOnScroll.observe(reveal));
    });
</script>

<?php include 'includes/footer.php'; ?>