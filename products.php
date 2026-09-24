<?php
include 'config/connect.php';
$pageTitle = "Our Products | Bhagirath Enterprise";

// Fetch Global Contact Info for Call Buttons
$contactQuery = mysqli_query($conn, "SELECT phone FROM contacts LIMIT 1");
$contactInfo = mysqli_fetch_assoc($contactQuery);
$sitePhone = !empty($contactInfo['phone']) ? $contactInfo['phone'] : '+91-8448211202';

$whereClause = "WHERE status = 1";
$urlParams = [];

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $cat_input = mysqli_real_escape_string($conn, $_GET['category']);

    $catCheckQ = mysqli_query($conn, "SELECT cate_id FROM categories WHERE slug_url = '$cat_input' OR cate_id = '$cat_input' OR categories = '$cat_input' LIMIT 1");
    if ($catCheckQ && mysqli_num_rows($catCheckQ) > 0) {
        $catData = mysqli_fetch_assoc($catCheckQ);
        $cat_id = $catData['cate_id'];
        $whereClause .= " AND pro_cate = '$cat_id'";
    } else {
        $whereClause .= " AND pro_cate = '$cat_input'";
    }

    $urlParams[] = "category=" . urlencode($cat_input);
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $whereClause .= " AND pro_name LIKE '%$search%'";
    $urlParams[] = "search=" . urlencode($search);
}

$orderBy = "ORDER BY id DESC"; // Default
if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
    $urlParams[] = "sort=$sort";
    if ($sort == 'name_asc') {
        $orderBy = "ORDER BY pro_name ASC";
    } elseif ($sort == 'name_desc') {
        $orderBy = "ORDER BY pro_name DESC";
    } elseif ($sort == 'oldest') {
        $orderBy = "ORDER BY id ASC";
    }
}

$limit = 9;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM products $whereClause");
$totalRow = mysqli_fetch_assoc($totalQuery);
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

$queryString = !empty($urlParams) ? "&" . implode("&", $urlParams) : "";

$productsQuery = mysqli_query($conn, "SELECT * FROM products $whereClause $orderBy LIMIT $limit OFFSET $offset");

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


include 'includes/header.php';
include 'includes/breadcrumb.php';
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

