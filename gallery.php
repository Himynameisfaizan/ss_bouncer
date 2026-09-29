<?php
require_once 'config/connect.php'; 

$currentPage = basename($_SERVER['PHP_SELF']);
$seo_meta_query = $conn->query("SELECT meta_title, meta_key, meta_desc FROM meta WHERE page_url = '$currentPage'");
$seo_data = ($seo_meta_query && $seo_meta_query->num_rows > 0) ? $seo_meta_query->fetch_assoc() : null;

$pageTitle = $seo_data['meta_title'] ?? "Our Gallery | SS Bouncers";
$meta_keywords = $seo_data['meta_key'] ?? "security gallery, bouncers photos, event security images";
$meta_description = $seo_data['meta_desc'] ?? "View our gallery to see our professional security guards, bouncers, and housekeeping staff in action.";

include 'includes/header.php';
include 'includes/breadcrumb.php';

$limit = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalQuery = $conn->query("SELECT COUNT(*) as total FROM gallery");
$totalRow = $totalQuery->fetch_assoc();
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

$galleryQuery = $conn->query("SELECT * FROM gallery ORDER BY ID DESC LIMIT $limit OFFSET $offset");
?>


<!-- ==================== GALLERY SECTION ==================== -->
<section class="gallery-page-section py-5 bg-white">
    <div class="container py-5">

        <!-- Gallery Title / Subheading -->
        <div class="row justify-content-center text-center mb-5 reveal">
            <div class="col-lg-8">
                <span class="sub-heading text-secondary-accent fw-bold text-uppercase tracking-wider">Visual Showcase</span>
                <h2 class="main-heading text-primary-dark fw-bold mt-2">Glimpses of Excellence</h2>
                <p class="text-muted mt-3">See our professional security personnel, trained bouncers, and dedicated staff in action across various deployments.</p>
            </div>
        </div>

        <!-- Dynamic Images Gallery Grid[cite: 12] -->
        <div class="gallery-grid reveal">
            <?php
            if($total_records > 0):
                while ($item = $galleryQuery->fetch_assoc()):
                    // Make sure image path is correct relative to frontend
                    $imagePath = $site . 'admin/' . $item['image_path'];
            ?>

                <div class="gallery-item shadow-sm" onclick="openLightbox('<?php echo htmlspecialchars($imagePath); ?>')">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($item['image_name']); ?>">
                    <!-- Hover Overlay -->
                    <div class="gallery-overlay">
                        <i class="fa-solid fa-expand"></i>
                        <span>Click to Enlarge</span>
                    </div>
                </div>

            <?php 
                endwhile;
            else:
                echo "<div class='col-12 text-center py-5'>
                        <i class='fa-regular fa-image display-1 text-muted opacity-25 mb-3'></i>
                        <h3 class='text-primary-dark'>No Images Found</h3>
                        <p class='text-muted'>Our gallery is currently being updated. Check back later!</p>
                      </div>";
            endif; 
            ?>
        </div>

        <!-- Dynamic Pagination Section[cite: 12] -->
        <?php if($total_pages > 1): ?>
        <div class="row reveal mt-5 pt-4 border-top">
            <div class="col-12">
                <nav aria-label="Gallery Pagination">
                    <ul class="pagination justify-content-center custom-pagination mb-0">
                        <!-- Prev Button -->
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link px-4" href="?page=<?php echo ($page-1); ?>"><i class="fa-solid fa-arrow-left me-2"></i> Prev</a>
                        </li>

                        <!-- Page Numbers -->
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
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

<!-- Lightbox Modal Container[cite: 12] -->
<div class="custom-lightbox" id="lightbox">
    <div class="lightbox-close" onclick="closeLightbox()"><i class="fa-solid fa-xmark"></i></div>
    <div class="lightbox-content">
        <img id="lightbox-img" src="" alt="Enlarged Image">
    </div>
</div>

<!-- ==============================
     JAVASCRIPT FOR LIGHTBOX & SCROLL
     ============================== -->
<script>
    // 1. LIGHTBOX LOGIC[cite: 12]
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');

    function openLightbox(imageSrc) {
        lightboxImg.src = imageSrc;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden'; // Stop page scrolling
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = 'auto'; // Resume page scrolling
        setTimeout(() => {
            lightboxImg.src = '';
        }, 300); // Clear source after fade out
    }

    // Close on background click
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox || e.target.classList.contains('lightbox-content')) {
            closeLightbox();
        }
    });

    // Close on Escape key
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
        }, { threshold: 0.15 });

        reveals.forEach(reveal => revealOnScroll.observe(reveal));
    });
</script>

<?php include 'includes/footer.php'; ?>