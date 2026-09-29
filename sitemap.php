<?php
header("Content-Type: application/xml; charset=utf-8");
require_once 'config/connect.php';

global $site;
$baseUrl = !empty($site) ? $site : "https://chocolate-llama-622925.hostingersite.com/"; // Backup URL

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// 1. Static Pages
$staticPages = [
    ["url" => "", "priority" => "1.0", "changefreq" => "daily"],
    ["url" => "about.php", "priority" => "0.8", "changefreq" => "monthly"],
    ["url" => "services.php", "priority" => "0.9", "changefreq" => "weekly"],
    ["url" => "gallery.php", "priority" => "0.7", "changefreq" => "monthly"],
    ["url" => "blog.php", "priority" => "0.8", "changefreq" => "weekly"],
    ["url" => "contact.php", "priority" => "0.7", "changefreq" => "monthly"],
    ["url" => "privacy-policy.php", "priority" => "0.3", "changefreq" => "yearly"],
    ["url" => "terms-conditions.php", "priority" => "0.3", "changefreq" => "yearly"],
    ["url" => "refund-cancellation.php", "priority" => "0.3", "changefreq" => "yearly"]
];

foreach ($staticPages as $page) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($baseUrl . $page['url']) . '</loc>';
    echo '<changefreq>' . $page['changefreq'] . '</changefreq>';
    echo '<priority>' . $page['priority'] . '</priority>';
    echo '</url>';
}

// 2. Dynamic Services
if (isset($conn)) {
    $servQuery = $conn->query("SELECT id, slug_url FROM services WHERE status = 1");
    if ($servQuery && $servQuery->num_rows > 0) {
        while ($row = $servQuery->fetch_assoc()) {
            $servVal = !empty($row['slug_url']) ? $row['slug_url'] : $row['id'];
            echo '<url>';
            echo '<loc>' . htmlspecialchars($baseUrl . 'service-details.php?slug=' . urlencode($servVal)) . '</loc>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.8</priority>';
            echo '</url>';
        }
    }

    // 3. Dynamic Blogs
    $blogQuery = $conn->query("SELECT slug FROM blogs WHERE status = 1");
    if ($blogQuery && $blogQuery->num_rows > 0) {
        while ($row = $blogQuery->fetch_assoc()) {
            if (!empty($row['slug'])) {
                echo '<url>';
                echo '<loc>' . htmlspecialchars($baseUrl . 'blog-details.php?slug=' . urlencode($row['slug'])) . '</loc>';
                echo '<changefreq>weekly</changefreq>';
                echo '<priority>0.7</priority>';
                echo '</url>';
            }
        }
    }
}

echo '</urlset>';
?>