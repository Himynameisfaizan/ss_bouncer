<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "db-conn.php";

// Initialize variables
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['sections'])) {
            foreach ($_POST['sections'] as $section_id => $section_data) {
                $title = trim($section_data['title'] ?? '');
                $content = trim($section_data['content'] ?? '');
                $section_order = intval($section_data['order'] ?? 0);

                if (empty($title) || empty($content)) {
                    throw new Exception("Title and content are required for all sections.");
                }

                // Default to existing image
                $image_path = $section_data['current_image'] ?? '';

                // =========================================================
                // 100% ERROR-PROOF IMAGE UPLOAD LOGIC
                // =========================================================
                if (
                    isset($_FILES['sections']['name'][$section_id]['image']) &&
                    !empty($_FILES['sections']['name'][$section_id]['image'])
                ) {

                    $file_tmp   = $_FILES['sections']['tmp_name'][$section_id]['image'];
                    $file_error = $_FILES['sections']['error'][$section_id]['image'];
                    $file_size  = $_FILES['sections']['size'][$section_id]['image'];

                    // Check PHP upload errors
                    if ($file_error !== UPLOAD_ERR_OK) {
                        $error_types = [
                            UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the upload_max_filesize limit in php.ini.",
                            UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the MAX_FILE_SIZE directive in HTML form.",
                            UPLOAD_ERR_PARTIAL    => "The uploaded file was only partially uploaded.",
                            UPLOAD_ERR_NO_FILE    => "No file was uploaded.",
                            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder on server.",
                            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk. Check folder permissions.",
                            UPLOAD_ERR_EXTENSION  => "A PHP extension stopped the file upload."
                        ];
                        $msg = $error_types[$file_error] ?? "Unknown upload error.";
                        throw new Exception("Image upload error in Section #{$section_order}: " . $msg);
                    }

                    // Check directory
                    $upload_dir = 'uploads/about_us/';
                    if (!is_dir($upload_dir)) {
                        if (!mkdir($upload_dir, 0777, true)) {
                            throw new Exception("Failed to create folder 'uploads/about_us/'. Please create it manually.");
                        }
                    }

                    // Validate file extension
                    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
                    $file_ext = strtolower(pathinfo($_FILES['sections']['name'][$section_id]['image'], PATHINFO_EXTENSION));

                    if (!in_array($file_ext, $allowed_exts)) {
                        throw new Exception("Invalid file type in Section #{$section_order}. Only JPG, PNG, GIF, and WEBP allowed.");
                    }

                    // Generate clean unique filename
                    $file_name = 'about-sec-' . time() . '-' . rand(1000, 9999) . '.' . $file_ext;
                    $target_path = $upload_dir . $file_name;

                    // Move uploaded file
                    if (!move_uploaded_file($file_tmp, $target_path)) {
                        throw new Exception("Failed to save image in folder. Check directory read/write permissions!");
                    }

                    // Delete old image if present
                    if (!empty($image_path) && file_exists($image_path)) {
                        @unlink($image_path);
                    }

                    $image_path = $target_path;
                }

                // Remove Image check
                if (isset($section_data['remove_image']) && $section_data['remove_image'] == '1') {
                    if (!empty($image_path) && file_exists($image_path)) {
                        @unlink($image_path);
                    }
                    $image_path = '';
                }

                // =========================================================
                // DATABASE INSERT / UPDATE
                // =========================================================
                if (strpos((string)$section_id, 'new') === 0) {
                    $stmt = $conn->prepare("INSERT INTO about_sections (title, content, image_url, section_order, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                    $stmt->bind_param('sssi', $title, $content, $image_path, $section_order);
                } else {
                    $int_id = intval($section_id);
                    $stmt = $conn->prepare("UPDATE about_sections SET title = ?, content = ?, image_url = ?, section_order = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->bind_param('sssii', $title, $content, $image_path, $section_order, $int_id);
                }

                if (!$stmt->execute()) {
                    throw new Exception("Database error: " . $conn->error);
                }
            }
        }

        // Handle deletions
        if (isset($_POST['delete_sections'])) {
            foreach ($_POST['delete_sections'] as $del_id) {
                $del_id = intval($del_id);
                $stmt = $conn->prepare("SELECT image_url FROM about_sections WHERE id = ?");
                $stmt->bind_param('i', $del_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($section = $result->fetch_assoc()) {
                    if (!empty($section['image_url']) && file_exists($section['image_url'])) {
                        @unlink($section['image_url']);
                    }
                }

                $stmt = $conn->prepare("DELETE FROM about_sections WHERE id = ?");
                $stmt->bind_param('i', $del_id);
                $stmt->execute();
            }
        }

        $success_message = "About Us sections & images saved successfully!";
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Fetch all sections
$sections = [];
$sql = "SELECT * FROM about_sections ORDER BY section_order ASC, id ASC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $sections[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>About Us Management | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <link rel="stylesheet" href="assets/css/contact.css">
    <style>
        .section-card {
            background: #fff;
            border: 1px solid #e5e9f2;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .section-handle {
            cursor: move;
            color: #8a96a3;
        }

        .image-preview {
            max-height: 120px;
            border-radius: 8px;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <?php include "top_nav.php"; ?>
                </div>
            </div>
        </div>

        <div class="main_content_iner">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="page-header mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <h2 class="mb-0">About Us Sections</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="about-form">
                            <?php if (!empty($success_message)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($success_message) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($error_message) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form method="post" enctype="multipart/form-data" id="sectionsForm">
                                <div id="sectionsContainer">
                                    <?php foreach ($sections as $index => $section): ?>
                                        <div class="section-card" data-id="<?= $section['id'] ?>">
                                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                                <h5 class="mb-0 d-flex align-items-center">
                                                    <i class="fas fa-arrows-alt section-handle me-2"></i>
                                                    <span>Section #<?= $index + 1 ?></span>
                                                </h5>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="delete_sections[]" value="<?= $section['id'] ?>" id="deleteSection<?= $section['id'] ?>">
                                                    <label class="form-check-label text-danger fw-bold" for="deleteSection<?= $section['id'] ?>">
                                                        <i class="fas fa-trash-alt me-1"></i> Delete Section
                                                    </label>
                                                </div>
                                            </div>

                                            <input type="hidden" name="sections[<?= $section['id'] ?>][current_image]" value="<?= htmlspecialchars($section['image_url']) ?>">

                                            <div class="row">
                                                <div class="col-md-8 mb-3">
                                                    <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="sections[<?= $section['id'] ?>][title]" required value="<?= htmlspecialchars($section['title']) ?>">
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label fw-bold">Section Order</label>
                                                    <input type="number" class="form-control" name="sections[<?= $section['id'] ?>][order]" value="<?= $section['section_order'] ?>">
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label fw-bold">Content <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="sections[<?= $section['id'] ?>][content]" rows="5" required><?= htmlspecialchars($section['content']) ?></textarea>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label fw-bold">Featured Image</label>
                                                    <!-- .file-upload-input class hata di gayi hai -->
                                                    <input type="file" name="sections[<?= $section['id'] ?>][image]" class="form-control" accept="image/*">
                                                    <small class="d-block text-muted mt-1">Recommended size: 1200x800px (JPG, PNG, GIF, WEBP)</small>

                                                    <?php if (!empty($section['image_url'])): ?>
                                                        <div class="current-image mt-2">
                                                            <p class="mb-1 text-muted small">Current Image:</p>
                                                            <img src="<?= htmlspecialchars($section['image_url']) ?>" alt="Current Section Image" class="image-preview">
                                                            <div class="form-check mt-2">
                                                                <input class="form-check-input" type="checkbox" name="sections[<?= $section['id'] ?>][remove_image]" value="1" id="removeImage<?= $section['id'] ?>">
                                                                <label class="form-check-label text-danger small" for="removeImage<?= $section['id'] ?>">
                                                                    Remove current image
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="text-center mt-4 mb-4">
                                    <button type="button" id="addSectionBtn" class="btn btn-dark px-4 py-2">
                                        <i class="fas fa-plus me-2"></i> Add New Section
                                    </button>
                                </div>

                                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                    <a href="dashboard.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fas fa-save me-2"></i> Save All Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "footer.php"; ?>
    </section>

    <!-- Template for new section -->
    <template id="newSectionTemplate">
        <div class="section-card" data-id="new">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-arrows-alt section-handle me-2"></i>
                    <span class="new-section-title">New Section</span>
                </h5>
                <button type="button" class="btn btn-sm btn-outline-danger remove-section-btn">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title_placeholder" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Section Order</label>
                    <input type="number" class="form-control" name="order_placeholder" value="0">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="content_placeholder" rows="5" required></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Featured Image</label>
                    <input type="file" name="image_placeholder" class="form-control file-upload-input" accept="image/*">
                    <small class="d-block text-muted mt-1">Recommended size: 1200x800px (JPG, PNG, GIF, WEBP)</small>
                </div>
            </div>
        </div>
    </template>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        // Initialize sortable for sections
        new Sortable(document.getElementById('sectionsContainer'), {
            handle: '.section-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                document.querySelectorAll('#sectionsContainer .section-card').forEach((card, index) => {
                    const orderInput = card.querySelector('input[name$="[order]"]');
                    if (orderInput) {
                        orderInput.value = index + 1;
                    }
                    const titleElement = card.querySelector('h5 span');
                    if (titleElement && !titleElement.classList.contains('new-section-title')) {
                        titleElement.textContent = `Section #${index + 1}`;
                    }
                });
            }
        });

        // Add new section with Unique Temporary ID
        document.getElementById('addSectionBtn').addEventListener('click', function() {
            const template = document.getElementById('newSectionTemplate');
            const clone = template.content.cloneNode(true);
            const container = document.getElementById('sectionsContainer');

            // Unique ID to prevent overwriting when adding multiple sections
            const uniqueId = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

            const card = clone.querySelector('.section-card');
            card.setAttribute('data-id', uniqueId);

            const titleInput = clone.querySelector('input[name="title_placeholder"]');
            titleInput.setAttribute('name', `sections[${uniqueId}][title]`);

            const orderInput = clone.querySelector('input[name="order_placeholder"]');
            orderInput.setAttribute('name', `sections[${uniqueId}][order]`);
            orderInput.value = container.querySelectorAll('.section-card').length + 1;

            const contentInput = clone.querySelector('textarea[name="content_placeholder"]');
            contentInput.setAttribute('name', `sections[${uniqueId}][content]`);

            const imageInput = clone.querySelector('input[name="image_placeholder"]');
            imageInput.setAttribute('name', `sections[${uniqueId}][image]`);

            container.appendChild(clone);

            // Event listener for remove button
            const newSection = container.lastElementChild;
            const removeBtn = newSection.querySelector('.remove-section-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    newSection.remove();
                });
            }

            // Initialize live image preview
            const fileInput = newSection.querySelector('.file-upload-input');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            let preview = newSection.querySelector('.image-preview');
                            if (!preview) {
                                preview = document.createElement('img');
                                preview.className = 'image-preview mt-2';
                                fileInput.after(preview);
                            }
                            preview.src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

        // Initialize image preview for existing sections
        document.querySelectorAll('.file-upload-input').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const sectionCard = input.closest('.section-card');
                        let preview = sectionCard.querySelector('.image-preview');
                        if (!preview) {
                            preview = document.createElement('img');
                            preview.className = 'image-preview mt-2';
                            input.after(preview);
                        }
                        preview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // Confirm before deleting checked sections
        document.getElementById('sectionsForm').addEventListener('submit', function(e) {
            const deleteCheckboxes = document.querySelectorAll('input[name^="delete_sections"]:checked');
            if (deleteCheckboxes.length > 0) {
                if (!confirm('Are you sure you want to delete the selected sections? This action cannot be undone.')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>

</html>