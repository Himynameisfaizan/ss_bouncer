<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db-conn.php";

// ==================== ADD CATEGORY ====================
if (isset($_POST["add-categories"])) {
    $cate_name = mysqli_real_escape_string($conn, $_POST['cate_name']);
    $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title'] ?? '');
    $meta_desc = mysqli_real_escape_string($conn, $_POST['meta_desc'] ?? '');
    $meta_key = mysqli_real_escape_string($conn, $_POST['meta_key'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '1');
    $added_on = date('Y-m-d H:i:s');

    $cate_id = mt_rand(11111, 99999);
    $slug_url = strtolower(str_replace(" ", "-", $cate_name));
    $slug_url = preg_replace('/[^a-z0-9-]/', '', $slug_url);

    $image_name = '';
    $folder = 'assets/img/category/';
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] == 0) {
        $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['imageUpload']['name']);
        $destination = $folder . $filename;
        if (move_uploaded_file($_FILES['imageUpload']['tmp_name'], $destination)) {
            $image_name = $filename;
        }
    }

    $sql = "INSERT INTO `categories` (`cate_id`, `categories`, `slug_url`, `meta_title`, `meta_desc`, `meta_key`, `image`, `status`, `added_on`) 
            VALUES ('$cate_id', '$cate_name', '$slug_url', '$meta_title', '$meta_desc', '$meta_key', '$image_name', '$status', '$added_on')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Category Added Successfully!'); window.location.href='view-categories.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// ==================== ADD SUB CATEGORY ====================
if (isset($_POST["add-sub-categories"])) {
    $parent_id = mysqli_real_escape_string($conn, $_POST['parent_id']);
    $cate_name = mysqli_real_escape_string($conn, $_POST['cate_name']);
    $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title'] ?? '');
    $meta_desc = mysqli_real_escape_string($conn, $_POST['meta_desc'] ?? '');
    $meta_key = mysqli_real_escape_string($conn, $_POST['meta_key'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '1');
    $added_on = date('Y-m-d H:i:s');

    $slug_url = strtolower(str_replace(" ", "-", $cate_name));
    $slug_url = preg_replace('/[^a-z0-9-]/', '', $slug_url);

    $image_name = '';
    $folder = 'assets/img/subcategory/';
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] == 0) {
        $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['imageUpload']['name']);
        $destination = $folder . $filename;
        if (move_uploaded_file($_FILES['imageUpload']['tmp_name'], $destination)) {
            $image_name = $filename;
        }
    }

    $sql = "INSERT INTO `sub_categories` (`parent_id`, `categories`, `slug_url`, `meta_title`, `meta_desc`, `meta_key`, `sub_cat_img`, `status`, `added_on`) 
            VALUES ('$parent_id', '$cate_name', '$slug_url', '$meta_title', '$meta_desc', '$meta_key', '$image_name', '$status', '$added_on')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Sub Category Added Successfully!'); window.location.href='view-sub-categories.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// ==================== ADD PRODUCT ====================
