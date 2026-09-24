<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db-conn.php";

if (isset($_POST["add-categories"])) {
    
    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
        // File details
        $fileTmpPath = $_FILES['imageUpload']['tmp_name'];
        $fileName = $_FILES['imageUpload']['name'];
        $fileSize = $_FILES['imageUpload']['size'];
        $fileType = $_FILES['imageUpload']['type'];

        // Get file extension
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allowed file extensions
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        // Check if file type is allowed
        if (in_array($fileExtension, $allowedExtensions)) {
            // Create a unique filename
            $newFileName = uniqid('img_', true) . '.' . $fileExtension;

            // Define upload directory
            $uploadDir = 'uploads/category/';

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create with full permissions
            }

            // Final file destination
            $destPath = $uploadDir . $newFileName;

            // Move uploaded file
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                echo "✅ File uploaded successfully! <br>";
                echo "📂 Saved at: <a href='$destPath'>$destPath</a>";
            } else {
                echo "❌ Error: Could not move file!";
            }
        } else {
            echo "❌ Error: Only JPG, JPEG, PNG, and GIF files are allowed!";
        }
    }

    $cate_id = mt_rand(11111, 99999);
    $cate_name = mysqli_real_escape_string($conn, $_POST["cate_name"]);
    $meta_title = mysqli_real_escape_string($conn, $_POST["meta_title"]);
    $meta_key = mysqli_real_escape_string($conn, $_POST["meta_key"]);
    $meta_desc = mysqli_real_escape_string($conn, $_POST["meta_desc"]);
    $slug_url = strtolower(str_replace(" ", "-", $cate_name)); // Temp fix for SlugUrl()

    $sql = "INSERT INTO `categories` (`cate_id`, `categories`, `meta_title`, `meta_desc`, `meta_key`, `image`, `slug_url`, `status`, `added_on`) 
            VALUES ('$cate_id', '$cate_name', '$meta_title', '$meta_desc', '$meta_key', '$fileName', '$slug_url', 1, NOW())";

    $check = mysqli_query($conn, $sql);

    if (!$check) {
        die("SQL Error: " . mysqli_error($conn)); // Debugging SQL error
    } else {
        echo "<script>alert('Inserted Successfully!'); window.location.href = 'view-categories.php';</script>";
    }
}

if (isset($_POST["add-sub-categories"])) {

    $uploadedImage = ''; // default empty value
    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
        // File details
        $fileTmpPath = $_FILES['imageUpload']['tmp_name'];
        $fileName = $_FILES['imageUpload']['name'];
        // $fileSize and $fileType are retrieved but not used
        // $fileSize = $_FILES['imageUpload']['size'];
        // $fileType = $_FILES['imageUpload']['type'];

        // Get file extension
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allowed file extensions
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        // Check if file type is allowed
        if (in_array($fileExtension, $allowedExtensions)) {
            // Create a unique filename
            $newFileName = uniqid('img_', true) . '.' . $fileExtension;

            // Define upload directory
            $uploadDir = 'uploads/sub-category/';

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create with full permissions
            }

            // Final file destination
            $destPath = $uploadDir . $newFileName;

            // Move uploaded file
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                echo "✅ File uploaded successfully! <br>";
                echo "📂 Saved at: <a href='$destPath'>$destPath</a>";
                $uploadedImage = $newFileName; // use the new unique filename for database
            } else {
                echo "❌ Error: Could not move file!";
            }
        } else {
            echo "❌ Error: Only JPG, JPEG, PNG, GIF, and WEBP files are allowed!";
        }
    }

    // If no file was uploaded, you may want to set a default image filename or leave empty.
    if (empty($uploadedImage)) {
        // echo "<script>alert('Image is not uploaded!!')</script>";
        // $uploadedImage = 'default_image.jpg'; 
        // or empty string if you wish
    }

    $cate_id    = mt_rand(11111, 99999);
    $cate_name  = mysqli_real_escape_string($conn, $_POST["cate_name"]);
    $meta_title = mysqli_real_escape_string($conn, $_POST["meta_title"]);
    $meta_key   = mysqli_real_escape_string($conn, $_POST["meta_key"]);
    $meta_desc  = mysqli_real_escape_string($conn, $_POST["meta_desc"]);
    $added_on   = date('M d, Y');
    $parent_id  = mysqli_real_escape_string($conn, $_POST['parent_id']);
    $slug_url   = strtolower(str_replace(" ", "-", $cate_name));

    $sql = "INSERT INTO `sub_categories`( `parent_id`,`cate_id`, `categories`, `meta_title`, `meta_desc`, `meta_key`, `sub_cat_img`, `slug_url`, `status`, `added_on`) 
            VALUES ('$parent_id','$cate_id','$cate_name','$meta_title','$meta_desc','$meta_key', '$uploadedImage', '$slug_url', 1, '$added_on')";

    $check = mysqli_query($conn, $sql);
    if ($check) {
        ?>
        <script type="text/javascript">
            alert('Inserted Successfully!');
            window.location.href = "view-sub-categories.php";
        </script>
        <?php
    } else {
        echo "Error inserting record: " . mysqli_error($conn);
    }
}

