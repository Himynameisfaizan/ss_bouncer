<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "db-conn.php";

if (!isset($_GET['edit_product_details'])) {
    die("Product ID is missing from the URL.");
}

$product_id = intval($_GET['edit_product_details']);

// Fetch product details with prepared statement
$query = "SELECT p.*, 
          c.categories as category_name,
          p.pro_cate as resolved_cate_id
          FROM products p 
          LEFT JOIN categories c ON p.pro_cate = c.cate_id 
          WHERE p.pro_id = ?";
          
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
} else {
    die("Product not found.");
}
$stmt->close();

// Fetch categories
$category_query = "SELECT * FROM `categories` ORDER BY categories";
$categories_result = mysqli_query($conn, $category_query);

// Add columns if not exists
$columns_to_check = ['image_fx', 'editor_config', 'size_options'];
foreach($columns_to_check as $col) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE '$col'");
    if(mysqli_num_rows($check) == 0) {
        $after = ($col == 'size_options') ? 'image_fx' : 'text_zones';
        mysqli_query($conn, "ALTER TABLE products ADD COLUMN `$col` TEXT DEFAULT NULL AFTER `$after`");
    }
}

// Load Google Fonts list
include_once 'google_fonts_list.php';
$allGoogleFonts = array_keys($GOOGLE_FONTS);
$system_fonts = ['Arial','Georgia','Impact','Verdana','Times New Roman','Tahoma','Courier New','Palatino','Garamond','Comic Sans MS','Lucida Console','Trebuchet MS'];
$gf_to_load = array_diff($allGoogleFonts, $system_fonts);
$gf_to_load = array_slice($gf_to_load, 0, 100, true);

$gf_url = 'https://fonts.googleapis.com/css2?';
$font_params = array_map(function($f) { return 'family='.urlencode($f).':wght@400;700'; }, $gf_to_load);
$gf_url .= implode('&', array_slice($font_params, 0, 50)) . '&display=swap';

// Parse editor config
$editor_config = [];
if (!empty($product['editor_config'])) {
    $decoded = json_decode($product['editor_config'], true);
    if (is_array($decoded)) { $editor_config = $decoded; }
}

function ec($key, $cfg) { return !empty($cfg[$key]) ? 'checked' : ''; }

$saved_fonts = !empty($editor_config['fonts']) ? $editor_config['fonts'] : $allGoogleFonts;
$product_img = !empty($product['pro_img']) ? 'assets/img/uploads/'.$product['pro_img'] : '';
$text_zones_json = !empty($product['text_zones']) ? $product['text_zones'] : '[]';
$image_fx_json = !empty($product['image_fx']) ? $product['image_fx'] : '{}';
$size_options_json = !empty($product['size_options']) ? $product['size_options'] : '[]';