if (isset($_POST["add-product"])) {
    $pro_id = mt_rand(11111, 99999);

    $pro_name = mysqli_real_escape_string($conn, $_POST['pro_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $pro_sub_cate = mysqli_real_escape_string($conn, $_POST['pro_sub_cate'] ?? '');
    $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name'] ?? '');
    $short_desc = mysqli_real_escape_string($conn, $_POST['short_desc'] ?? '');

    $mrp = mysqli_real_escape_string($conn, $_POST['mrp']);
    $selling_price = mysqli_real_escape_string($conn, $_POST['selling_price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock'] ?? 0);

    $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title'] ?? '');
    $meta_desc = mysqli_real_escape_string($conn, $_POST['meta_desc'] ?? '');
    $meta_key = mysqli_real_escape_string($conn, $_POST["meta_key"] ?? '');

    $slug_url = strtolower(str_replace(" ", "-", $pro_name));
    $slug_url = getUniqueSlug($conn, $slug_url);

    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '1');
    $new_arrival = mysqli_real_escape_string($conn, $_POST['new_arrival'] ?? '0');
    $trending = mysqli_real_escape_string($conn, $_POST['trending'] ?? '0');
    $added_on = date('Y-m-d H:i:s');

    // ==================== EDITOR CONFIG (UPDATED) ====================
    $editor_config = [
        'name_change'       => !empty($_POST['ec_name_change']) ? 1 : 0,
        'bg_color'          => !empty($_POST['ec_bg_color']) ? 1 : 0,
        'brush'             => !empty($_POST['ec_brush']) ? 1 : 0,
        'eraser'            => !empty($_POST['ec_eraser']) ? 1 : 0,
        'size'              => !empty($_POST['ec_size']) ? 1 : 0,
        'download'          => !empty($_POST['ec_download']) ? 1 : 0,
        'preset_colors'     => $_POST['ec_preset_colors'] ?? '#ff0000,#ff8c00,#ffd700,#00aa00,#0000ff,#800080',
        'heading'           => $_POST['ec_heading'] ?? '',
        'fonts'             => !empty($_POST['ec_fonts']) ? array_map('strip_tags', (array)$_POST['ec_fonts']) : ['Arial', 'Times New Roman', 'Georgia', 'Courier New', 'Verdana', 'Impact', 'Trebuchet MS', 'Tahoma', 'Palatino', 'Garamond', 'Comic Sans MS', 'Lucida Console'],
        // 🔥 NEW FIELDS
        'default_base'      => $_POST['ec_default_base'] ?? 'black',
        'show_text_colors'  => !empty($_POST['ec_show_text_colors']) ? 1 : 0,
    ];
    $editor_config_json = mysqli_real_escape_string($conn, json_encode($editor_config));

    $has_customization = (!empty($editor_config['name_change']) || !empty($editor_config['bg_color']) ||
        !empty($editor_config['brush']) || !empty($editor_config['eraser']) ||
        !empty($editor_config['size']) || !empty($editor_config['download'])) ? 1 : 0;

    $raw_zones = isset($_POST['text_zones']) ? $_POST['text_zones'] : '[]';
    $zones_array = json_decode(html_entity_decode($raw_zones), true);
    if (!is_array($zones_array)) {
        $zones_array = [];
    }
    $text_zones_json = mysqli_real_escape_string($conn, json_encode($zones_array));

    $raw_image_fx = isset($_POST['image_fx']) ? $_POST['image_fx'] : '{}';
    $image_fx_array = json_decode(html_entity_decode($raw_image_fx), true);
    if (!is_array($image_fx_array)) {
        $image_fx_array = [];
    }
    $image_fx_json = mysqli_real_escape_string($conn, json_encode($image_fx_array));

    // 🔥 SIZE OPTIONS
    $raw_sizes = isset($_POST['size_options']) ? $_POST['size_options'] : '[]';
    $size_array = json_decode(html_entity_decode($raw_sizes), true);
    if (!is_array($size_array)) {
        $size_array = [];
    }
    $size_json = mysqli_real_escape_string($conn, json_encode($size_array));

    $pro_img = '';
    $folder = 'assets/img/uploads/';
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    if (isset($_FILES['pro_img']) && $_FILES['pro_img']['error'] == 0) {
        $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['pro_img']['name']);
        $destination = $folder . $filename;
        if (move_uploaded_file($_FILES['pro_img']['tmp_name'], $destination)) {
            $pro_img = $filename;
        }
    }

    // 🔥 INSERT with size_options
    $sql = "INSERT INTO `products` (
        `pro_id`, `pro_name`, `brand_name`, `description`, `short_desc`, `pro_sub_cate`, 
        `mrp`, `selling_price`, `stock`, `pro_img`, 
        `meta_title`, `meta_desc`, `meta_key`, 
        `slug_url`, `status`, `new_arrival`, `trending`, `has_customization`,
        `added_on`, `editor_config`, `text_zones`, `image_fx`, `size_options`
    ) VALUES (
        '$pro_id', '$pro_name', '$brand_name', '$description', '$short_desc', '$pro_sub_cate',
        '$mrp', '$selling_price', '$stock', '$pro_img',
        '$meta_title', '$meta_desc', '$meta_key',
        '$slug_url', '$status', '$new_arrival', '$trending', '$has_customization',
        '$added_on', '$editor_config_json', '$text_zones_json', '$image_fx_json', '$size_json'
    )";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Product Added Successfully!'); window.location.href='show-products.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// ==================== UPDATE PRODUCT ====================