function get_Category()
{
    include "db-conn.php";

    $sql = "SELECT * FROM `categories` ORDER BY id DESC";
    $check = mysqli_query($conn, $sql);
    $sno = 1;
    while ($result = mysqli_fetch_assoc($check)) {
        echo $output = "<tr>
        <td>" . $sno++ . "</td>
        <td>" . $result['cate_id'] . "</td>
        <td>" . $result['categories'] . "</td>
        <td>" . $result['slug_url'] . "</td>
        <td>" . $result['status'] . "</td>
        <td><a href='delete-category.php?id=" . $result['cate_id'] . "' onclick='return confirm(\"Are you sure you want to delete this category?\")'><i class='fa-solid fa-trash text-danger fs-4'></i></a></td>
        <td><a href='edit_category.php?id=" . $result['cate_id']."'><i class='fa-solid fa-file-pen fs-4'></i></a></td>
        <td>" . $result['added_on'] . "</td>
        </tr>";
    }
}

// if(isset($_POST["add-product"])){
//     $pro_id = mt_rand(11111, 99999);
//     $pro_name = $_POST['pro_name'];
//     $pro_cate = $_POST['pro_cate'];
//     $pro_sub_cate = $_POST['pro_sub_cate'];
//     $description = $_POST['pro_desc'];
//     $new_arrival = $_POST['new_arrival'];
//     $mrp = $_POST['mrp'];
//     $selling_price = $_POST['selling_price'];
//     $stock = $_POST['stock'];
//     $status = $_POST['status'];

//     $filename = $_FILES['pro_img']['name'];
//     $tmepname = $_FILES['pro_img']['tmp_name'];
//     $destination = 'assests/img/uploads/'.$filename;
//     move_uploaded_file($tmepname,$destination);

//     $meta_title = $_POST["meta_title"];
//     $meta_key = $_POST["meta_key"];
//     $meta_desc = $_POST["meta_desc"];
//     $added_on = date('M d, Y');
//     $slug_url = SlugUrl($pro_name); 


//     $sql ="INSERT INTO `products`(`pro_id`, `pro_name`, `pro_cate`, `pro_sub_cate`, `short_desc`, `description`,`new_arrival`, `mrp`, `selling_price`, `stock`, `pro_img`, `status`,`slug_url`, `meta_title`, `meta_desc`, `meta_key`, `added_on`) VALUES ('$pro_id','$pro_name','$pro_cate','$pro_sub_cate','$description','$new_arrival','$mrp','$selling_price','$stock','$status','$slug_url','$filename','$meta_title','$meta_key','$meta_desc','$added_on','$added_on')";

//     $check = mysqli_query($conn, $sql);
//     if($check){
//         


    if (isset($_POST["add-product"])) {
        $pro_id = mt_rand(11111, 99999);
        $pro_name       = mysqli_real_escape_string($conn, $_POST['pro_name']);
        $brand_name       = mysqli_real_escape_string($conn, $_POST['brand_name']);
        $pro_cate       = mysqli_real_escape_string($conn, $_POST['pro_cate']);
        $pro_sub_cate   = mysqli_real_escape_string($conn, $_POST['pro_sub_cate']);
        $short_description    = mysqli_real_escape_string($conn, $_POST['short_desc']);
        $description    = mysqli_real_escape_string($conn, $_POST['pro_desc']);
        $new_arrival    = mysqli_real_escape_string($conn, $_POST['new_arrival']);
        $trending    = mysqli_real_escape_string($conn, $_POST['trending']);
        $whole_sale_selling_price  = mysqli_real_escape_string($conn, $_POST['whole_selling_price']);
        $qty            = mysqli_real_escape_string($conn, $_POST['qty']);
        $mrp            = mysqli_real_escape_string($conn, $_POST['mrp']);
        $selling_price  = mysqli_real_escape_string($conn, $_POST['selling_price']);
        $stock          = mysqli_real_escape_string($conn, $_POST['stock']);
        $status         = mysqli_real_escape_string($conn, $_POST['status']);

            // Ensure the folder exists and is writable
        $folder = 'assets/img/uploads/';
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        // Loop through each uploaded file
        foreach ($_FILES['pro_img']['tmp_name'] as $key => $tempname) {
            // Get the original file name for the current image
            $filename = $_FILES['pro_img']['name'][$key];
            $destination = $folder . $filename;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($tempname, $destination)) {
                echo "Image uploaded successfully: " . $filename . "<br>";
            } else {
                echo "Failed to upload image: " . $filename . "<br>";
            }
        }



        $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title']);
        $meta_key = mysqli_real_escape_string($conn, $_POST["meta_key"]);
        $meta_desc = mysqli_real_escape_string($conn, $_POST["meta_desc"]);
        $added_on = date('M d, Y');
        $slug_url = strtolower(str_replace(" ", "-", $pro_name));
        // $slug_url = SlugUrl($pro_name); 
        // Assuming this function generates a valid slug

        // Corrected SQL query
        $sql = $sql = "INSERT INTO `products`(`pro_id`, `pro_name`,`brand_name` , `pro_cate`, `pro_sub_cate`, `short_desc`, `description`, `new_arrival`,`trending`, `qty`, `mrp`, `selling_price`, `whole_sale_selling_price`, `stock`, `pro_img`, `status`, `slug_url`, `meta_title`, `meta_desc`, `meta_key`, `added_on`) 
