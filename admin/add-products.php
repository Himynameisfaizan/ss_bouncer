<?php
session_start();
include "db-conn.php";

$sql = "SELECT * FROM `categories` ORDER BY id DESC";
$check = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Add New Product | Admin Dashboard</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .form-section { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; padding: 25px; border-left: 4px solid #4e73df; }
        .section-title { font-size: 1.1rem; font-weight: 600; color: #4e73df; margin-bottom: 20px; display: flex; align-items: center; }
        .section-title i { margin-right: 10px; font-size: 1.3rem; }
        .preview-image-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .preview-image { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
        .required-field::after { content: " *"; color: #f44336; }
        .nav-tabs .nav-link { color: #555; font-weight: 500; }
        .nav-tabs .nav-link.active { color: #4e73df; border-bottom: 3px solid #4e73df; }
        .font-picker-search { width:100%; padding:8px 12px; border:1.5px solid #4e73df; border-radius:8px 8px 0 0; font-size:13px; outline:none; box-sizing:border-box; }
        .font-picker-list { max-height:200px; overflow-y:auto; border:1.5px solid #4e73df; border-top:none; border-radius:0 0 8px 8px; background:#fff; }
        .font-picker-item { display:flex; align-items:center; justify-content:space-between; padding:8px 12px; cursor:pointer; border-bottom:1px solid #f0f0f0; }
        .font-picker-item:hover { background:#f0f4ff; } .font-picker-item.selected { background:#e8eeff; }
        .font-preview { font-size:15px; flex:1; } .font-check { color:#4e73df; font-weight:bold; font-size:14px; }
        .selected-fonts-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; min-height:32px; padding:6px; background:#f8f9fa; border-radius:8px; border:1px solid #e0e0e0; }
        .font-tag { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; background:#4e73df; color:#fff; border-radius:20px; font-size:12px; }
        .remove-tag { cursor:pointer; font-weight:bold; opacity:.8; } .remove-tag:hover { opacity:1; }
        .picker-btn { padding:4px 10px; font-size:12px; border:1px solid #4e73df; color:#4e73df; background:#fff; border-radius:6px; cursor:pointer; }
        .picker-btn:hover { background:#4e73df; color:#fff; }
        
        /* 🔥 SIZE OPTIONS */
        .size-options-table { width: 100%; border-collapse: collapse; }
        .size-options-table th { background: #f8f9fa; padding: 10px; font-size: 13px; text-align: left; border-bottom: 2px solid #e0e0e0; }
        .size-options-table td { padding: 8px 10px; border-bottom: 1px solid #eee; }
        .size-options-table input { width: 100%; padding: 6px 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; }
        .size-options-table input[type="number"] { width: 100px; }
        .size-options-table input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        .add-size-row { margin-top: 10px; }
        .add-size-row button { padding: 6px 15px; background: #4e73df; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; }
        .add-size-row button:hover { background: #3b5ec0; }
        .remove-size-btn { background: #ef4444; color: #fff; border: none; border-radius: 4px; padding: 4px 10px; cursor: pointer; font-size: 11px; }
        
        /* Text Zones Designer */
        #tz-wrap { display:flex; gap:18px; flex-wrap:wrap; }
        #tz-canvas-col { flex:1; min-width:280px; }
        #tz-panel-col { width:300px; min-width:260px; }
        #tz-canvas-wrap { position:relative; background:#f5f5f5; border:2px dashed #ccc; border-radius:8px; overflow:hidden; cursor:crosshair; }
        #tz-canvas { display:block; width:100%; height:auto; }
        .tz-zone-card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:12px; margin-bottom:10px; position:relative; }
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
        .tz-hint { font-size:11px; color:#aaa; margin-top:6px; line-height:1.5; }
        #tz-no-img { text-align:center; padding:40px 20px; color:#aaa; font-size:13px; }
    </style>
    <?php
    include_once 'google_fonts_list.php';
    $gf_add = array_diff(array_keys($GOOGLE_FONTS), ['Arial','Georgia','Impact','Verdana','Times New Roman','Tahoma','Courier New']);
    $gf_url_add = 'https://fonts.googleapis.com/css2?' . implode('&', array_map(function($f){return 'family='.urlencode($f).':wght@400;700';}, $gf_add)) . '&display=swap';
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?= htmlspecialchars($gf_url_add) ?>" rel="stylesheet">
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>
    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div>
            </div>
        </div>

        <div class="main_content_iner">
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="m-0">Add New Product</h2>
                                        <p class="m-0 text-muted">Fill in the details below to add a new product</p>
                                    </div>
                                </div>
                            </div>

                            <div class="white_card_body">
                                <div class="card-body">
                                    <ul class="nav nav-tabs mb-4" id="productTabs" role="tablist">
                                        <li class="nav-item"><button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button">Basic Info</button></li>
                                        <li class="nav-item"><button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button">Media & SEO</button></li>
                                        <li class="nav-item"><button class="nav-link" id="sizes-tab" data-bs-toggle="tab" data-bs-target="#sizes" type="button">📏 Sizes & Pricing</button></li>
                                        <li class="nav-item"><button class="nav-link" id="editor-tab" data-bs-toggle="tab" data-bs-target="#editor" type="button">🎨 Editor Settings</button></li>
                                        <li class="nav-item"><button class="nav-link" id="zones-tab" data-bs-toggle="tab" data-bs-target="#zones" type="button">✏️ Text Zones</button></li>
                                    </ul>

                                    <form id="productForm" action="functions.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="text_zones" id="text_zones_input" value="[]">
                                        <input type="hidden" name="image_fx" id="image_fx_input" value="{}">
                                        <input type="hidden" name="size_options" id="size_options_input" value="[]">

                                        <div class="tab-content">
                                            <!-- Basic Info Tab -->
                                            <div class="tab-pane fade show active" id="basic">
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-info-circle"></i> Basic Information</div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label required-field">Product Name</label>
                                                            <input type="text" class="form-control" name="pro_name" placeholder="Enter product name" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label required-field">Main Category</label>
                                                            <select class="form-select" name="pro_cate" required onchange="get_subcategory(this.value)">
                                                                <option value="">Select a category</option>
                                                                <?php foreach ($check as $val): ?>
                                                                    <option value="<?= $val['cate_id'] ?>"><?= ucwords($val['categories']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Sub Category</label>
                                                            <select class="form-select" name="pro_sub_cate" id="subcate_id">
                                                                <option value="">Select subcategory (optional)</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Brand Name</label>
                                                            <input type="text" class="form-control" name="brand_name" placeholder="Enter brand name">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Short Description</label>
                                                        <textarea class="form-control" name="short_desc" rows="3" placeholder="Brief description"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Full Description</label>
                                                        <textarea class="form-control" name="description" id="pro_desc" rows="6" placeholder="Detailed product description"></textarea>
                                                    </div>
                                                </div>

                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-dollar-sign"></i> Default Pricing</div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3"><label class="form-label required-field">MRP (₹)</label><input type="number" class="form-control" name="mrp" step="0.01" min="0" required></div>
                                                        <div class="col-md-4 mb-3"><label class="form-label required-field">Selling Price (₹)</label><input type="number" class="form-control" name="selling_price" step="0.01" min="0" required></div>
                                                        <div class="col-md-4 mb-3"><label class="form-label">Stock Quantity</label><input type="number" class="form-control" name="stock" min="0" value="0"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1">Active</option><option value="0">Inactive</option></select></div>
                                                        <div class="col-md-4 mb-3"><label class="form-label">New Arrival</label><select class="form-select" name="new_arrival"><option value="0">No</option><option value="1">Yes</option></select></div>
                                                        <div class="col-md-4 mb-3"><label class="form-label">Trending</label><select class="form-select" name="trending"><option value="0">No</option><option value="1">Yes</option></select></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Media & SEO Tab -->
                                            <div class="tab-pane fade" id="media">
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-images"></i> Product Image</div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Image</label>
                                                        <input type="file" class="form-control" name="pro_img" id="pro_img" accept="image/*">
                                                        <div class="preview-image-container" id="imagePreview"></div>
                                                    </div>
                                                </div>
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-search"></i> SEO Settings</div>
                                                    <div class="mb-3"><label class="form-label">Meta Title</label><input type="text" class="form-control" name="meta_title" maxlength="60" placeholder="SEO title"><small class="text-muted" id="titleCounter">0/60</small></div>
                                                    <div class="mb-3"><label class="form-label">Meta Description</label><textarea class="form-control" name="meta_desc" rows="3" maxlength="160" placeholder="SEO description"></textarea><small class="text-muted" id="descCounter">0/160</small></div>
                                                    <div class="mb-3"><label class="form-label">Meta Keywords</label><input type="text" class="form-control" name="meta_key" placeholder="Comma separated keywords"></div>
                                                    <div class="mb-3"><label class="form-label">Slug URL</label><input type="text" class="form-control" name="slug_url" placeholder="url-friendly-name"><small class="text-muted">Leave empty to auto-generate</small></div>
                                                </div>
                                            </div>

                                            <!-- 🔥 SIZES & PRICING TAB -->
                                            <div class="tab-pane fade" id="sizes">
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-ruler"></i> Size Options & Pricing</div>
                                                    <p class="text-muted mb-3" style="font-size:0.9rem;">Different sizes ke liye alag price set karo. Customer product page pe size select kar sakega.</p>
                                                    
                                                    <table class="size-options-table" id="sizeTable">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:40px;">Enable</th>
                                                                <th>Size Label</th>
                                                                <th style="width:120px;">Price Adjustment (₹)</th>
                                                                <th>Dimensions (optional)</th>
                                                                <th style="width:60px;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="sizeTableBody">
                                                            <!-- Pre-loaded default sizes -->
                                                            <tr>
                                                                <td style="text-align:center;"><input type="checkbox" checked onchange="updateSizeData()"></td>
                                                                <td><input type="text" value="Small" placeholder="Size name"></td>
                                                                <td><input type="number" value="0" step="0.01" placeholder="0"></td>
                                                                <td><input type="text" placeholder="e.g., 10x10 cm"></td>
                                                                <td><button type="button" class="remove-size-btn" onclick="removeSizeRow(this)">✕</button></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="text-align:center;"><input type="checkbox" checked onchange="updateSizeData()"></td>
                                                                <td><input type="text" value="Medium" placeholder="Size name"></td>
                                                                <td><input type="number" value="200" step="0.01" placeholder="0"></td>
                                                                <td><input type="text" placeholder="e.g., 20x20 cm"></td>
                                                                <td><button type="button" class="remove-size-btn" onclick="removeSizeRow(this)">✕</button></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="text-align:center;"><input type="checkbox" checked onchange="updateSizeData()"></td>
                                                                <td><input type="text" value="Large" placeholder="Size name"></td>
                                                                <td><input type="number" value="500" step="0.01" placeholder="0"></td>
                                                                <td><input type="text" placeholder="e.g., 30x30 cm"></td>
                                                                <td><button type="button" class="remove-size-btn" onclick="removeSizeRow(this)">✕</button></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    
                                                    <div class="add-size-row">
                                                        <button type="button" onclick="addSizeRow()">+ Add Size Option</button>
                                                        <small class="text-muted ms-2">Price Adjustment = Base price mein kitna add hoga</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Editor Settings Tab -->
                                            <div class="tab-pane fade" id="editor">
                                                <div class="form-section">
                                                    <div class="section-title"><i class="fas fa-paint-brush"></i> Product Page Editor</div>
                                                    <div class="row g-3">
                                                        <div class="col-md-6"><div class="card border-0 shadow-sm p-3" style="border-left:4px solid #4e73df!important;"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="ec_name_change" id="ec_name_change" value="1" checked><label class="form-check-label fw-bold" for="ec_name_change">✏️ Name / Text Change</label></div></div></div>
                                                        <div class="col-md-6"><div class="card border-0 shadow-sm p-3" style="border-left:4px solid #e74a3b!important;"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="ec_bg_color" id="ec_bg_color" value="1" checked><label class="form-check-label fw-bold" for="ec_bg_color">🎨 Product Color</label></div></div></div>
                                                        <div class="col-md-6"><div class="card border-0 shadow-sm p-3" style="border-left:4px solid #1cc88a!important;"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="ec_brush" id="ec_brush" value="1" checked><label class="form-check-label fw-bold" for="ec_brush">🖌️ Brush Tool</label></div></div></div>
                                                        <div class="col-md-6"><div class="card border-0 shadow-sm p-3" style="border-left:4px solid #f6c23e!important;"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="ec_eraser" id="ec_eraser" value="1" checked><label class="form-check-label fw-bold" for="ec_eraser">🧹 Eraser Tool</label></div></div></div>
                                                        <div class="col-md-6"><div class="card border-0 shadow-sm p-3" style="border-left:4px solid #36b9cc!important;"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="ec_size" id="ec_size" value="1" checked><label class="form-check-label fw-bold" for="ec_size">📐 Size Options</label></div></div></div>
                                                        <div class="col-md-6"><div class="card border-0 shadow-sm p-3" style="border-left:4px solid #858796!important;"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="ec_download" id="ec_download" value="1" checked><label class="form-check-label fw-bold" for="ec_download">📥 Download Button</label></div></div></div>
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="form-label fw-bold">🎨 Preset Colors</label>
                                                        <input type="text" class="form-control" name="ec_preset_colors" value="#ff0000,#ff8c00,#ffd700,#00aa00,#0000ff,#800080">
                                                    </div>
                                                    <div class="mt-3">
                                                        <label class="form-label fw-bold">🏷️ Editor Heading</label>
                                                        <input type="text" class="form-control" name="ec_heading" placeholder="e.g., Customize Your Product">
                                                    </div>
                                                    
                                                    <!-- 🔥 NEW: Design Base Background -->
                                                    <div class="mt-3">
                                                        <label class="form-label fw-bold">🖼️ Default Base Background</label>
                                                        <select class="form-select" name="ec_default_base" style="max-width:250px;">
                                                            <option value="black" selected>⚫ Black</option>
                                                            <option value="white">⚪ White</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- 🔥 NEW: Show/Hide Text Colour Swatches -->
                                                    <div class="mt-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="ec_show_text_colors" id="ec_show_text_colors" value="1" checked>
                                                            <label class="form-check-label fw-bold" for="ec_show_text_colors">🎨 Show Text Colour Swatches</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-4">
                                                        <label class="form-label fw-bold">🔤 Available Fonts</label>
                                                        <div id="ec_fonts_hidden_wrap_add"></div>
                                                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                                            <span style="font-size:12px;color:#888;">Selected: <b id="fontCountBadgeAdd">0</b> fonts</span>
                                                            <div style="display:flex;gap:6px;">
                                                                <button type="button" class="picker-btn" onclick="fpAddSelectAll()">Select All</button>
                                                                <button type="button" class="picker-btn" onclick="fpAddClearAll()">Clear All</button>
                                                            </div>
                                                        </div>
                                                        <div class="selected-fonts-tags" id="selectedFontTagsAdd"></div>
                                                        <div class="font-picker-wrap mt-2">
                                                            <input type="text" class="font-picker-search" id="fontSearchInputAdd" placeholder="🔍 Search fonts...">
                                                            <div class="font-picker-list" id="fontPickerListAdd"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Text Zones Tab -->
                                            <div class="tab-pane fade" id="zones" role="tabpanel">
                                                <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;">
                                                    ⚠️ <b>Pehle image select karo</b> (Media tab mein), phir yahan zones add karo.
                                                </div>
                                                <div id="tz-wrap">
                                                    <div id="tz-canvas-col">
                                                        <p style="font-size:13px;color:#666;margin-bottom:8px;">👆 Canvas pe click karo — nayi text zone add hogi. Drag karke move karo.</p>
                                                        <div id="tz-canvas-wrap">
                                                            <div id="tz-no-img"><i class="fas fa-image" style="font-size:32px;display:block;margin-bottom:8px;"></i>Koi image select nahi hai.</div>
                                                            <canvas id="tz-canvas" style="display:none;"></canvas>
                                                        </div>
                                                    </div>
                                                    <div id="tz-panel-col">
                                                        <button class="tz-btn-add" onclick="tzAddZone()">+ Nayi Text Zone Add Karo</button>
                                                        <div id="tz-active-controls" style="display:none;background:#f8f9ff;border:1px solid #d0d8ff;border-radius:8px;padding:12px;margin-bottom:12px;">
                                                            <div style="font-size:12px;font-weight:600;color:#4e73df;margin-bottom:10px;">✏️ Zone Settings</div>
                                                            <div class="tz-mini-label">Label</div><input type="text" class="tz-input" id="tz-label" placeholder="e.g. Aapka Naam" oninput="tzUpdateSelected('label',this.value)" style="margin-bottom:8px;">
                                                            <div class="tz-mini-label">Default Text</div><input type="text" class="tz-input" id="tz-default" placeholder="e.g. SHARMA" oninput="tzUpdateSelected('defaultText',this.value)" style="margin-bottom:8px;">
                                                            <div class="tz-row">
                                                                <div style="flex:1;"><div class="tz-mini-label">Font</div><select class="tz-select" id="tz-font" onchange="tzUpdateSelected('font',this.value)" style="width:100%;"><?php foreach(array_keys($GOOGLE_FONTS) as $gf): ?><option value="<?= htmlspecialchars($gf) ?>"><?= htmlspecialchars($gf) ?></option><?php endforeach; ?></select></div>
                                                                <div style="width:70px;"><div class="tz-mini-label">Size</div><input type="number" class="tz-input" id="tz-size" value="30" min="10" max="500" oninput="tzUpdateSelected('size',parseInt(this.value)||30)"></div>
                                                            </div>
                                                            <div class="tz-row"><div style="flex:1;"><div class="tz-mini-label">Color</div><input type="color" id="tz-color" value="#ffffff" oninput="tzUpdateSelected('color',this.value)" style="width:100%;height:32px;"></div><div style="flex:1;"><div class="tz-mini-label">Align</div><select class="tz-select" id="tz-align" onchange="tzUpdateSelected('align',this.value)" style="width:100%;"><option value="center">Center</option><option value="left">Left</option><option value="right">Right</option></select></div></div>
                                                            <div class="tz-row"><label style="font-size:12px;display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" id="tz-bold" onchange="tzUpdateSelected('bold',this.checked)"> Bold</label><label style="font-size:12px;display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" id="tz-italic" onchange="tzUpdateSelected('italic',this.checked)"> Italic</label></div>
                                                            <div class="tz-mini-label" style="margin-top:8px;">Max Characters (0=unlimited)</div><input type="number" class="tz-input" id="tz-maxlen" value="0" min="0" max="200" oninput="tzUpdateSelected('maxLen',parseInt(this.value)||0)" style="margin-bottom:8px;">
                                                            <div class="tz-mini-label">Required?</div><label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" id="tz-required" onchange="tzUpdateSelected('required',this.checked)"> Haan</label>
                                                        </div>
                                                        <div id="tz-zone-list" style="max-height:300px;overflow-y:auto;"></div>
                                                        <div style="margin-top:12px;"><button class="tz-btn" onclick="tzSaveZones()" style="width:100%;background:#1cc88a;color:#fff;border-color:#1cc88a;font-weight:600;padding:10px;">💾 Zones Save Karo</button></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Buttons -->
                                        <div class="d-flex justify-content-between mt-4">
                                            <button type="reset" class="btn btn-outline-secondary"><i class="fas fa-undo me-2"></i>Reset Form</button>
                                            <button type="submit" class="btn btn-primary" name="add-product"><i class="fas fa-plus-circle me-2"></i>Add Product</button>
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        CKEDITOR.replace('pro_desc');
        CKEDITOR.replace('short_desc');

        document.getElementById('pro_img').addEventListener('change', function(event) {
            var previewContainer = document.getElementById('imagePreview');
            previewContainer.innerHTML = '';
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('preview-image');
                    previewContainer.appendChild(img);
                    initTzCanvasFromFile(file);
                };
                reader.readAsDataURL(file);
            }
        });

        document.querySelector('input[name="meta_title"]').addEventListener('input', function() {
            document.getElementById('titleCounter').textContent = this.value.length + '/60 characters';
        });
        document.querySelector('textarea[name="meta_desc"]').addEventListener('input', function() {
            document.getElementById('descCounter').textContent = this.value.length + '/160 characters';
        });

        $('input[name="pro_name"]').on('keyup', function() {
            var slug = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            $('input[name="slug_url"]').val(slug);
        });

        function get_subcategory(cate_id) {
            if (!cate_id) { $('#subcate_id').html('<option value="">Select subcategory (optional)</option>'); return; }
            $.ajax({ url: 'functions.php', method: 'post', data: { cate_id: cate_id }, success: function(data) { $("#subcate_id").html('<option value="">Select subcategory (optional)</option>' + data); } });
        }

        document.getElementById('productForm').addEventListener('submit', function(event) {
            var mrp = parseFloat(document.querySelector('input[name="mrp"]').value);
            var sellingPrice = parseFloat(document.querySelector('input[name="selling_price"]').value);
            if (sellingPrice > mrp) { alert('Selling price cannot be higher than MRP'); event.preventDefault(); }
            if (sellingPrice <= 0) { alert('Selling price must be greater than 0'); event.preventDefault(); }
            updateSizeData();
        });

        // 🔥 SIZE OPTIONS
        function updateSizeData() {
            var rows = document.querySelectorAll('#sizeTableBody tr');
            var sizes = [];
            rows.forEach(function(row) {
                var inputs = row.querySelectorAll('input');
                sizes.push({
                    enabled: inputs[0].checked,
                    label: inputs[1].value,
                    price_adj: parseFloat(inputs[2].value) || 0,
                    dimensions: inputs[3].value
                });
            });
            document.getElementById('size_options_input').value = JSON.stringify(sizes);
        }

        function addSizeRow() {
            var tbody = document.getElementById('sizeTableBody');
            var row = document.createElement('tr');
            row.innerHTML = '<td style="text-align:center;"><input type="checkbox" checked onchange="updateSizeData()"></td>' +
                '<td><input type="text" value="Extra Large" placeholder="Size name"></td>' +
                '<td><input type="number" value="1000" step="0.01" placeholder="0"></td>' +
                '<td><input type="text" placeholder="e.g., 40x40 cm"></td>' +
                '<td><button type="button" class="remove-size-btn" onclick="removeSizeRow(this)">✕</button></td>';
            tbody.appendChild(row);
            updateSizeData();
        }

        function removeSizeRow(btn) {
            var row = btn.closest('tr');
            if (document.querySelectorAll('#sizeTableBody tr').length > 1) {
                row.remove();
                updateSizeData();
            } else {
                alert('At least one size option is required!');
            }
        }

        // Initialize size data on load
        document.addEventListener('DOMContentLoaded', function() { updateSizeData(); });
    </script>

    <!-- Font Picker JS -->
    <script>
    (function(){
        var ALL_FONTS_ADD = <?php echo json_encode(array_keys($GOOGLE_FONTS)); ?>;
        var selectedAdd = new Set(ALL_FONTS_ADD);
        function renderAdd(filter){
            filter=(filter||'').toLowerCase();
            var list=document.getElementById('fontPickerListAdd');
            list.innerHTML=ALL_FONTS_ADD.filter(function(f){return f.toLowerCase().indexOf(filter)>-1;}).map(function(f){
                var esc=f.replace(/'/g,"\\'");
                return '<div class="font-picker-item '+(selectedAdd.has(f)?'selected':'')+'" onclick="fpAddToggle(\''+esc+'\')"><span class="font-preview" style="font-family:\''+f+'\';">'+f+'</span>'+(selectedAdd.has(f)?'<span class="font-check">✓</span>':'')+'</div>';
            }).join('');
        }
        function renderTagsAdd(){
            var wrap=document.getElementById('selectedFontTagsAdd'), hidden=document.getElementById('ec_fonts_hidden_wrap_add'), arr=[];
            selectedAdd.forEach(function(f){arr.push(f);});
            wrap.innerHTML=arr.map(function(f){var esc=f.replace(/'/g,"\\'");return '<span class="font-tag" style="font-family:\''+f+'\';">'+f+' <span class="remove-tag" onclick="fpAddToggle(\''+esc+'\')">×</span></span>';}).join('');
            hidden.innerHTML=arr.map(function(f){return '<input type="hidden" name="ec_fonts[]" value="'+f.replace(/"/g,'&quot;')+'">';}).join('');
            document.getElementById('fontCountBadgeAdd').textContent=selectedAdd.size;
        }
        window.fpAddToggle=function(f){selectedAdd.has(f)?selectedAdd.delete(f):selectedAdd.add(f);renderAdd(document.getElementById('fontSearchInputAdd').value);renderTagsAdd();};
        window.fpAddSelectAll=function(){ALL_FONTS_ADD.forEach(function(f){selectedAdd.add(f);});renderAdd(document.getElementById('fontSearchInputAdd').value);renderTagsAdd();};
        window.fpAddClearAll=function(){selectedAdd.clear();renderAdd(document.getElementById('fontSearchInputAdd').value);renderTagsAdd();};
        document.getElementById('fontSearchInputAdd').addEventListener('input',function(){renderAdd(this.value);});
        renderAdd(); renderTagsAdd();
    })();
    </script>

    <!-- Text Zones Designer JS -->
    <script>
    (function() {
        var FONTS = <?php echo json_encode(array_keys($GOOGLE_FONTS)); ?>;
        var zones = [], selectedZoneId = null, tzIdCounter = 1;
        var tzCanvas, tzCtx, tzImg, tzDragging=false, tzDragOffX=0, tzDragOffY=0, tzScaleX=1, tzScaleY=1;

        document.getElementById('zones-tab').addEventListener('click', function() {
            document.querySelectorAll('.tab-pane').forEach(function(p){p.classList.remove('show','active');});
            document.getElementById('zones').classList.add('show','active');
            document.getElementById('zones').style.display='block';
            setTimeout(function() { if(tzImg) { updateScale(); } }, 100);
        });

        window.initTzCanvasFromFile = function(file) {
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function(e) {
                tzCanvas = document.getElementById('tz-canvas'); tzCtx = tzCanvas.getContext('2d'); tzImg = new Image();
                tzImg.onload = function() {
                    document.getElementById('tz-no-img').style.display='none'; tzCanvas.style.display='block';
                    tzCanvas.width = tzImg.naturalWidth; tzCanvas.height = tzImg.naturalHeight;
                    updateScale(); tzRender(); renderZoneList();
                };
                tzImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        };

        function updateScale() { var r = tzCanvas.getBoundingClientRect(); tzScaleX = tzCanvas.width/r.width; tzScaleY = tzCanvas.height/r.height; }
        function getPos(e) { var r = tzCanvas.getBoundingClientRect(); var cx = (e.touches?e.touches[0].clientX:e.clientX)-r.left; var cy = (e.touches?e.touches[0].clientY:e.clientY)-r.top; return {x:cx*tzScaleX, y:cy*tzScaleY}; }
        function zoneAt(x,y) { return zones.slice().reverse().find(function(z){var hw=(z.size||30)*4, hh=(z.size||30)*1.4; return x>=z.x-hw&&x<=z.x+hw&&y>=z.y-hh&&y<=z.y+hh;}); }

        window.tzCanvasDown = function(e) { e.preventDefault(); updateScale(); var p=getPos(e), hit=zoneAt(p.x,p.y); if(hit){selectZone(hit.id);tzDragging=true;tzDragOffX=p.x-hit.x;tzDragOffY=p.y-hit.y;window._tzDragId=hit.id;}else{tzAddZoneAt(p.x,p.y);} };
        window.tzCanvasMove = function(e) { if(!tzDragging)return; e.preventDefault(); var p=getPos(e), z=zones.find(function(zz){return zz.id===window._tzDragId;}); if(z){z.x=p.x-tzDragOffX;z.y=p.y-tzDragOffY;tzRender();} };
        window.tzCanvasUp = function() { tzDragging=false; };

        function tzRender() {
            if(!tzCtx||!tzImg)return; tzCtx.clearRect(0,0,tzCanvas.width,tzCanvas.height); tzCtx.drawImage(tzImg,0,0,tzCanvas.width,tzCanvas.height);
            zones.forEach(function(z){var t=z.defaultText||z.label||'Text',sz=z.size||30,w=z.bold?'bold':'normal',st=z.italic?'italic ':'',al=z.align||'center';tzCtx.save();tzCtx.font=st+' '+w+' '+sz+'px "'+(z.font||'Arial')+'"';tzCtx.shadowColor='rgba(0,0,0,0.5)';tzCtx.shadowBlur=3;tzCtx.fillStyle=z.color||'#ffffff';tzCtx.textAlign=al;tzCtx.textBaseline='middle';tzCtx.fillText(t,z.x,z.y);tzCtx.shadowColor='transparent';tzCtx.shadowBlur=0;var m=tzCtx.measureText(t),tw=m.width,th=sz*1.4,bx=al==='center'?z.x-tw/2-10:al==='left'?z.x-10:z.x-tw-10,by=z.y-th/2-6;tzCtx.strokeStyle=z.id===selectedZoneId?'#4e73df':'rgba(200,200,200,0.7)';tzCtx.lineWidth=z.id===selectedZoneId?3:1.5;tzCtx.setLineDash(z.id===selectedZoneId?[7,4]:[4,4]);tzCtx.strokeRect(bx,by,tw+20,th+12);tzCtx.setLineDash([]);tzCtx.restore();});
        }

        function tzAddZoneAt(x,y) { var z={id:tzIdCounter++,label:'Text Zone '+(zones.length+1),defaultText:'',font:'Arial',size:40,color:'#ffffff',align:'center',bold:false,italic:false,maxLen:0,required:false,x:x,y:y}; zones.push(z); selectZone(z.id); tzRender(); renderZoneList(); }
        window.tzAddZone = function() { if(!tzCanvas||!tzCanvas.width){alert('Pehle Media tab mein image upload karo!');return;} tzAddZoneAt(tzCanvas.width/2,tzCanvas.height/3+zones.length*60); };

        function selectZone(id) { selectedZoneId=id; var z=zones.find(function(zz){return zz.id===id;}); if(!z)return; document.getElementById('tz-active-controls').style.display='block'; document.getElementById('tz-label').value=z.label||''; document.getElementById('tz-default').value=z.defaultText||''; document.getElementById('tz-size').value=z.size||30; document.getElementById('tz-color').value=z.color||'#ffffff'; document.getElementById('tz-align').value=z.align||'center'; document.getElementById('tz-bold').checked=!!z.bold; document.getElementById('tz-italic').checked=!!z.italic; document.getElementById('tz-maxlen').value=z.maxLen||0; document.getElementById('tz-required').checked=!!z.required; var fs=document.getElementById('tz-font'); for(var i=0;i<fs.options.length;i++){if(fs.options[i].value===(z.font||'Arial')){fs.selectedIndex=i;break;}} renderZoneList(); tzRender(); }

        window.tzUpdateSelected = function(key,val) { var z=zones.find(function(zz){return zz.id===selectedZoneId;}); if(!z)return; z[key]=val; tzRender(); renderZoneList(); };
        window.tzDeleteZone = function(id) { if(!confirm('Delete?'))return; zones=zones.filter(function(z){return z.id!==id;}); if(selectedZoneId===id){selectedZoneId=null;document.getElementById('tz-active-controls').style.display='none';} tzRender(); renderZoneList(); };

        function renderZoneList() { var c=document.getElementById('tz-zone-list'); if(!zones.length){c.innerHTML='<p style="font-size:12px;color:#aaa;text-align:center;padding:20px;">Koi zone nahi hai.</p>';return;} c.innerHTML=zones.map(function(z,i){return '<div class="tz-zone-card '+(z.id===selectedZoneId?'active-zone':'')+'" onclick="tzSelectZone('+z.id+')"><button class="delete-zone" onclick="event.stopPropagation();tzDeleteZone('+z.id+')">✕</button><div class="zone-label">'+(i+1)+'. '+(z.label||'Zone '+(i+1))+'</div><div style="font-size:11px;color:#888;">'+(z.font||'Arial')+' · '+(z.size||30)+'px</div></div>';}).join(''); }
        window.tzSelectZone = function(id) { selectZone(id); };

        window.tzSaveZones = function() { document.getElementById('text_zones_input').value = JSON.stringify(zones); var btn=event.target; btn.textContent='✅ Saved!'; setTimeout(function(){btn.textContent='💾 Zones Save Karo';},3000); };
        var form=document.getElementById('productForm'); if(form)form.addEventListener('submit',function(){document.getElementById('text_zones_input').value=JSON.stringify(zones);});

        setTimeout(function(){var c=document.getElementById('tz-canvas');if(!c)return;c.addEventListener('mousedown',window.tzCanvasDown);c.addEventListener('mousemove',window.tzCanvasMove);c.addEventListener('mouseup',window.tzCanvasUp);c.addEventListener('touchstart',window.tzCanvasDown,{passive:false});c.addEventListener('touchmove',window.tzCanvasMove,{passive:false});c.addEventListener('touchend',window.tzCanvasUp);window.addEventListener('resize',updateScale);},500);
    })();
    </script>
</body>
</html>