if (isset($_POST["update-product"])) {
    $pro_id = mysqli_real_escape_string($conn, $_POST['pro_id']);

    $pro_name = mysqli_real_escape_string($conn, $_POST['pro_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $pro_sub_cate = mysqli_real_escape_string($conn, $_POST['pro_sub_cate'] ?? '');
    $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name'] ?? '');
    $short_desc = mysqli_real_escape_string($conn, $_POST['short_desc'] ?? '');

    $mrp = mysqli_real_escape_string($conn, $_POST['mrp']);
    $selling_price = mysqli_real_escape_string($conn, $_POST['selling_price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock'] ?? 0);

    $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title'] ?? '');
    $meta_desc = mysqli_real_escape_string($conn, $_POST['meta_desc'] ?? '');
    $meta_key = mysqli_real_escape_string($conn, $_POST["meta_key"] ?? '');

    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '1');
    $new_arrival = mysqli_real_escape_string($conn, $_POST['new_arrival'] ?? '0');
    $trending = mysqli_real_escape_string($conn, $_POST['trending'] ?? '0');

    if (!empty($_POST['slug_url'])) {
        $slug_url = mysqli_real_escape_string($conn, $_POST['slug_url']);
    }

    // ==================== EDITOR CONFIG (UPDATED) ====================
    $editor_config = [
        'name_change'       => !empty($_POST['ec_name_change']) ? 1 : 0,
        'bg_color'          => !empty($_POST['ec_bg_color']) ? 1 : 0,
        'brush'             => !empty($_POST['ec_brush']) ? 1 : 0,
        'eraser'            => !empty($_POST['ec_eraser']) ? 1 : 0,
        'size'              => !empty($_POST['ec_size']) ? 1 : 0,
        'download'          => !empty($_POST['ec_download']) ? 1 : 0,
        'preset_colors'     => $_POST['ec_preset_colors'] ?? '#ff0000,#ff8c00,#ffd700,#00aa00,#0000ff,#800080',
        'heading'           => $_POST['ec_heading'] ?? '',
        'fonts'             => !empty($_POST['ec_fonts']) ? array_map('strip_tags', (array)$_POST['ec_fonts']) : ['Arial', 'Times New Roman', 'Georgia', 'Courier New', 'Verdana', 'Impact', 'Trebuchet MS', 'Tahoma', 'Palatino', 'Garamond', 'Comic Sans MS', 'Lucida Console'],
        // 🔥 NEW FIELDS
        'default_base'      => $_POST['ec_default_base'] ?? 'black',
        'show_text_colors'  => !empty($_POST['ec_show_text_colors']) ? 1 : 0,
    ];
    $editor_config_json = mysqli_real_escape_string($conn, json_encode($editor_config));

    $has_customization = (!empty($editor_config['name_change']) || !empty($editor_config['bg_color']) ||
        !empty($editor_config['brush']) || !empty($editor_config['eraser']) ||
        !empty($editor_config['size']) || !empty($editor_config['download'])) ? 1 : 0;

    $raw_zones = isset($_POST['text_zones']) ? $_POST['text_zones'] : '[]';
    $zones_array = json_decode(html_entity_decode($raw_zones), true);
    if (!is_array($zones_array)) {
        $zones_array = [];
    }
    $text_zones_json = mysqli_real_escape_string($conn, json_encode($zones_array));

    $raw_image_fx = isset($_POST['image_fx']) ? $_POST['image_fx'] : '{}';
    $image_fx_array = json_decode(html_entity_decode($raw_image_fx), true);
    if (!is_array($image_fx_array)) {
        $image_fx_array = [];
    }
    $image_fx_json = mysqli_real_escape_string($conn, json_encode($image_fx_array));

    // 🔥 SIZE OPTIONS
    $raw_sizes = isset($_POST['size_options']) ? $_POST['size_options'] : '[]';
    $size_array = json_decode(html_entity_decode($raw_sizes), true);
    if (!is_array($size_array)) {
        $size_array = [];
    }
    $size_json = mysqli_real_escape_string($conn, json_encode($size_array));

    $get_img_query = "SELECT pro_img FROM products WHERE pro_id = '$pro_id'";
    $get_img_result = mysqli_query($conn, $get_img_query);
    $img_data = mysqli_fetch_assoc($get_img_result);
    $existing_img = $img_data['pro_img'] ?? '';

    $pro_img = $existing_img;
    $folder = 'assets/img/uploads/';
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    if (isset($_FILES['pro_img']) && $_FILES['pro_img']['error'] == 0) {
        if (!empty($existing_img) && file_exists($folder . $existing_img)) {
            unlink($folder . $existing_img);
        }
        $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['pro_img']['name']);
        $destination = $folder . $filename;
        if (move_uploaded_file($_FILES['pro_img']['tmp_name'], $destination)) {
            $pro_img = $filename;
        }
    }

    if (!empty($_POST['removed_images'])) {
        $removed = explode(',', $_POST['removed_images']);
        foreach ($removed as $rimg) {
            if (!empty($rimg) && file_exists($folder . $rimg)) {
                unlink($folder . $rimg);
            }
        }
    }

    // 🔥 UPDATE with size_options
    $update_sql = "UPDATE `products` SET 
        `pro_name` = '$pro_name',
        `brand_name` = '$brand_name',
        `description` = '$description',
        `short_desc` = '$short_desc',
        `pro_sub_cate` = '$pro_sub_cate',
        `mrp` = '$mrp',
        `selling_price` = '$selling_price',
        `stock` = '$stock',
        `pro_img` = '$pro_img',
        `meta_title` = '$meta_title',
        `meta_desc` = '$meta_desc',
        `meta_key` = '$meta_key',
        `status` = '$status',
        `new_arrival` = '$new_arrival',
        `trending` = '$trending',
        `has_customization` = '$has_customization',
        `editor_config` = '$editor_config_json',
        `text_zones` = '$text_zones_json',
        `image_fx` = '$image_fx_json',
        `size_options` = '$size_json'";

    if (isset($slug_url)) {
        $update_sql .= ", `slug_url` = '$slug_url'";
    }
    $update_sql .= " WHERE `pro_id` = '$pro_id'";

    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Product Updated Successfully!'); window.location.href='show-products.php';</script>";
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}

