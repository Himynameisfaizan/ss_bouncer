<?php
$rawTitle = isset($pageTitle) ? $pageTitle : 'SS Bouncers';
$displayTitle = explode(' | ', $rawTitle)[0]; 
?>

<section class="breadcrumb-wrapper">
    <div class="breadcrumb-overlay"></div>
    <div class="container position-relative z-2">
        <h2 class="breadcrumb-title"><?php echo htmlspecialchars($displayTitle); ?></h2>
        
        <ul class="custom-breadcrumb">
            <li><a href="index.php"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="active"><?php echo htmlspecialchars($displayTitle); ?></li>
        </ul>
    </div>
</section>