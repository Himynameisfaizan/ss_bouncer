<?php
session_start();
include "db-conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Inputs ko clean kiya (Prepared Statement automatically SQL injection se bacha lega)
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = trim($_POST['author']);
    $status = trim($_POST['status']);

    // 2. Slug generate kiya
    $slug_url = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));

    // 3. Image Upload Handling
    $image_name = '';
    $upload_ok = true; // Is flag se hum decide karenge ki DB me insert karna hai ya nahi

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        // STANDARD PATH: Wahi path use kiya hai jo product page pe chal raha hai
        $uploadDir = "assets/img/uploads/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = basename($_FILES['image']['name']);
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Unique file name generate kiya
        $newFileName = time() . '_blog.' . $fileType;
        $uploadPath = $uploadDir . $newFileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if ($fileSize <= 5000000) { // 5MB Limit
                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $image_name = $newFileName;
                } else {
                    $_SESSION['error'] = "Failed to upload image. Folder permissions check karein: " . $uploadDir;
                    $upload_ok = false;
                }
            } else {
                $_SESSION['error'] = "File is too large. Maximum size is 5MB.";
                $upload_ok = false;
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, WEBP & GIF allowed.";
            $upload_ok = false;
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Agar PHP config limit exceed ho jaye (e.g. > 2MB default upload limit)
        $_SESSION['error'] = "Server Image Upload Error Code: " . $_FILES['image']['error'];
        $upload_ok = false;
    }

    // 4. Database Insert (Sirf tabhi chalega jab upload_ok true hoga)
    if ($upload_ok) {
        // Timestamps (created_at, updated_at) DB khud laga lega schema ke hisaab se
        $sql = "INSERT INTO blogs (title, content, slug_url, image, author, status) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $title, $content, $slug_url, $image_name, $author, $status);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Blog added successfully!";
                header("Location: view-all-blog.php");
                exit();
            } else {
                // Ye exactly batayega agar ENUM status ya kisi aur wajah se query fail hui
                $_SESSION['error'] = "Database Execution Error: " . mysqli_stmt_error($stmt);
            }
        } else {
            $_SESSION['error'] = "SQL Prepare Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Add New Blog | Khetarpal Trading Co.</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">

    <?php include "links.php"; ?>

    <!-- CKEditor CDN -->
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <!-- Select2 -->
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
                                        <h2 class="text-center">Add New Blog</h2>
                                    </div>
                                </div>
                            </div>

                            <div class="white_card_body">

                                <!-- Display Session Messages -->
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger font-weight-bold">
                                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error']);
                                                                                    unset($_SESSION['error']); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success font-weight-bold">
                                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']);
                                                                            unset($_SESSION['success']); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="col-md-12 mb-4">
                                    <a href="view-all-blog.php" class="btn btn-danger">
                                        <i class="fas fa-list"></i> View All Blogs
                                    </a>
                                </div>

                                <div class="card-body">
                                    <!-- Form Action is self -->
                                    <form method="POST" action="" enctype="multipart/form-data" class="p-4 shadow bg-white rounded">
                                        <div class="row">

                                            <!-- LEFT COLUMN -->
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Blog Title *</label>
                                                    <input type="text" name="title" class="form-control" placeholder="Enter blog title" required
                                                        value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Content (Full Details) *</label>
                                                    <textarea name="content" class="form-control" rows="10" id="editor" required>
                                                        <?= isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '' ?>
                                                    </textarea>
                                                </div>
                                            </div>

                                            <!-- RIGHT COLUMN -->
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Author Name *</label>
                                                    <input type="text" name="author" class="form-control" placeholder="Admin or Author Name" required
                                                        value="<?= isset($_POST['author']) ? htmlspecialchars($_POST['author']) : 'Admin' ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Status *</label>
                                                    <!-- Make sure these values match your DB ENUM exactly -->
                                                    <select name="status" class="form-control select2" required>
                                                        <option value="published" <?= (!isset($_POST['status']) || (isset($_POST['status']) && $_POST['status'] == 'published') ? 'selected' : '') ?>>Published</option>
                                                        <option value="draft" <?= (isset($_POST['status']) && $_POST['status'] == 'draft') ? 'selected' : '' ?>>Draft</option>
                                                        <option value="archived" <?= (isset($_POST['status']) && $_POST['status'] == 'archived') ? 'selected' : '' ?>>Archived</option>
                                                    </select>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="form-label font-weight-bold">Featured Image *</label>
                                                    <input type="file" name="image" class="form-control" required accept="image/*">
                                                    <small class="text-muted d-block mt-1">Max size: 5MB (JPG, PNG, WEBP)</small>
                                                    <div class="mt-3 text-center border p-2 bg-light rounded" style="min-height: 150px;">
                                                        <img id="imagePreview" src="#" alt="Image preview" style="max-width: 100%; max-height: 200px; display: none;">
                                                        <span id="previewPlaceholder" class="text-muted"><br><br>Image Preview Here</span>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm">
                                                        <i class="fas fa-plus-circle"></i> Publish Blog
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

        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            // Initialize CKEditor
            CKEDITOR.replace('editor', {
                toolbar: [{
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList', 'Blockquote']
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
                        name: 'document',
                        items: ['Source']
                    }
                ],
                height: 400
            });

            // Initialize Select2 & Image Preview
            $(document).ready(function() {
                $('.select2').select2({
                    minimumResultsForSearch: Infinity
                });

                // Better Image Preview Logic
                $('input[type="file"]').change(function(e) {
                    if (this.files && this.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#previewPlaceholder').hide();
                            $('#imagePreview').attr('src', e.target.result).fadeIn();
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            });
        </script>
    </section>
</body>

</html>