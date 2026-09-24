<?php
session_start();
include "db-conn.php";

// Initialize variables
$error = '';
$success = '';
$blog = [];

// Get ID safely (from GET on load, or POST on submit)
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($id > 0) {
    // Fetch existing blog data
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $blog = $result->fetch_assoc();
    } else {
        header("Location: view-all-blog.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: view-all-blog.php");
    exit();
}

// ==========================================
// HANDLE FORM SUBMISSION
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    // Added Missing Fields
    $author = trim($_POST['author']);
    $status = trim($_POST['status']);

    if (empty($title) || empty($content)) {
        $error = "Title and content are required fields.";
    } else {
        $slug = generateSlug($title);

        // Prepare data array for update
        $update_data = [
            'title' => $title,
            'content' => $content,
            'author' => $author,
            'status' => $status,
            'slug_url' => $slug
            // updated_at DB schema se automatic update hoga
        ];

        // Handle "Remove Image" Checkbox
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            if (!empty($blog['image'])) {
                deleteImage($blog['image']);
            }
            $update_data['image'] = ''; // Set empty string in DB
            $blog['image'] = ''; // Update local variable for UI
        }

        // Handle New Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = handleImageUpload($_FILES['image'], $id);

            if ($upload_result['success']) {
                $update_data['image'] = $upload_result['filename'];

                // Delete old image if exists and wasn't already removed by checkbox
                if (!empty($blog['image']) && empty($_POST['remove_image'])) {
                    deleteImage($blog['image']);
                }

                // Update local variable for UI
                $blog['image'] = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }

        // Only proceed if no upload errors
        if (empty($error)) {
            // Dynamic Query Builder
            $set_parts = [];
            $params = [];
            $types = '';

            foreach ($update_data as $field => $value) {
                $set_parts[] = "$field = ?";
                $params[] = $value;
                $types .= 's'; // all are strings
            }

            // Add ID at the end
            $params[] = $id;
            $types .= 'i';

            $query = "UPDATE blogs SET " . implode(', ', $set_parts) . " WHERE id = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $success = "Blog updated successfully!";
                // Update local array so UI shows new data immediately
                $blog['title'] = $title;
                $blog['content'] = $content;
                $blog['author'] = $author;
                $blog['status'] = $status;
            } else {
                $error = "Database Error: " . $stmt->error;
            }
        }
    }
}

// ==========================================
// HELPER FUNCTIONS
// ==========================================
function generateSlug($string)
{
    $slug = strtolower($string);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', ' ', $slug);
    $slug = preg_replace('/\s/', '-', $slug);
    return trim($slug, '-');
}

function handleImageUpload($file, $blog_id)
{
    // CORRECTED PATH
    $uploadDir = "assets/img/uploads/";
    $allowed_types = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'jfif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $image_name = basename($file['name']);
    $image_tmp = $file['tmp_name'];
    $image_size = $file['size'];
    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

    if (!in_array($image_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, WEBP & GIF allowed.'];
    }
    if ($image_size > $max_size) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit.'];
    }

    // Unique file name
    $new_filename = time() . '_blog_' . $blog_id . '.' . $image_ext;
    $destination = $uploadDir . $new_filename;

    if (move_uploaded_file($image_tmp, $destination)) {
        return ['success' => true, 'filename' => $new_filename];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file. Check folder permissions.'];
    }
}

function deleteImage($filename)
{
    // CORRECTED PATH
    $filepath = "assets/img/uploads/" . $filename;
    if (file_exists($filepath) && !is_dir($filepath)) {
        unlink($filepath);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Blog | Khetarpal Trading Co.</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">

    <?php include "links.php"; ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <?php include "top_nav.php"; ?>
                </div>
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
                                        <h2 class="text-center">Update Blog Details</h2>
                                    </div>
                                </div>
                            </div>

                            <div class="white_card_body">
                                <div class="card-body">

                                    <!-- Alerts -->
                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger font-weight-bold">
                                            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($success)): ?>
                                        <div class="alert alert-success font-weight-bold">
                                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mb-4">
                                        <a href="view-all-blog.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Back to Blogs
                                        </a>
                                    </div>

                                    <!-- Edit Form -->
                                    <form method="POST" action="" enctype="multipart/form-data" class="p-4 shadow bg-white rounded">

                                        <!-- ID is vital here -->
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($blog['id']); ?>">

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Blog Title *</label>
                                                    <input type="text" name="title" class="form-control"
                                                        value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Content *</label>
                                                    <textarea name="content" class="form-control" rows="10" id="pro_desc" required>
                                                        <?php echo htmlspecialchars($blog['content']); ?>
                                                    </textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-4">

                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Author *</label>
                                                    <input type="text" name="author" class="form-control"
                                                        value="<?php echo htmlspecialchars($blog['author']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Status *</label>
                                                    <select name="status" class="form-control select2" required>
                                                        <option value="published" <?php echo ($blog['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                                                        <option value="draft" <?php echo ($blog['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                                        <option value="archived" <?php echo ($blog['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3 border p-3 bg-light rounded">
                                                    <label class="form-label font-weight-bold">Current Featured Image:</label><br>

                                                    <?php if (!empty($blog['image'])): ?>
                                                        <img src="assets/img/uploads/<?php echo htmlspecialchars($blog['image']); ?>"
                                                            style="max-width:100%; max-height:200px;" class="img-thumbnail mb-3">

                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                                                            <label class="form-check-label text-danger font-weight-bold" for="remove_image">
                                                                <i class="fas fa-trash"></i> Remove current image
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <p class="text-muted"><i class="fas fa-image"></i> No image uploaded</p>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="form-label font-weight-bold">Upload New Image (Replaces old):</label>
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                    <small class="text-muted">Max size: 5MB (JPG, PNG, WEBP)</small>
                                                </div>

                                                <div class="mb-3">
                                                    <button type="submit" name="update" class="btn btn-success btn-lg btn-block shadow-sm">
                                                        <i class="fas fa-save"></i> Update Blog
                                                    </button>
                                                </div>

                                            </div>
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

        <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    minimumResultsForSearch: Infinity
                });
            });

            CKEDITOR.replace('pro_desc', {
                toolbar: [{
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList', '-', 'Blockquote']
                    },
                    {
                        name: 'links',
                        items: ['Link', 'Unlink']
                    },
                    {
                        name: 'insert',
                        items: ['Image', 'Table']
                    },
                    {
                        name: 'tools',
                        items: ['Maximize']
                    },
                    {
                        name: 'document',
                        items: ['Source']
                    }
                ],
                height: 350
            });
        </script>
    </section>
</body>

</html>