// ==================== HELPER FUNCTIONS ====================
function getUniqueSlug($conn, $slug)
{
    $original_slug = $slug;
    $counter = 1;
    $query = "SELECT id FROM products WHERE slug_url = '$slug'";
    $result = mysqli_query($conn, $query);
    while (mysqli_num_rows($result) > 0) {
        $slug = $original_slug . '-' . $counter;
        $query = "SELECT id FROM products WHERE slug_url = '$slug'";
        $result = mysqli_query($conn, $query);
        $counter++;
    }
    return $slug;
}

// ==================== DISPLAY PRODUCTS FUNCTION ====================
function get_Products()
{
    global $conn;
    $sql = "SELECT * FROM `products` ORDER BY id DESC";
    $check = mysqli_query($conn, $sql);
    $sno = 1;
    while ($result = mysqli_fetch_assoc($check)) {
        $status = $result['status'] == '1' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
        $added_on = date('d M Y', strtotime($result['added_on']));
        $custom_badge = (!empty($result['has_customization']) && $result['has_customization'] == 1) ? '<span class="badge bg-purple ms-1" style="background:#667eea;">Custom</span>' : '';
        $img_html = '<div class="bg-light rounded p-1 text-center" style="width:60px;height:60px;"><i class="fas fa-image fs-3 text-muted"></i></div>';
        if (!empty($result['pro_img']) && file_exists('assets/img/uploads/' . $result['pro_img'])) {
            $img_html = '<img src="assets/img/uploads/' . $result['pro_img'] . '" style="width:60px;height:60px;object-fit:cover;" class="rounded">';
        }
        echo "<tr><td class='text-center'>" . $sno++ . "</td><td>" . $img_html . "</td><td><div class='fw-semibold'>" . htmlspecialchars($result['pro_name']) . $custom_badge . "</div><small class='text-muted'>ID: " . $result['pro_id'] . "</small></td><td><div>₹" . number_format($result['selling_price'], 2) . "</div><small class='text-decoration-line-through text-muted'>₹" . number_format($result['mrp'], 2) . "</small></td><td class='text-center'>" . $status . "</td><td class='text-center'>" . $added_on . "</td><td class='text-center'><div class='btn-group'><a href='edit-product.php?edit_product_details=" . $result['pro_id'] . "' class='btn btn-sm btn-outline-primary'><i class='fas fa-edit'></i></a><a href='delete-product.php?id=" . $result['pro_id'] . "' onclick='return confirm(\"Delete?\")' class='btn btn-sm btn-outline-danger'><i class='fas fa-trash'></i></a></div></td></tr>";
    }
}