<section class="products-page-section">
    <div class="products-page-container">
        <div class="row">

            <!-- LEFT SIDEBAR -->
            <div class="col-lg-3 mb-5 mb-lg-0 reveal">
                <div class="catalog-sidebar">

                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Search Products</h4>
                        <form class="sidebar-search" action="products.php" method="GET">
                            <?php if (isset($_GET['category'])): ?>
                                <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">
                            <?php endif; ?>
                            <input type="text" name="search" placeholder="Type product name..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">All Categories</h4>
                        <ul class="cat-list">
                            <?php
                            $catQuery = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1");
                            while ($cat = mysqli_fetch_assoc($catQuery)):
                                $countQ = mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE pro_cate='" . $cat['cate_id'] . "' AND status=1");
                                $pCount = mysqli_fetch_assoc($countQ)['c'];
                                $isActive = (isset($_GET['category']) && $_GET['category'] == $cat['slug_url']) ? 'active' : '';
                            ?>
                                <li>
                                    <a href="products.php?category=<?php echo $cat['slug_url']; ?>" class="<?php echo $isActive; ?>">
                                        <?php echo $cat['categories']; ?> <span><?php echo $pCount; ?></span>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <!-- Sidebar Support Banner -->
                    <div class="sidebar-widget text-center support-banner">
                        <i class="fa-solid fa-headset banner-icon"></i>
                        <h5>Need Help?</h5>
                        <p>Contact our export team for bulk orders.</p>
                        <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $sitePhone); ?>"><?php echo $sitePhone; ?></a>
                    </div>
                </div>
            </div>

            <!-- RIGHT MAIN CONTENT -->
            <div class="col-lg-9">
                <div class="filter-bar reveal">
                    <div class="filter-result-count">
                        <?php
                        $startItem = ($total_records > 0) ? $offset + 1 : 0;
                        $endItem = min($offset + $limit, $total_records);
                        ?>
                        Showing <span><?php echo $startItem; ?>–<?php echo $endItem; ?></span> of <?php echo $total_records; ?> results
                    </div>

                    <div class="sort-box">
                        <label for="sortBy">Sort by:</label>
                        <select id="sortBy" onchange="window.location.href=this.value;">
                            <?php
                            $sortBaseUrl = "products.php?";
                            $sortParams = [];
                            if (isset($_GET['category'])) $sortParams[] = "category=" . $_GET['category'];
                            if (isset($_GET['search'])) $sortParams[] = "search=" . $_GET['search'];
                            if (!empty($sortParams)) $sortBaseUrl .= implode("&", $sortParams) . "&";
                            ?>
                            <option value="<?php echo $sortBaseUrl; ?>sort=latest" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'latest') ? 'selected' : ''; ?>>Latest Products</option>
                            <option value="<?php echo $sortBaseUrl; ?>sort=oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest Products</option>
                            <option value="<?php echo $sortBaseUrl; ?>sort=name_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>Name: A to Z</option>
                            <option value="<?php echo $sortBaseUrl; ?>sort=name_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : ''; ?>>Name: Z to A</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid (UPDATED DESIGN) -->
                <div class="row g-4">
                    <?php
                    if ($total_records > 0) {
                        while ($product = mysqli_fetch_assoc($productsQuery)):

                            $shortDesc = !empty($product['short_desc']) ? $product['short_desc'] : (!empty($product['meta_desc']) && $product['meta_desc'] != $product['pro_name'] ? $product['meta_desc'] : 'Premium quality agricultural export product sourced from India.');
                    ?>
                            <div class="col-lg-4 col-md-6 col-12 reveal">
                                <div class="product-card h-100 shadow-sm border rounded overflow-hidden d-flex flex-column" style="background: #ffffff; transition: all 0.3s ease;">
                                    <a href="product-details.php?slug=<?php echo $product['slug_url']; ?>" class="product-img-link" style="text-decoration:none;">
                                        <div class="product-img-wrapper" style="height: 220px; overflow: hidden; background: #f8f9fa;">
                                            <img src="admin/assets/img/uploads/<?php echo $product['pro_img']; ?>" alt="<?php echo $product['pro_name']; ?>" style="width: 100%; height: 100%; object-fit: contain; padding: 10px; transition: transform 0.5s ease;" onerror="this.src='assets/images/black.png'">
                                        </div>
                                    </a>

                                    <div class="product-content p-4 d-flex flex-column flex-grow-1">
                                        <a href="product-details.php?slug=<?php echo $product['slug_url']; ?>" style="text-decoration: none;">
                                            <h3 class="product-title" style="font-size: 1.15rem; font-weight: 700; color: #222222; margin-bottom: 8px;">
                                                <?php echo htmlspecialchars($product['pro_name']); ?>
                                            </h3>
                                        </a>

                                        <!-- Dynamic Short Description (2 Lines Limit) -->
                                        <p class="product-short-desc text-muted mb-4" style="font-size: 0.9rem; line-height: 1.5; display: -webkit-box; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.8em;">
                                            <?php echo htmlspecialchars(strip_tags($shortDesc)); ?>
                                        </p>

                                        <!-- Action Buttons (Left: View Details, Right: Quote) -->
                                        <div class="product-actions mt-auto d-flex justify-content-between align-items-center border-top pt-3">
                                            <a href="product-details.php?slug=<?php echo $product['slug_url']; ?>" class="view-details-link small fw-bold" style="color: #711b3c; text-decoration: none; transition: 0.3s;">
                                                View Details <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                            <a href="contact.php?product=<?php echo urlencode($product['pro_name']); ?>" class="btn-quote-full small px-3 py-2 rounded" style="background-color: #222222; color: white; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                                                Request Quote
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        endwhile;
                    } else {
                        echo "
                        <div class='col-12 text-center py-5'>
                            <i class='fa-solid fa-box-open' style='font-size: 50px; color: #ccc; margin-bottom: 15px;'></i>
                            <h3 style='color: #222222;'>No Products Found</h3>
                            <p style='color: #666;'>Try selecting a different category or search term.</p>
                            <a href='products.php' class='btn-quote-full mt-3 px-4 py-2 rounded' style='background: #711b3c; color: white; display:inline-block; text-decoration:none;'>Clear All Filters</a>
                        </div>";
                    }
                    ?>
                </div>

                <!-- Dynamic Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="row reveal mt-5">
                        <div class="col-12">
                            <ul class="k2k-pagination">
                                <?php if ($page > 1): ?>
                                    <li class="prev"><a href="?page=<?php echo ($page - 1) . $queryString; ?>"><i class="fa-solid fa-arrow-left me-2"></i> Prev</a></li>
                                <?php endif; ?>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a href="?page=<?php echo $i . $queryString; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($page < $total_pages): ?>
                                    <li class="next"><a href="?page=<?php echo ($page + 1) . $queryString; ?>">Next <i class="fa-solid fa-arrow-right ms-2"></i></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

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
        }, {
            threshold: 0.1
        });

        reveals.forEach(reveal => revealOnScroll.observe(reveal));
    });
</script>

<?php include 'includes/footer.php'; ?>