// New settings defaults
$default_base = $editor_config['default_base'] ?? 'black';
$show_text_colors = isset($editor_config['show_text_colors']) ? (bool)$editor_config['show_text_colors'] : true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Product - <?= htmlspecialchars($product['pro_name']) ?></title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?= htmlspecialchars($gf_url) ?>" rel="stylesheet">
    
    <style>
    .font-picker-wrap { position:relative; }
    .font-picker-search { width:100%; padding:8px 12px; border:1.5px solid #4e73df; border-radius:8px 8px 0 0; font-size:13px; outline:none; box-sizing:border-box; }
    .font-picker-list { max-height:200px; overflow-y:auto; border:1.5px solid #4e73df; border-top:none; border-radius:0 0 8px 8px; background:#fff; }
    .font-picker-item { display:flex; align-items:center; justify-content:space-between; padding:8px 12px; cursor:pointer; border-bottom:1px solid #f0f0f0; transition:background .15s; }
    .font-picker-item:hover { background:#f0f4ff; } .font-picker-item.selected { background:#e8eeff; }
    .font-preview { font-size:15px; flex:1; } .font-check { color:#4e73df; font-weight:bold; font-size:14px; }
    .selected-fonts-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; min-height:32px; padding:6px; background:#f8f9fa; border-radius:8px; border:1px solid #e0e0e0; }
    .font-tag { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; background:#4e73df; color:#fff; border-radius:20px; font-size:12px; }
    .remove-tag { cursor:pointer; font-weight:bold; opacity:.8; } .remove-tag:hover { opacity:1; }
    .picker-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; }
    .picker-btn { padding:4px 10px; font-size:12px; border:1px solid #4e73df; color:#4e73df; background:#fff; border-radius:6px; cursor:pointer; }
    .picker-btn:hover { background:#4e73df; color:#fff; }

    .form-section { background:#fff; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05); margin-bottom:25px; padding:25px; border-left:4px solid #4e73df; }
    .section-title { font-size:1.1rem; font-weight:600; color:#4e73df; margin-bottom:20px; display:flex; align-items:center; }
    .section-title i { margin-right:10px; font-size:1.3rem; }
    .preview-image-container { display:flex; flex-wrap:wrap; gap:10px; margin-top:15px; }
    .preview-image { width:80px; height:80px; object-fit:cover; border-radius:4px; border:1px solid #eee; }
    .current-image { position:relative; display:inline-block; margin-right:10px; margin-bottom:10px; }
    .current-image img { width:100px; height:100px; object-fit:cover; border-radius:4px; border:1px solid #ddd; }
    .remove-image-btn { position:absolute; top:-5px; right:-5px; background:#e74a3b; color:#fff; border:none; border-radius:50%; width:24px; height:24px; font-size:12px; cursor:pointer; }
    .required-field::after { content:" *"; color:#f44336; }
    .nav-tabs .nav-link { color:#555; font-weight:500; }
    .nav-tabs .nav-link.active { color:#4e73df; border-bottom:3px solid #4e73df; }
    
    /* Size Options */
    .size-options-table { width:100%; border-collapse:collapse; }
    .size-options-table th { background:#f8f9fa; padding:10px; font-size:13px; text-align:left; border-bottom:2px solid #e0e0e0; }
    .size-options-table td { padding:6px 10px; border-bottom:1px solid #eee; }
    .size-options-table input[type="text"] { width:100%; padding:6px 10px; border:1px solid #ddd; border-radius:5px; font-size:13px; }
    .size-options-table input[type="number"] { width:100px; padding:6px 10px; border:1px solid #ddd; border-radius:5px; font-size:13px; }
    .size-options-table input[type="checkbox"] { width:18px; height:18px; cursor:pointer; }
    .remove-size-btn { background:#ef4444; color:#fff; border:none; border-radius:4px; padding:4px 10px; cursor:pointer; font-size:11px; }
    
    /* Text Zones */
    #tz-wrap { display:flex; gap:18px; flex-wrap:wrap; }
    #tz-canvas-col { flex:1; min-width:280px; }
    #tz-panel-col { width:320px; min-width:260px; }
    #tz-canvas-wrap { position:relative; background:#f5f5f5; border:2px dashed #ccc; border-radius:8px; overflow:hidden; cursor:crosshair; }
    #tz-canvas { display:block; width:100%; height:auto; max-width: 100%; }
    .tz-zone-card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:12px; margin-bottom:10px; position:relative; cursor:pointer; }
    .tz-zone-card.active-zone { border-color:#4e73df; box-shadow:0 0 0 2px rgba(78,115,223,0.2); }
    .tz-zone-card .zone-label { font-weight:600; font-size:13px; color:#4e73df; }
    .tz-zone-card .delete-zone { position:absolute; top:8px; right:8px; background:#ef4444; color:#fff; border:none; border-radius:4px; padding:2px 8px; font-size:11px; cursor:pointer; }
    .tz-mini-label { font-size:11px; color:#888; margin-bottom:3px; }
    .tz-row { display:flex; gap:6px; margin-bottom:8px; align-items:center; }
    .tz-btn { padding:6px 14px; border:1px solid #4e73df; color:#4e73df; background:#fff; border-radius:6px; cursor:pointer; font-size:13px; }
    .tz-btn:hover { background:#4e73df; color:#fff; }
    .tz-btn-add { background:#4e73df; color:#fff; border:none; border-radius:6px; padding:8px 18px; cursor:pointer; font-size:13px; width:100%; margin-bottom:12px; }
    .tz-input { width:100%; padding:5px 8px; border:1px solid #ddd; border-radius:5px; font-size:13px; }
    .tz-select { padding:5px 6px; border:1px solid #ddd; border-radius:5px; font-size:12px; }
    #tz-no-img { text-align:center; padding:40px 20px; color:#aaa; font-size:13px; }
    
    .image-fx-section { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:12px; margin-bottom:12px; }
    .image-fx-title { font-size:13px; font-weight:600; color:#4e73df; margin-bottom:10px; display:flex; align-items:center; gap:6px; }
    .fx-row { display:flex; gap:10px; margin-bottom:8px; align-items:center; }
    .fx-row label { font-size:11px; color:#666; min-width:70px; }
    .fx-row input[type="range"] { flex:1; height:4px; -webkit-appearance:none; background:#ddd; border-radius:2px; outline:none; }
    .fx-row input[type="range"]::-webkit-slider-thumb { -webkit-appearance:none; width:16px; height:16px; border-radius:50%; background:#4e73df; cursor:pointer; }
    .fx-row span { font-size:11px; color:#888; min-width:35px; text-align:right; }
    .fx-preset-btn { padding:3px 10px; font-size:11px; border:1px solid #4e73df; color:#4e73df; background:#fff; border-radius:12px; cursor:pointer; }
    .fx-preset-btn:hover { background:#4e73df; color:#fff; }
    .fx-color-input { width:32px; height:28px; padding:2px; border:1px solid #ddd; border-radius:4px; cursor:pointer; }
    .tz-shadow-toggle { display:flex; align-items:center; gap:6px; font-size:12px; cursor:pointer; }
    .tz-shadow-color-pick { width:32px; height:28px; padding:2px; border:1px solid #ddd; border-radius:4px; cursor:pointer; }
    .tz-shadow-opacity-slider { width:100px; }
    </style>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>
    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid g-0"><div class="row"><div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div></div></div>
        <div class="main_content_iner">
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0"><div class="main-title"><h2 class="m-0"><i class="fas fa-edit me-2"></i>Edit Product</h2><p class="m-0 text-muted">Update product details below</p></div></div>
                            </div>
                            <div class="white_card_body">
                                <div class="card-body">
                                    <ul class="nav nav-tabs mb-4" id="productTabs" role="tablist">
                                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basic"><i class="fas fa-info-circle me-1"></i>Basic Info</button></li>
                                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#media"><i class="fas fa-image me-1"></i>Media & SEO</button></li>
                                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#sizes"><i class="fas fa-ruler me-1"></i>Sizes & Pricing</button></li>
                                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#editor"><i class="fas fa-paint-brush me-1"></i>Editor Settings</button></li>
                                        <li class="nav-item"><button class="nav-link" id="zones-tab" data-bs-toggle="tab" data-bs-target="#zones"><i class="fas fa-font me-1"></i>Text Zones</button></li>
                                    </ul>

                                    <form action="functions.php" method="POST" enctype="multipart/form-data" id="productForm">
                                        <input type="hidden" name="pro_id" value="<?= htmlspecialchars($product['pro_id']) ?>" />
                                        <input type="hidden" name="removed_images" id="removed_images" value="" />
                                        <input type="hidden" name="text_zones" id="text_zones_input" value="<?= htmlspecialchars($text_zones_json) ?>">
                                        <input type="hidden" name="image_fx" id="image_fx_input" value="<?= htmlspecialchars($image_fx_json) ?>">
                                        <input type="hidden" name="size_options" id="size_options_input" value="<?= htmlspecialchars($size_options_json) ?>">

                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="basic">
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-info-circle"></i> Basic Information</div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3"><label class="form-label required-field">Product Name</label><input type="text" class="form-control" name="pro_name" id="pro_name" value="<?= htmlspecialchars($product['pro_name']) ?>" required></div>
                                                        <div class="col-md-6 mb-3"><label class="form-label required-field">Main Category</label><select class="form-select" name="pro_cate" onchange="getSubcategory(this.value)" required><option value="">Select a category</option><?php mysqli_data_seek($categories_result,0); while($c=mysqli_fetch_assoc($categories_result)){$sel=($product['resolved_cate_id']==$c['cate_id'])?'selected':'';echo "<option value='{$c['cate_id']}' $sel>".htmlspecialchars(ucwords($c['categories']))."</option>";} ?></select></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3"><label class="form-label">Sub Category</label><select class="form-select" name="pro_sub_cate" id="subcate_id"><option value="">Select subcategory (optional)</option></select></div>
                                                        <div class="col-md-6 mb-3"><label class="form-label">Brand Name</label><input type="text" class="form-control" name="brand_name" value="<?= htmlspecialchars($product['brand_name']??'') ?>"></div>
                                                    </div>
                                                    <div class="mb-3"><label class="form-label">Short Description</label><textarea class="form-control" name="short_desc" id="short_desc" rows="3"><?= htmlspecialchars($product['short_desc']??'') ?></textarea></div>
                                                    <div class="mb-3"><label class="form-label">Full Description</label><textarea class="form-control" name="description" id="pro_desc" rows="5"><?= htmlspecialchars($product['description']??'') ?></textarea></div>
                                                </div>
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-dollar-sign"></i> Pricing & Inventory</div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3"><label class="form-label required-field">MRP (₹)</label><input type="number" class="form-control" name="mrp" step="0.01" min="0" value="<?= htmlspecialchars($product['mrp']) ?>" required></div>
                                                        <div class="col-md-4 mb-3"><label class="form-label required-field">Selling Price (₹)</label><input type="number" class="form-control" name="selling_price" step="0.01" min="0" value="<?= htmlspecialchars($product['selling_price']) ?>" required></div>
                                                        <div class="col-md-4 mb-3"><label class="form-label">Stock</label><input type="number" class="form-control" name="stock" min="0" value="<?= htmlspecialchars($product['stock']??0) ?>"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1" <?=($product['status']??1)==1?'selected':''?>>Active</option><option value="0" <?=($product['status']??1)==0?'selected':''?>>Inactive</option></select></div>
                                                        <div class="col-md-4 mb-3"><label class="form-label">New Arrival</label><select class="form-select" name="new_arrival"><option value="0" <?=($product['new_arrival']??0)==0?'selected':''?>>No</option><option value="1" <?=($product['new_arrival']??0)==1?'selected':''?>>Yes</option></select></div>
                                                        <div class="col-md-4 mb-3"><label class="form-label">Trending</label><select class="form-select" name="trending"><option value="0" <?=($product['trending']??0)==0?'selected':''?>>No</option><option value="1" <?=($product['trending']??0)==1?'selected':''?>>Yes</option></select></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="media">
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-images"></i> Product Image</div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Current Image</label>
                                                        <div class="current-image" id="currentImageContainer">
                                                            <?php if(!empty($product['pro_img'])): ?>
                                                                <img src="assets/img/uploads/<?= htmlspecialchars($product['pro_img']) ?>" id="domProductTargetImage" alt="Product Image">
                                                                <button type="button" class="remove-image-btn" onclick="removeImage('<?= htmlspecialchars($product['pro_img']) ?>')">×</button>
                                                            <?php else: ?>
                                                                <img src="" id="domProductTargetImage" style="display:none;">
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3"><label class="form-label">Upload New Image</label><input type="file" class="form-control" name="pro_img" id="pro_img" accept="image/*"><div class="preview-image-container" id="imagePreview"></div></div>
                                                </div>
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-search"></i> SEO Settings</div>
                                                    <div class="mb-3"><label class="form-label">Meta Title</label><input type="text" class="form-control" name="meta_title" maxlength="60" value="<?= htmlspecialchars($product['meta_title']??'') ?>"></div>
                                                    <div class="mb-3"><label class="form-label">Meta Description</label><textarea class="form-control" name="meta_desc" rows="3" maxlength="160"><?= htmlspecialchars($product['meta_desc']??'') ?></textarea></div>
                                                    <div class="mb-3"><label class="form-label">Meta Keywords</label><input type="text" class="form-control" name="meta_key" value="<?= htmlspecialchars($product['meta_key']??'') ?>"></div>
                                                    <div class="mb-3"><label class="form-label">Slug URL</label><input type="text" class="form-control" name="slug_url" id="slug_url" value="<?= htmlspecialchars($product['slug_url']??'') ?>"></div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="sizes">
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-ruler"></i> Size Options & Pricing</div>
                                                    <p class="text-muted mb-3">Different sizes ke liye alag price set karo. Customer product page pe size select kar sakega.</p>
                                                    <table class="size-options-table" id="sizeTable">
                                                        <thead><tr><th style="width:40px;">Enable</th><th>Size Label</th><th style="width:130px;">Price Adj (₹)</th><th>Dimensions</th><th style="width:50px;"></th></tr></thead>
                                                        <tbody id="sizeTableBody"></tbody>
                                                    </table>
                                                    <div style="margin-top:10px;"><button type="button" onclick="addSizeRow()" style="padding:6px 15px;background:#4e73df;color:#fff;border:none;border-radius:5px;cursor:pointer;font-size:12px;">+ Add Size Option</button><small class="text-muted ms-2">Price Adj = Base price mein kitna add hoga</small></div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="editor">
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-paint-brush"></i> Product Page Editor Settings</div>
                                                    <div class="row g-3">
                                                        <?php foreach([['name'=>'ec_name_change','label'=>'✏️ Name / Text Change','color'=>'#4e73df'],['name'=>'ec_bg_color','label'=>'🎨 Product Color','color'=>'#e74a3b'],['name'=>'ec_brush','label'=>'🖌️ Brush Tool','color'=>'#1cc88a'],['name'=>'ec_eraser','label'=>'🧹 Eraser Tool','color'=>'#f6c23e'],['name'=>'ec_size','label'=>'📐 Size Options','color'=>'#36b9cc'],['name'=>'ec_download','label'=>'📥 Download Button','color'=>'#858796']] as $f): ?>
                                                        <div class="col-md-6"><div class="card border-0 shadow-sm p-3" style="border-left:4px solid <?=$f['color']?>"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="<?=$f['name']?>" value="1" <?=ec(str_replace('ec_','',$f['name']),$editor_config)?>><label class="form-check-label fw-bold"><?=$f['label']?></label></div></div></div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <div class="mt-4"><label class="form-label fw-bold">🎨 Preset Colors</label><input type="text" class="form-control" name="ec_preset_colors" value="<?= htmlspecialchars($editor_config['preset_colors']??'#ff0000,#ff8c00,#ffd700,#00aa00,#0000ff,#800080') ?>"></div>
                                                    <div class="mt-3"><label class="form-label fw-bold">🏷️ Editor Heading</label><input type="text" class="form-control" name="ec_heading" value="<?= htmlspecialchars($editor_config['heading']??'Customize Your Product') ?>"></div>
                                                    
                                                    <!-- 🔥 NEW: Design Base Background -->
                                                    <div class="mt-3">
                                                        <label class="form-label fw-bold">🖼️ Default Base Background</label>
                                                        <select class="form-select" name="ec_default_base" style="max-width:250px;">
                                                            <option value="black" <?= $default_base == 'black' ? 'selected' : '' ?>>⚫ Black</option>
                                                            <option value="white" <?= $default_base == 'white' ? 'selected' : '' ?>>⚪ White</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- 🔥 NEW: Show/Hide Text Colour Swatches -->
                                                    <div class="mt-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="ec_show_text_colors" id="ec_show_text_colors" value="1" <?= $show_text_colors ? 'checked' : '' ?>>
                                                            <label class="form-check-label fw-bold" for="ec_show_text_colors">🎨 Show Text Colour Swatches</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-4"><label class="form-label fw-bold">🔤 Available Fonts</label><div id="ec_fonts_hidden_wrap"></div><div class="picker-header"><span>Selected: <b id="fontCountBadge"><?=count($saved_fonts)?></b></span><div class="picker-actions"><button type="button" class="picker-btn" onclick="fpSelectAll()">Select All</button><button type="button" class="picker-btn" onclick="fpClearAll()">Clear All</button></div></div><div class="selected-fonts-tags" id="selectedFontTags"></div><div class="font-picker-wrap mt-2"><input type="text" class="font-picker-search" id="fontSearchInput" placeholder="🔍 Search fonts..."><div class="font-picker-list" id="fontPickerList"></div></div></div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="zones">
                                                <div id="tz-wrap">
                                                    <div id="tz-canvas-col"><p style="font-size:13px;color:#666;margin-bottom:8px;">👆 Click on canvas to add text zone. Drag to move.</p><div id="tz-canvas-wrap"><div id="tz-no-img"><i class="fas fa-image" style="font-size:32px;display:block;margin-bottom:8px;"></i>Loading product image...</div><canvas id="tz-canvas" style="display:none;"></canvas></div></div>
                                                    <div id="tz-panel-col">
                                                        <button type="button" class="tz-btn-add" onclick="tzAddZone()">+ Add New Text Zone</button>
                                                        <div id="tz-active-controls" style="display:none;background:#f8f9ff;border:1px solid #d0d8ff;border-radius:8px;padding:12px;margin-bottom:12px;">
                                                            <div style="font-size:12px;font-weight:600;color:#4e73df;margin-bottom:10px;">✏️ Selected Zone Settings</div>
                                                            <div class="tz-mini-label">Label</div><input type="text" class="tz-input" id="tz-label" placeholder="e.g. Your Name" oninput="tzUpdateSelected('label',this.value)">
                                                            <div class="tz-mini-label" style="margin-top:8px;">Default Text</div><input type="text" class="tz-input" id="tz-default" placeholder="e.g. SHARMA" oninput="tzUpdateSelected('defaultText',this.value)">
                                                            <div class="tz-mini-label" style="margin-top:8px;">Font Family</div>
                                                            <div class="font-picker-wrap"><input type="text" class="font-picker-search" id="tz-font-search" placeholder="🔍 Search font..." autocomplete="off"><div class="font-picker-list" id="tz-font-list" style="max-height:150px;"></div></div>
                                                            <input type="hidden" id="tz-font" value="Arial"><div id="tz-selected-font" style="font-size:14px;padding:6px 10px;background:#f0f4ff;border-radius:6px;margin-bottom:8px;font-family:Arial;">Arial ✓</div>
                                                            <div class="tz-row"><div style="width:70px;"><div class="tz-mini-label">Size</div><input type="number" class="tz-input" id="tz-size" value="30" min="10" max="500" oninput="tzUpdateSelected('size',parseInt(this.value)||30)"></div><div style="flex:1;"><div class="tz-mini-label">Color</div><input type="color" id="tz-color" value="#ffffff" oninput="tzUpdateSelected('color',this.value)" style="width:100%;height:32px;"></div><div style="flex:1;"><div class="tz-mini-label">Align</div><select class="tz-select" id="tz-align" onchange="tzUpdateSelected('align',this.value)" style="width:100%;"><option value="center">Center</option><option value="left">Left</option><option value="right">Right</option></select></div></div>
                                                            <div style="margin-top:10px;border-top:1px solid #e0e0e0;padding-top:10px;"><div class="tz-mini-label" style="font-weight:600;color:#4e73df;">💫 Text Shadow / Glow</div><div class="tz-shadow-toggle"><input type="checkbox" id="tz-shadow-enabled" onchange="tzUpdateSelected('shadowEnabled',this.checked)"><span>Enable Shadow</span></div><div id="tz-shadow-settings" style="display:none;"><div class="tz-row"><div style="flex:1;"><div class="tz-mini-label">Shadow Color</div><input type="color" class="tz-shadow-color-pick" id="tz-shadow-color" value="#000000" oninput="tzUpdateSelected('shadowColor',this.value)" style="width:100%;height:32px;"></div><div style="flex:1;"><div class="tz-mini-label">Blur</div><input type="number" class="tz-input" id="tz-shadow-blur" value="8" min="0" max="50" oninput="tzUpdateSelected('shadowBlur',parseInt(this.value)||0)"></div></div><div class="tz-row"><div style="flex:1;"><div class="tz-mini-label">Offset X</div><input type="number" class="tz-input" id="tz-shadow-offx" value="2" min="-20" max="20" oninput="tzUpdateSelected('shadowOffX',parseInt(this.value)||0)"></div><div style="flex:1;"><div class="tz-mini-label">Offset Y</div><input type="number" class="tz-input" id="tz-shadow-offy" value="2" min="-20" max="20" oninput="tzUpdateSelected('shadowOffY',parseInt(this.value)||0)"></div></div><div class="tz-row"><div style="flex:1;"><div class="tz-mini-label">Opacity</div><input type="range" class="tz-shadow-opacity-slider" id="tz-shadow-opacity" min="0" max="100" value="80" oninput="tzUpdateSelected('shadowOpacity',parseInt(this.value));document.getElementById('tz-shadow-opacity-val').textContent=this.value+'%';"><span id="tz-shadow-opacity-val">80%</span></div></div></div></div>
                                                            <div class="tz-mini-label" style="margin-top:8px;">Bold / Italic</div><div class="tz-row"><label><input type="checkbox" id="tz-bold" onchange="tzUpdateSelected('bold',this.checked)"> Bold</label><label><input type="checkbox" id="tz-italic" onchange="tzUpdateSelected('italic',this.checked)"> Italic</label></div>
                                                            <div class="tz-mini-label">Max Characters (0=unlimited)</div><input type="number" class="tz-input" id="tz-maxlen" value="0" min="0" max="200" oninput="tzUpdateSelected('maxLen',parseInt(this.value)||0)">
                                                            <div class="tz-mini-label">Required?</div><label><input type="checkbox" id="tz-required" onchange="tzUpdateSelected('required',this.checked)"> Yes</label>
                                                        </div>
                                                        <div class="image-fx-section"><div class="image-fx-title">💫 Image Border Shadow</div><div class="tz-shadow-toggle"><input type="checkbox" id="fx-shadow-enabled" onchange="toggleImageShadow(this.checked)"><span>Enable</span></div><div id="fx-shadow-settings" style="display:none;"><div class="fx-row"><label>Color:</label><input type="color" class="fx-color-input" id="fx-shadow-color" value="#000000" oninput="updateImageFX()"></div><div class="fx-row"><label>Blur:</label><input type="range" id="fx-shadow-blur" min="0" max="100" value="20" oninput="updateImageFX();document.getElementById('fx-shadow-blur-val').textContent=this.value+'px';"><span id="fx-shadow-blur-val">20px</span></div><div class="fx-row"><label>Spread:</label><input type="range" id="fx-shadow-spread" min="0" max="50" value="5" oninput="updateImageFX();document.getElementById('fx-shadow-spread-val').textContent=this.value+'px';"><span id="fx-shadow-spread-val">5px</span></div><div class="fx-row"><label>Offset X:</label><input type="range" id="fx-shadow-offx" min="-50" max="50" value="0" oninput="updateImageFX();document.getElementById('fx-shadow-offx-val').textContent=this.value+'px';"><span id="fx-shadow-offx-val">0px</span></div><div class="fx-row"><label>Offset Y:</label><input type="range" id="fx-shadow-offy" min="-50" max="50" value="5" oninput="updateImageFX();document.getElementById('fx-shadow-offy-val').textContent=this.value+'px';"><span id="fx-shadow-offy-val">5px</span></div><div class="fx-row"><label>Opacity:</label><input type="range" id="fx-shadow-opacity" min="0" max="100" value="60" oninput="updateImageFX();document.getElementById('fx-shadow-opacity-val').textContent=this.value+'%';"><span id="fx-shadow-opacity-val">60%</span></div></div><div style="margin-top:10px;display:flex;gap:6px;"><button type="button" class="fx-preset-btn" onclick="applyFXPreset('soft')">Soft</button><button type="button" class="fx-preset-btn" onclick="applyFXPreset('dark')">Dark</button><button type="button" class="fx-preset-btn" onclick="applyFXPreset('colored')">Colored</button><button type="button" class="fx-preset-btn" onclick="applyFXPreset('neon')">Neon</button><button type="button" class="fx-preset-btn" onclick="applyFXPreset('reset')">Reset</button></div></div>
                                                        <div id="tz-zone-list" style="max-height:300px;overflow-y:auto;"></div>
                                                        <div style="margin-top:12px;"><button type="button" class="tz-btn" onclick="tzSaveZones()" style="width:100%;background:#1cc88a;color:#fff;border-color:#1cc88a;font-weight:600;padding:10px;">💾 Save Zones</button></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="products.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Cancel</a>
                                            <button type="submit" name="update-product" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Product</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include "footer.php"; ?>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    CKEDITOR.replace('pro_desc'); CKEDITOR.replace('short_desc');
    
    // 🔥 IMAGE LIVE REFRESH SYNCING ENGINE FOR TEXT ZONES
    document.getElementById('pro_img').addEventListener('change', function(e) {
        var container = document.getElementById('imagePreview');
        container.innerHTML = '';
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                // 1. Appending to dynamic file upload tab thumbnail preview container
                var img = document.createElement('img');
                img.src = ev.target.result;
                img.classList.add('preview-image');
                container.appendChild(img);
                
                // 2. 🔥 LIVE SYNC FOR TEXT ZONES CANVAS: Replace standard target image element sources
                var masterDomImage = document.getElementById('domProductTargetImage');
                if (masterDomImage) {
                    masterDomImage.src = ev.target.result;
                    masterDomImage.style.display = 'inline-block';
                    // Re-trigger core layout canvas refresh to immediately stream uploaded image
                    if(typeof window.refreshTextZoneImage === 'function') {
                        window.refreshTextZoneImage(ev.target.result);
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    });

    $('#pro_name').on('keyup',function(){var slug=$(this).val().toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');$('#slug_url').val(slug);});
    var removedImages=[]; window.removeImage=function(n){if(confirm('Remove?')){removedImages.push(n);document.getElementById('removed_images').value=removedImages.join(',');var ci=document.querySelector('.current-image');if(ci)ci.remove(); var masterDomImage = document.getElementById('domProductTargetImage'); if(masterDomImage) masterDomImage.src = ''; }};
    function getSubcategory(cid,sid){if(!cid){$('#subcate_id').html('<option value="">Select Sub Category</option>');return;}$.ajax({url:'functions.php',method:'POST',data:{cate_id:cid},success:function(d){$("#subcate_id").html('<option value="">Select subcategory (optional)</option>'+d);if(sid)$("#subcate_id option[value='"+sid+"']").prop('selected',true);}});}
    $(function(){var sc="<?=intval($product['resolved_cate_id']??0)?>";var ss="<?=intval($product['pro_sub_cate']??0)?>";if(sc) getSubcategory(sc,ss);});
    document.getElementById('productForm').addEventListener('submit',function(e){var m=parseFloat(document.querySelector('input[name="mrp"]').value);var s=parseFloat(document.querySelector('input[name="selling_price"]').value);if(s>m){alert('Selling price cannot be higher than MRP');e.preventDefault();}if(s<=0){alert('Selling price must be greater than 0');e.preventDefault();}});
    </script>

    <script>
    (function(){var AF=<?=json_encode(array_values($allGoogleFonts))?>;var sel=new Set(<?=json_encode(array_values($saved_fonts))?>);function r(f){f=(f||'').toLowerCase();var l=document.getElementById('fontPickerList');l.innerHTML=AF.filter(function(x){return x.toLowerCase().indexOf(f)>-1;}).map(function(x){var e=x.replace(/'/g,"\\'");return'<div class="font-picker-item '+(sel.has(x)?'selected':'')+'" onclick="fpToggle(\''+e+'\')"><span class="font-preview" style="font-family:\''+x+'\';">'+x+'</span>'+(sel.has(x)?'<span class="font-check">✓</span>':'')+'</div>';}).join('');}function rt(){var w=document.getElementById('selectedFontTags'),h=document.getElementById('ec_fonts_hidden_wrap'),a=[];sel.forEach(function(f){a.push(f);});w.innerHTML=a.map(function(f){var e=f.replace(/'/g,"\\'");return'<span class="font-tag" style="font-family:\''+f+'\';">'+f+' <span class="remove-tag" onclick="fpToggle(\''+e+'\')">×</span></span>';}).join('');h.innerHTML=a.map(function(f){return'<input type="hidden" name="ec_fonts[]" value="' + f.replace(/"/g,'&quot;') + '">';}).join('');document.getElementById('fontCountBadge').textContent=sel.size;}window.fpToggle=function(f){sel.has(f)?sel.delete(f):sel.add(f);r(document.getElementById('fontSearchInput').value);rt();};window.fpSelectAll=function(){AF.forEach(function(f){sel.add(f);});r(document.getElementById('fontSearchInput').value);rt();};window.fpClearAll=function(){sel.clear();r(document.getElementById('fontSearchInput').value);rt();};document.getElementById('fontSearchInput').addEventListener('input',function(){r(this.value);});r();rt();})();
    </script>

    <script>
    var TZ_FONTS=<?=json_encode(array_values($allGoogleFonts))?>;
    function filterTzFonts(q){var f=(q||'').toLowerCase(),l=document.getElementById('tz-font-list');l.innerHTML=TZ_FONTS.filter(function(x){return x.toLowerCase().indexOf(f)>-1;}).map(function(x){return'<div class="font-picker-item" onclick="selectTzFont(\''+x.replace(/'/g,"\\'")+'\')" style="font-family:\''+x+'\';"><span class="font-preview" style="font-family:\''+x+'\';">'+x+'</span></div>';}).join('');}
    function selectTzFont(f){document.getElementById('tz-font').value=f;document.getElementById('tz-font-search').value='';document.getElementById('tz-font-list').innerHTML='';var d=document.getElementById('tz-selected-font');d.textContent=f+' ✓';d.style.fontFamily=f;tzUpdateSelected('font',f);}
    document.getElementById('tz-font-search').addEventListener('focus',function(){if(document.getElementById('tz-font-list').children.length===0)filterTzFonts('');});
    document.getElementById('tz-font-search').addEventListener('input',function(){filterTzFonts(this.value);});
    </script>

    <script>
    var sizeData=[];
    try{var ss=JSON.parse(document.getElementById('size_options_input').value||'[]');if(Array.isArray(ss)&&ss.length>0)sizeData=ss;else sizeData=[{enabled:true,label:'Small',price_adj:0,dimensions:''},{enabled:true,label:'Medium',price_adj:200,dimensions:''},{enabled:true,label:'Large',price_adj:500,dimensions:''}];}catch(e){sizeData=[{enabled:true,label:'Small',price_adj:0,dimensions:''},{enabled:true,label:'Medium',price_adj:200,dimensions:''},{enabled:true,label:'Large',price_adj:500,dimensions:''}];}
    function renderSizeTable(){var t=document.getElementById('sizeTableBody');t.innerHTML=sizeData.map(function(s,i){return'<tr><td style="text-align:center;"><input type="checkbox" '+(s.enabled?'checked':'')+' onchange="updateSizeData()"></td><td><input type="text" value="'+(s.label||'')+'" onchange="updateSizeData()"></td><td><input type="number" value="'+(s.price_adj||0)+'" step="0.01" onchange="updateSizeData()"></td><td><input type="text" value="'+(s.dimensions||'')+'" placeholder="e.g., 10x10 cm" onchange="updateSizeData()"></td><td><button type="button" class="remove-size-btn" onclick="removeSizeRow('+i+')">✕</button></td></tr>';}).join('');}
    function updateSizeData(){var r=document.querySelectorAll('#sizeTableBody tr');sizeData=[];r.forEach(function(row){var i=row.querySelectorAll('input');sizeData.push({enabled:i[0].checked,label:i[1].value,price_adj:parseFloat(i[2].value)||0,dimensions:i[3].value||''});});document.getElementById('size_options_input').value=JSON.stringify(sizeData);}
    function addSizeRow(){sizeData.push({enabled:true,label:'Extra Large',price_adj:1000,dimensions:''});renderSizeTable();updateSizeData();}
    function removeSizeRow(i){if(sizeData.length<=1){alert('At least one size option required!');return;}sizeData.splice(i,1);renderSizeTable();updateSizeData();}
    $(function(){renderSizeTable();updateSizeData();var f=document.getElementById('productForm');if(f)f.addEventListener('submit',function(){updateSizeData();});});
    </script>

<!-- Text Zones Designer (FIXED AND STABLE IMAGE LINK) -->
<script>
(function() {
    var FONTS = <?= json_encode($allGoogleFonts) ?>;
    var zones = [];
    try { zones = JSON.parse(<?= json_encode($text_zones_json) ?>) || []; } catch(e){ zones=[]; }
    var selectedZoneId = null;
    var tzIdCounter = zones.length ? Math.max.apply(null, zones.map(function(z){return z.id||0;}))+1 : 1;
    var tzCanvas, tzCtx, tzImg = null, tzDragging=false, tzDragOffX=0, tzDragOffY=0, tzScaleX=1, tzScaleY=1;
    
    var imageFX = { shadowEnabled: false, shadowColor: '#000000', shadowBlur: 20, shadowSpread: 5, shadowOffX: 0, shadowOffY: 5, shadowOpacity: 60 };
    try { var sfx = JSON.parse(<?= json_encode($image_fx_json) ?>); if (sfx && Object.keys(sfx).length > 0) { for (var k in sfx) { imageFX[k] = sfx[k]; } } } catch(e) {}

    // 🔥 FIX: Bootstrap ke default tabs tab-pane functionality ko disturb kiye bina canvas load karna
    var targetTabEl = document.getElementById('zones-tab');
    if (targetTabEl) {
        targetTabEl.addEventListener('shown.bs.tab', function (e) {
            // Jab text zones tab successfully active hoga, tabhi canvas initialize hoga
            setTimeout(function() { initTzCanvas(); loadImageFXControls(); }, 150);
        });
    }

    function loadImageFXControls() {
        document.getElementById('fx-shadow-enabled').checked = imageFX.shadowEnabled || false;
        document.getElementById('fx-shadow-color').value = imageFX.shadowColor || '#000000';
        document.getElementById('fx-shadow-blur').value = imageFX.shadowBlur || 20;
        document.getElementById('fx-shadow-spread').value = imageFX.shadowSpread || 5;
        document.getElementById('fx-shadow-offx').value = imageFX.shadowOffX || 0;
        document.getElementById('fx-shadow-offy').value = imageFX.shadowOffY || 5;
        document.getElementById('fx-shadow-opacity').value = imageFX.shadowOpacity || 60;
        document.getElementById('fx-shadow-blur-val').textContent = (imageFX.shadowBlur||20) + 'px';
        document.getElementById('fx-shadow-spread-val').textContent = (imageFX.shadowSpread||5) + 'px';
        document.getElementById('fx-shadow-offx-val').textContent = (imageFX.shadowOffX||0) + 'px';
        document.getElementById('fx-shadow-offy-val').textContent = (imageFX.shadowOffY||5) + 'px';
        document.getElementById('fx-shadow-opacity-val').textContent = (imageFX.shadowOpacity||60) + '%';
        document.getElementById('fx-shadow-settings').style.display = imageFX.shadowEnabled ? 'block' : 'none';
    }

    function initTzCanvas() {
        tzCanvas = document.getElementById('tz-canvas');
        if (!tzCanvas) return;
        tzCtx = tzCanvas.getContext('2d');
        
        var imgElement = document.getElementById('domProductTargetImage');
        
        if (!imgElement || imgElement.getAttribute('src') === "" || imgElement.naturalWidth === 0) {
            document.getElementById('tz-no-img').style.display = 'block';
            document.getElementById('tz-no-img').innerHTML = '<i class="fas fa-image mb-2 d-block" style="font-size:24px;"></i> No base product image found. Please upload or save an image first.';
            tzCanvas.style.display = 'none';
            return;
        }
        
        document.getElementById('tz-no-img').style.display = 'none';
        tzCanvas.style.display = 'block';
        tzCanvas.width = imgElement.naturalWidth;
        tzCanvas.height = imgElement.naturalHeight;
        
        tzImg = imgElement;
        
        updateScale();
        updateImageFX();
        tzRender();
        renderZoneList();
    }

    window.refreshTextZoneImage = function(newSrcStr) {
        var c = document.getElementById('tz-canvas');
        if(!c || c.style.display === 'none') return;
        var testImg = new Image();
        testImg.onload = function() {
            c.width = testImg.width;
            c.height = testImg.height;
            updateScale();
            tzRender();
        };
        testImg.src = newSrcStr;
    };

    function updateScale() { if(!tzCanvas) return; var r = tzCanvas.getBoundingClientRect(); tzScaleX = tzCanvas.width / r.width; tzScaleY = tzCanvas.height / r.height; }
    function getPos(e) { var r = tzCanvas.getBoundingClientRect(); var cx = (e.touches ? e.touches[0].clientX : e.clientX) - r.left; var cy = (e.touches ? e.touches[0].clientY : e.clientY) - r.top; return { x: cx * tzScaleX, y: cy * tzScaleY }; }
    function zoneAt(x,y) { return zones.slice().reverse().find(function(z) { var hw = (z.size||30)*4; var hh = (z.size||30)*1.4; return x>=z.x-hw && x<=z.x+hw && y>=z.y-hh && y<=z.y+hh; }); }
    function hexToRgb(hex) { hex = hex.replace('#', ''); return { r: parseInt(hex.substring(0,2),16), g: parseInt(hex.substring(2,4),16), b: parseInt(hex.substring(4,6),16) }; }

    window.tzCanvasDown = function(e) {
        e.preventDefault(); updateScale();
        var p = getPos(e); var hit = zoneAt(p.x,p.y);
        if (hit) { selectZone(hit.id); tzDragging=true; tzDragOffX=p.x-hit.x; tzDragOffY=p.y-hit.y; window._tzDragId=hit.id; }
        else { tzAddZoneAt(p.x, p.y); }
    };
    window.tzCanvasMove = function(e) { if (!tzDragging) return; e.preventDefault(); var p = getPos(e); var z = zones.find(function(zz){return zz.id===window._tzDragId;}); if (z) { z.x=p.x-tzDragOffX; z.y=p.y-tzDragOffY; tzRender(); } };
    window.tzCanvasUp = function() { tzDragging=false; };

    function tzRender() {
        if (!tzCtx || !tzImg || tzImg.getAttribute('src') === '') return;
        
        tzCtx.clearRect(0, 0, tzCanvas.width, tzCanvas.height);
        
        if (imageFX.shadowEnabled) {
            tzCtx.save();
            var sc = imageFX.shadowColor || '#000000', sb = imageFX.shadowBlur || 20, ss = imageFX.shadowSpread || 5;
            var sox = imageFX.shadowOffX || 0, soy = imageFX.shadowOffY || 5, so = (imageFX.shadowOpacity || 60) / 100;
            var srgb = hexToRgb(sc);
            tzCtx.shadowColor = 'rgba('+srgb.r+','+srgb.g+','+srgb.b+','+so+')';
            tzCtx.shadowBlur = sb; tzCtx.shadowOffsetX = sox; tzCtx.shadowOffsetY = soy;
            tzCtx.drawImage(tzImg, 0, 0, tzCanvas.width, tzCanvas.height);
            if (ss > 0) { for (var i = 1; i <= Math.ceil(ss/2); i++) { tzCtx.drawImage(tzImg, 0, 0, tzCanvas.width, tzCanvas.height); } }
            tzCtx.shadowColor = 'transparent'; tzCtx.shadowBlur = 0; tzCtx.shadowOffsetX = 0; tzCtx.shadowOffsetY = 0;
            tzCtx.drawImage(tzImg, 0, 0, tzCanvas.width, tzCanvas.height);
            tzCtx.restore();
        } else {
            tzCtx.drawImage(tzImg, 0, 0, tzCanvas.width, tzCanvas.height);
        }
        
        zones.forEach(function(z) {
            var txt = z.defaultText || z.label || 'Text';
            var sz = z.size || 30;
            var w = z.bold ? 'bold' : 'normal';
            var st = z.italic ? 'italic ' : '';
            var al = z.align || 'center';
            tzCtx.save();
            tzCtx.font = st + ' ' + w + ' ' + sz + 'px "' + (z.font||'Arial') + '"';
            var m = tzCtx.measureText(txt);
            var tw = m.width, th = sz * 1.4;
            
            if (z.shadowEnabled) {
                var sc2 = z.shadowColor || '#000000', sb2 = z.shadowBlur || 8;
                var sox2 = z.shadowOffX || 2, soy2 = z.shadowOffY || 2, so2 = (z.shadowOpacity || 80) / 100;
                var srgb2 = hexToRgb(sc2);
                tzCtx.shadowColor = 'rgba('+srgb2.r+','+srgb2.g+','+srgb2.b+','+so2+')';
                tzCtx.shadowBlur = sb2; tzCtx.shadowOffsetX = sox2; tzCtx.shadowOffsetY = soy2;
            } else {
                tzCtx.shadowColor = 'rgba(0,0,0,0.3)'; tzCtx.shadowBlur = 2; tzCtx.shadowOffsetX = 1; tzCtx.shadowOffsetY = 1;
            }
            tzCtx.fillStyle = z.color || '#ffffff';
            tzCtx.textAlign = al; tzCtx.textBaseline = 'middle';
            tzCtx.fillText(txt, z.x, z.y);
            tzCtx.shadowColor = 'transparent'; tzCtx.shadowBlur = 0; tzCtx.shadowOffsetX = 0; tzCtx.shadowOffsetY = 0;

            var bx = al==='center' ? z.x-tw/2-10 : al==='left' ? z.x-10 : z.x-tw-10;
            var by = z.y - th/2 - 6;
            tzCtx.strokeStyle = z.id===selectedZoneId ? '#4e73df' : 'rgba(200,200,200,0.7)';
            tzCtx.lineWidth = z.id===selectedZoneId ? 3 : 1.5;
            tzCtx.setLineDash(z.id===selectedZoneId ? [7,4] : [4,4]);
            tzCtx.strokeRect(bx, by, tw+20, th+12);
            var idx = zones.findIndex(function(zz){return zz.id===z.id;})+1;
            tzCtx.setLineDash([]);
            tzCtx.fillStyle = z.id===selectedZoneId ? '#4e73df':'rgba(0,0,0,0.5)';
            tzCtx.beginPath(); tzCtx.arc(bx+12, by+12, 12, 0, Math.PI * 2); tzCtx.fill();
            tzCtx.fillStyle='#fff'; tzCtx.font='bold 12px Arial'; tzCtx.textAlign='center'; tzCtx.textBaseline='middle';
            tzCtx.fillText(idx, bx+12, by+12);
            tzCtx.restore();
        });
    }

    function tzAddZoneAt(x,y) {
        var z = { id: tzIdCounter++, label: 'Text Zone '+(zones.length+1), defaultText: '', font: 'Arial', size: 40, color: '#ffffff', align: 'center', bold: false, italic: false, maxLen: 0, required: false, shadowEnabled: false, shadowColor: '#000000', shadowBlur: 8, shadowOffX: 2, shadowOffY: 2, shadowOpacity: 80, x: x, y: y };
        zones.push(z); selectZone(z.id); tzRender(); renderZoneList();
    }
    window.tzAddZone = function() { if (!tzCanvas||!tzCanvas.width) { alert('Pehle image save karo!'); return; } tzAddZoneAt(tzCanvas.width/2, tzCanvas.height/3 + zones.length*60); };

    function selectZone(id) {
        selectedZoneId = id; var z = zones.find(function(zz){return zz.id===id;}); if(!z) return;
        document.getElementById('tz-active-controls').style.display='block';
        document.getElementById('tz-label').value = z.label || '';
        document.getElementById('tz-default').value = z.defaultText || '';
        document.getElementById('tz-size').value = z.size || 30;
        document.getElementById('tz-color').value = z.color || '#ffffff';
        document.getElementById('tz-align').value = z.align || 'center';
        document.getElementById('tz-bold').checked = !!z.bold;
        document.getElementById('tz-italic').checked = !!z.italic;
        document.getElementById('tz-maxlen').value = z.maxLen || 0;
        document.getElementById('tz-required').checked = !!z.required;
        document.getElementById('tz-shadow-enabled').checked = !!z.shadowEnabled;
        document.getElementById('tz-shadow-color').value = z.shadowColor || '#000000';
        document.getElementById('tz-shadow-blur').value = z.shadowBlur || 8;
        document.getElementById('tz-shadow-offx').value = z.shadowOffX || 2;
        document.getElementById('tz-shadow-offy').value = z.shadowOffY || 2;
        document.getElementById('tz-shadow-opacity').value = z.shadowOpacity || 80;
        document.getElementById('tz-shadow-opacity-val').textContent = (z.shadowOpacity || 80) + '%';
        document.getElementById('tz-shadow-settings').style.display = z.shadowEnabled ? 'block' : 'none';
        var font = z.font || 'Arial';
        document.getElementById('tz-font').value = font;
        document.getElementById('tz-selected-font').textContent = font + ' ✓';
        document.getElementById('tz-selected-font').style.fontFamily = font;
        document.getElementById('tz-font-search').value = '';
        document.getElementById('tz-font-list').innerHTML = '';
        renderZoneList(); tzRender();
    }

    window.tzUpdateSelected = function(key, val) {
        var z = zones.find(function(zz){return zz.id===selectedZoneId;}); if(!z) return;
        z[key] = val;
        if (key === 'shadowEnabled') document.getElementById('tz-shadow-settings').style.display = val ? 'block' : 'none';
        if (key === 'shadowOpacity') document.getElementById('tz-shadow-opacity-val').textContent = val + '%';
        tzRender(); renderZoneList();
    };
    window.tzDeleteZone = function(id) { if(!confirm('Delete?')) return; zones = zones.filter(function(z){return z.id!==id;}); if(selectedZoneId===id) { selectedZoneId=null; document.getElementById('tz-active-controls').style.display='none'; } tzRender(); renderZoneList(); };

    function renderZoneList() {
        var c = document.getElementById('tz-zone-list');
        if(!zones.length) { c.innerHTML='<p style="font-size:12px;color:#aaa;text-align:center;padding:20px;">Koi zone nahi hai.</p>'; return; }
        c.innerHTML = zones.map(function(z,i){
            return '<div class="tz-zone-card '+(z.id===selectedZoneId?'active-zone':'')+'" onclick="tzSelectZone('+z.id+')"><button class="delete-zone" onclick="event.stopPropagation();tzDeleteZone('+z.id+')">✕</button><div class="zone-label">'+(i+1)+'. '+(z.label||'Zone '+(i+1))+'</div><div style="font-size:11px;color:#888;margin-top:3px;">'+(z.font||'Arial')+' · '+(z.size||30)+'px</div></div>';
        }).join('');
    }
    window.tzSelectZone = function(id) { selectZone(id); };

    window.toggleImageShadow = function(enabled) { imageFX.shadowEnabled = enabled; document.getElementById('fx-shadow-settings').style.display = enabled ? 'block' : 'none'; updateImageFX(); };
    window.updateImageFX = function() {
        if (imageFX.shadowEnabled) {
            imageFX.shadowColor = document.getElementById('fx-shadow-color').value || '#000000';
            imageFX.shadowBlur = parseInt(document.getElementById('fx-shadow-blur').value) || 20;
            imageFX.shadowSpread = parseInt(document.getElementById('fx-shadow-spread').value) || 5;
            imageFX.shadowOffX = parseInt(document.getElementById('fx-shadow-offx').value) || 0;
            imageFX.shadowOffY = parseInt(document.getElementById('fx-shadow-offy').value) || 5;
            imageFX.shadowOpacity = parseInt(document.getElementById('fx-shadow-opacity').value) || 60;
        }
        document.getElementById('fx-shadow-blur-val').textContent = (imageFX.shadowBlur||20) + 'px';
        document.getElementById('fx-shadow-spread-val').textContent = (imageFX.shadowSpread||5) + 'px';
        document.getElementById('fx-shadow-offx-val').textContent = (imageFX.shadowOffX||0) + 'px';
        document.getElementById('fx-shadow-offy-val').textContent = (imageFX.shadowOffY||5) + 'px';
        document.getElementById('fx-shadow-opacity-val').textContent = (imageFX.shadowOpacity||60) + '%';
        document.getElementById('image_fx_input').value = JSON.stringify(imageFX);
        tzRender();
    };
    window.applyFXPreset = function(p) {
        switch(p) {
            case 'soft': imageFX = { shadowEnabled: true, shadowColor: '#000000', shadowBlur: 30, shadowSpread: 10, shadowOffX: 0, shadowOffY: 5, shadowOpacity: 40 }; break;
            case 'dark': imageFX = { shadowEnabled: true, shadowColor: '#000000', shadowBlur: 15, shadowSpread: 20, shadowOffX: 5, shadowOffY: 10, shadowOpacity: 80 }; break;
            case 'colored': imageFX = { shadowEnabled: true, shadowColor: '#4e73df', shadowBlur: 40, shadowSpread: 5, shadowOffX: 0, shadowOffY: 0, shadowOpacity: 60 }; break;
            case 'neon': imageFX = { shadowEnabled: true, shadowColor: '#ff00ff', shadowBlur: 50, shadowSpread: 0, shadowOffX: 0, shadowOffY: 0, shadowOpacity: 90 }; break;
            case 'reset': imageFX = { shadowEnabled: false, shadowColor: '#000000', shadowBlur: 20, shadowSpread: 5, shadowOffX: 0, shadowOffY: 5, shadowOpacity: 60 }; break;
        }
        loadImageFXControls(); document.getElementById('image_fx_input').value = JSON.stringify(imageFX); tzRender();
    };
    window.tzSaveZones = function() {
        document.getElementById('text_zones_input').value = JSON.stringify(zones);
        document.getElementById('image_fx_input').value = JSON.stringify(imageFX);
        var btn = event.target; btn.textContent = '✅ Saved!'; setTimeout(function(){ btn.textContent='💾 Save Zones'; }, 3000);
    };

    var form = document.getElementById('productForm');
    if(form) form.addEventListener('submit', function(){ document.getElementById('text_zones_input').value = JSON.stringify(zones); document.getElementById('image_fx_input').value = JSON.stringify(imageFX); });

    setTimeout(function(){
        var c = document.getElementById('tz-canvas'); if(!c) return;
        c.addEventListener('mousedown', window.tzCanvasDown); c.addEventListener('mousemove', window.tzCanvasMove);
        c.addEventListener('mouseup', window.tzCanvasUp); c.addEventListener('touchstart', window.tzCanvasDown, {passive:false});
        c.addEventListener('touchmove', window.tzCanvasMove, {passive:false}); c.addEventListener('touchend', window.tzCanvasUp);
        window.addEventListener('resize', updateScale);
    }, 500);
})();
</script>
</body>
</html>