<?php
// 1. Database Connection Include
include 'admin/db-conn.php';

$service_found = false;
$pageTitle = "Service Details";

// 2. URL se Service ID fetch karo aur DB mein check karo
if (isset($_GET['id'])) {
    $service_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
        $pageTitle = $service['service_name'];
        $service_found = true; // Service mil gayi
    }
}

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<section class="section-padding bg-light-gray">
    <div class="container">

        <?php if ($service_found): ?>
            <!-- ================= CONTENT FOUND ================= -->
            <div class="row">

                <div class="col-lg-8 mb-5 mb-lg-0" data-aos="fade-up">

                    <img src="admin/assets/img/uploads/<?php echo htmlspecialchars($service['img_path']); ?>"
                        alt="<?php echo htmlspecialchars($service['service_name']); ?>" class="service-details-img">

                    <h2 class="fw-bold text-secondary mb-4"><?php echo htmlspecialchars($service['service_name']); ?></h2>

                    <p class="text-muted mb-4" style="line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars_decode($service['short_desc'])); ?>
                    </p>

                    <?php if (!empty($service['long_desc'])): ?>
                        <p class="text-muted mb-5" style="line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars_decode($service['long_desc'])); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($service['benefits'])): ?>
                        <h3 class="fw-bold text-secondary mb-4">Key Benefits of Our Service</h3>
                        <div class="row mb-5">
                            <?php
                            $benefits_array = explode(',', $service['benefits']);
                            $benefits_array = array_filter(array_map('trim', $benefits_array));
                            $benefits_array = array_values($benefits_array);

                            $total_benefits = count($benefits_array);
                            $half = ceil($total_benefits / 2);
                            ?>

                            <!-- Column 1 -->
                            <div class="col-md-6">
                                <ul class="list-custom">
                                    <?php
                                    for ($i = 0; $i < $half; $i++) {
                                        echo '<li><i class="fas fa-check-circle"></i> ' . htmlspecialchars($benefits_array[$i]) . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>

                            <!-- Column 2 -->
                            <div class="col-md-6">
                                <ul class="list-custom">
                                    <?php
                                    for ($i = $half; $i < $total_benefits; $i++) {
                                        echo '<li><i class="fas fa-check-circle"></i> ' . htmlspecialchars($benefits_array[$i]) . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar Area -->
                <div class="col-lg-4" data-aos="fade-left">
                    <!-- Services List Widget (Dynamic) -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-widget-title">All Services</h4>
                        <ul class="service-list">
                            <?php
                            // Database se saari services fetch karo Sidebar ke liye
                            $all_services = $conn->query("SELECT id, service_name FROM services ORDER BY id DESC LIMIT 10");

                            if ($all_services && $all_services->num_rows > 0) {
                                while ($row = $all_services->fetch_assoc()) {
                                    $isActive = ($row['id'] == $service_id) ? 'active' : '';
                                    echo '<li><a href="service-details.php?id=' . $row['id'] . '" class="' . $isActive . '">' . htmlspecialchars($row['service_name']) . ' <i class="fas fa-angle-right float-end mt-1"></i></a></li>';
                                }
                            } else {
                                echo '<li><a href="#">No other services available</a></li>';
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- Contact Help Widget -->
                    <div class="sidebar-widget help-widget">
                        <i class="fas fa-headset fs-1 text-primary-custom mb-3"></i>
                        <h4 class="fw-bold mb-3">Need Any Help?</h4>
                        <p class="text-white-50 mb-4">Contact our expert team to get a customized security plan for your
                            premises.</p>
                        <h5 class="text-primary-custom fw-bold mb-4"><i class="fas fa-phone-alt me-2"></i> <a class="text-primary-custom fw-bold mb-4" style="text-decoration: none;" href="tel:+917200864976">+91 72008 64976</a>
                        </h5>
                        <a href="https://wa.me/917200864976?text=Hello,%20I%20am%20interested%20in%20<?php echo urlencode($service['service_name']); ?>"
                            target="_blank" class="btn btn-primary-custom w-100">Chat on WhatsApp</a>
                    </div>
                </div>

            </div>
        <?php else: ?>
            <!-- ================= CONTENT NOT FOUND ================= -->
            <div class="row justify-content-center text-center py-5">
                <div class="col-md-8" data-aos="zoom-in">
                    <i class="fas fa-exclamation-triangle text-primary-custom mb-4" style="font-size: 60px;"></i>
                    <h2 class="fw-bold text-secondary mb-3">No Service Details Found!</h2>
                    <p class="text-muted mb-5 fs-5">We couldn't find the service you are looking for. It might have been
                        removed or the link is incorrect.</p>
                    <a href="services.php" class="btn btn-primary-custom px-4 py-2"><i class="fas fa-arrow-left me-2"></i>
                        Back to Services</a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>