// ==================== AJAX HANDLERS ====================
if (isset($_POST['cate_id'])) {
    $p_id = mysqli_real_escape_string($conn, $_POST['cate_id']);
    $sql = "SELECT * FROM `sub_categories` WHERE `parent_id` = '$p_id' ORDER BY categories ASC";
    $check = mysqli_query($conn, $sql);
    while ($result = mysqli_fetch_assoc($check)) {
        echo "<option value='" . $result['id'] . "'>" . htmlspecialchars($result['categories']) . "</option>";
    }
}

function get_Sub_Category()
{
    global $conn;
    $sql = "SELECT sc.*, c.categories AS parent_name FROM sub_categories sc LEFT JOIN categories c ON sc.parent_id = c.cate_id ORDER BY sc.id DESC";
    $check = mysqli_query($conn, $sql);
    $sno = 1;
    while ($result = mysqli_fetch_assoc($check)) {
        $status = $result['status'] == '1' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
        $added_on = !empty($result['added_on']) ? date('d M Y', strtotime($result['added_on'])) : '—';
        echo "<tr><td class='text-center'>" . $sno++ . "</td><td>" . htmlspecialchars($result['categories']) . "</td><td>" . htmlspecialchars($result['parent_name'] ?? 'None') . "</td><td><span class='text-muted'>" . htmlspecialchars($result['slug_url'] ?? '') . "</span></td><td class='text-center'>" . $status . "</td><td class='text-center'>" . $added_on . "</td><td class='text-center'><a href='edit_sub_category.php?id=" . $result['cate_id'] . "' class='btn btn-sm btn-outline-primary'><i class='fas fa-edit'></i></a><a href='view-sub-categories.php?id=" . $result['cate_id'] . "' onclick='return confirm(\"Delete?\")' class='btn btn-sm btn-outline-danger'><i class='fas fa-trash'></i></a></td></tr>";
    }
}

function get_Category()
{
    global $conn;
    $sql = "SELECT * FROM `categories` ORDER BY id DESC";
    $check = mysqli_query($conn, $sql);
    $sno = 1;
    while ($result = mysqli_fetch_assoc($check)) {
        $status = $result['status'] == '1' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
        $added_on = !empty($result['added_on']) ? date('d M Y', strtotime($result['added_on'])) : '—';
        echo "<tr><td class='text-center'>" . $sno++ . "</td><td class='text-center'><span class='badge bg-light text-dark'>" . $result['cate_id'] . "</span></td><td><div class='fw-semibold'>" . htmlspecialchars($result['categories']) . "</div></td><td><span class='text-muted'>" . htmlspecialchars($result['slug_url'] ?? '') . "</span></td><td class='text-center'>" . $status . "</td><td class='text-center'>" . $added_on . "</td><td class='text-center'><a href='edit_category.php?id=" . $result['cate_id'] . "' class='btn btn-sm btn-outline-primary'><i class='fas fa-edit'></i></a><a href='delete-category.php?id=" . $result['cate_id'] . "' onclick='return confirm(\"Delete?\")' class='btn btn-sm btn-outline-danger'><i class='fas fa-trash'></i></a></td><td class='text-center'><small class='text-muted'>" . $added_on . "</small></td></tr>";
    }
}

function get_category_by_id($cate_id)
{
    global $conn;
    $cate_id = mysqli_real_escape_string($conn, $cate_id);
    $sql = "SELECT * FROM `categories` WHERE `cate_id` = '$cate_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    return ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result) : null;
}