VALUES ('$pro_id', '$pro_name', '$brand_name','$pro_cate', '$pro_sub_cate', '$short_description', '$description', '$new_arrival', '$trending', '$qty','$mrp', '$selling_price', '$whole_sale_selling_price', '$stock', '$filename', '$status', '$slug_url', '$meta_title', '$meta_desc', '$meta_key', '$added_on')";


        // Execute the query
        $check = mysqli_query($conn, $sql);
        if ($check) {
    ?>
        <script type="text/javascript">
            alert('Inserted Successfully!');
            window.location.href = "add-products.php";
        </script>
    <?php
        } else {
            echo "Error: " . mysqli_error($conn);  // Optional: Display any error message from MySQL
        }
    }





    function get_Sub_Category()
    {
        include "db-conn.php";
    
        $searchQuery = "";
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = mysqli_real_escape_string($conn, $_GET['search']);
            $searchQuery = " WHERE `categories` LIKE '%$search%' OR `slug_url` LIKE '%$search%' ";
        }
    
        $sql = "SELECT * FROM `sub_categories` $searchQuery ORDER BY id DESC";
        $check = mysqli_query($conn, $sql);
        $sno = 1;
    
        if ($check && mysqli_num_rows($check) > 0) {
            while ($result = mysqli_fetch_assoc($check)) {
                $parent_id = $result['parent_id'];
    
                // Fetch parent category
                $sql2 = "SELECT `categories` FROM `categories` WHERE `cate_id` = $parent_id";
                $check2 = mysqli_query($conn, $sql2);
                $parent_cate = ""; // Default empty if not found
    
                if ($check2 && mysqli_num_rows($check2) > 0) {
                    $parent = mysqli_fetch_assoc($check2);
                    $parent_cate = $parent['categories'];
                }
    
                // Output row
                echo "<tr>
                    <td>" . $sno++ . "</td>
                    <td>" . htmlspecialchars($result['cate_id']) . "</td>
                    <td>" . htmlspecialchars(ucwords($result['categories'])) . "</td>
                    <td>" . htmlspecialchars($parent_cate) . "</td>
                    <td>" . htmlspecialchars($result['slug_url']) . "</td>
                    <td>" . htmlspecialchars($result['status']) . "</td>
                    <td>
                            <a href='delete_sub_category.php?id=" . $result['cate_id'] . "' 
                            onclick='return confirm(\"Are you sure you want to delete this sub-category?\")' 
                            class='btn btn-danger'>Delete</a>
                    </td>
                    <td><a href='edit_sub_category.php?id=" . $result['cate_id']."'><i class='fa-solid fa-file-pen fs-4'></i></a></td>
                    <td>" . htmlspecialchars($result['added_on']) . "</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='9' class='text-center'>No subcategories found</td></tr>";
        }
    }
    


    if (isset($_POST['cate_id'])) {
        $p_id = $_POST['cate_id'];
        $sql = "SELECT * FROM `sub_categories` where `parent_id` = '$p_id' ORDER BY id DESC";
        $check = mysqli_query($conn, $sql);
    ?>
    <option value="">Select</option>
<?php
        while ($result = mysqli_fetch_assoc($check)) {
            echo "<option value=" . $result['cate_id'] . ">" . $result['categories'] . "</option>";
        }
    }

    function SlugUrl($string)
    {
        $slug = preg_replace('/[^a-zA-Z0-9 -]/', '', $string);
        $slug = str_replace('', '-', $slug);
        $slug = strtolower($slug);
        return ($slug);
    }



// Get a single category by ID
function get_category_by_id($cat_id) {
    global $conn;
    $cat_id = mysqli_real_escape_string($conn, $cat_id);
    $sql = "SELECT * FROM `categories` WHERE cate_id = '$cat_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// Update a category



function get_sub_category_by_id($cat_id) {
    global $conn;
    $cat_id = mysqli_real_escape_string($conn, $cat_id);
    $sql = "SELECT * FROM `sub_categories` WHERE cate_id = '$cat_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}




?>