<?php
$rawTitle = isset($pageTitle) ? $pageTitle : 'Bhagirath Enterprise';

$displayTitle = explode(' | ', $rawTitle)[0]; 
?>

<section class="breadcrumb-wrapper">
    <div class="container">
        <!-- Dynamic Title -->
        <h2 class="breadcrumb-title"><?php echo htmlspecialchars($displayTitle); ?></h2>
        
        <ul class="custom-breadcrumb">
            <li><a href="index.php"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="active"><?php echo htmlspecialchars($displayTitle); ?></li>
        </ul>
    </div>
</section>