function update_category($cate_id, $cate_name, $slug_url, $status)
{
    global $conn;
    $cate_id = mysqli_real_escape_string($conn, $cate_id);
    $cate_name = mysqli_real_escape_string($conn, $cate_name);
    $slug_url = mysqli_real_escape_string($conn, $slug_url);
    $status = mysqli_real_escape_string($conn, $status);
    $sql = "UPDATE `categories` SET `categories`='$cate_name', `slug_url`='$slug_url', `status`='$status' WHERE `cate_id`='$cate_id'";
    return mysqli_query($conn, $sql);
}

// Note: Upar agar db-conn.php include nahi hai toh kar lena
// include "db-conn.php"; 

// ==========================================
// TESTIMONIAL ADD / UPDATE BACKEND LOGIC
// ==========================================
if (isset($_POST['action']) && ($_POST['action'] === 'add-testimonial' || $_POST['action'] === 'update-testimonial')) {

    // Hamesha JSON return karo kyunki frontend AJAX use kar raha hai
    header('Content-Type: application/json');

    // 1. Get and Sanitize Data
    $client_name = trim($_POST['client_name']);
    $client_title = trim($_POST['client_title'] ?? '');
    $client_company = trim($_POST['client_company'] ?? '');
    $project_name = trim($_POST['project_name'] ?? '');
    $testimonial_text = trim($_POST['testimonial_text']);
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    $display_order = isset($_POST['display_order']) && $_POST['display_order'] !== '' ? (int)$_POST['display_order'] : 0;

    // Checkbox Fix: Agar checked nahi hai toh 0, warna 1
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Date Fix: Agar empty hai toh NULL set karo
    $project_date = !empty($_POST['project_date']) ? $_POST['project_date'] : NULL;

    // Basic Validation
    if (empty($client_name) || empty($testimonial_text)) {
        echo json_encode(['status' => 'error', 'message' => 'Client Name and Testimonial text are required!']);
        exit;
    }

    // 2. Image Upload Logic (Standard Path)
    $client_photo = null;
    $uploadDir = "assets/img/uploads/";

    if (isset($_FILES['client_photo']) && $_FILES['client_photo']['error'] === UPLOAD_ERR_OK) {
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExt = strtolower(pathinfo($_FILES['client_photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExt, $allowed)) {
            $newFileName = time() . '_client.' . $fileExt;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['client_photo']['tmp_name'], $uploadPath)) {
                $client_photo = $newFileName;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload photo. Check folder permissions.']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid image format. Only JPG, PNG, WEBP allowed.']);
            exit;
        }
    }

    // 3. Database Execution
    if ($_POST['action'] === 'add-testimonial') {

        // INSERT QUERY
        $sql = "INSERT INTO testimonials (client_name, client_title, client_company, client_photo, testimonial_text, rating, project_name, project_date, featured, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        // Types: s(string), s, s, s, s, i(int), s, s, i, i
        $stmt->bind_param("sssssissii", $client_name, $client_title, $client_company, $client_photo, $testimonial_text, $rating, $project_name, $project_date, $featured, $display_order);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Testimonial added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $stmt->error]);
        }
    } elseif ($_POST['action'] === 'update-testimonial') {

        // UPDATE QUERY
        $id = (int)$_POST['testimonial_id'];

        if ($client_photo !== null) {
            // New image uploaded
            $sql = "UPDATE testimonials SET client_name=?, client_title=?, client_company=?, client_photo=?, testimonial_text=?, rating=?, project_name=?, project_date=?, featured=?, display_order=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssissiii", $client_name, $client_title, $client_company, $client_photo, $testimonial_text, $rating, $project_name, $project_date, $featured, $display_order, $id);
        } else {
            // No new image, keep old image
            $sql = "UPDATE testimonials SET client_name=?, client_title=?, client_company=?, testimonial_text=?, rating=?, project_name=?, project_date=?, featured=?, display_order=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssissiii", $client_name, $client_title, $client_company, $testimonial_text, $rating, $project_name, $project_date, $featured, $display_order, $id);
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Testimonial updated successfully!', 'testimonial_id' => $id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $stmt->error]);
        }
    }

    // Very important: Exit script after JSON response so no HTML mixes with it
    if (isset($stmt)) $stmt->close();
    exit;
}

// ==========================================
// YAHAN TUMHARA BAAKI FUNCTIONS KA CODE HOGA
// ==========================================
?>