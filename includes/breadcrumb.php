<!-- Breadcrumb Section -->
<section class="breadcrumb-section">
    <div class="container text-center">
        <!-- Dynamic Page Title (Uses PHP Variable) -->
        <h1 class="breadcrumb-title" data-aos="fade-down">
            <?php echo isset($pageTitle) ? $pageTitle : 'Page Name'; ?>
        </h1>
        
        <!-- Breadcrumb Links -->
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="200">
            <ol class="breadcrumb breadcrumb-custom justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo isset($pageTitle) ? $pageTitle : 'Page Name'; ?>
                </li>
            </ol>
        </nav>
    </div>
</section>