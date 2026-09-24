<?php
// Database connection file zaroor include karein
include ('config/connect.php'); 

$pageTitle = "Our Gallery";
include 'includes/header.php';
include 'includes/breadcrumb.php';

// ==========================================
// PAGINATION LOGIC
// ==========================================
$limit = 12; // Ek page par 12 photos dikhayenge (4x3 grid)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total Photos Count
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM gallery");
$totalRow = mysqli_fetch_assoc($totalQuery);
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

// Fetch Images for Current Page
$galleryQuery = mysqli_query($conn, "SELECT * FROM gallery ORDER BY ID DESC LIMIT $limit OFFSET $offset");
?>

<section class="gallery-page-section">
    <div class="container">

        <!-- Gallery Filters -->
        <div class="row reveal">
            <div class="col-12 text-center">
                <div class="gallery-filters">
                    <button class="filter-btn active">All Photos</button>
                    <button class="filter-btn">Farms & Sourcing</button>
                    <button class="filter-btn">Factory Processing</button>
                    <button class="filter-btn">Packaging</button>
                    <button class="filter-btn">Export Deliveries</button>
                </div>
            </div>
        </div>

        <!-- Dynamic Images Gallery Grid -->
        <div class="gallery-grid reveal">
            <?php
            if($total_records > 0):
                while ($item = mysqli_fetch_assoc($galleryQuery)):
                    // DB paths already have 'uploads/' prefix usually, but checking to be safe
                    $imagePath = $item['image_path'];
            ?>

                <div class="gallery-item" onclick="openLightbox('<?php echo $imagePath; ?>')">
                    <img src="admin/<?php echo $imagePath; ?>" alt="<?php echo $item['image_name']; ?>">
                    <!-- Hover Overlay -->
                    <div class="gallery-overlay">
                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                        <span><?php echo pathinfo($item['image_name'], PATHINFO_FILENAME); // Removing extension for clean title ?></span>
                    </div>
                </div>

            <?php 
                endwhile;
            else:
                echo "<div class='col-12 text-center py-5'><h3 style='color: var(--text-muted);'>No Images Found in Gallery</h3></div>";
            endif; 
            ?>
        </div>

        <!-- Dynamic Pagination Section -->
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

<!-- Lightbox Modal Container -->
<div class="k2k-lightbox" id="lightbox">
    <div class="lightbox-close" onclick="closeLightbox()">&times;</div>
    <img id="lightbox-img" src="" alt="Enlarged Gallery Image">
</div>

<!-- ==============================
     JAVASCRIPT FOR LIGHTBOX & SCROLL
     ============================== -->
<script>
    // 1. LIGHTBOX LOGIC
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');

    function openLightbox(imageSrc) {
        lightboxImg.src = imageSrc;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden'; 
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = 'auto'; 
        setTimeout(() => {
            lightboxImg.src = '';
        }, 300); 
    }

    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && lightbox.classList.contains('active')) {
            closeLightbox();
        }
    });

    // 2. SCROLL ANIMATION
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