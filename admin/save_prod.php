<?php
// Include necessary files
include_once '../CloudServices.php'; // Cloud service utilities for AWS S3 operations
include("../db.php"); // Database connection

// Handle product deletion
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $pid = $_GET['product_id']; // Get the product ID from query parameters
    try {
        // Fetch the product image name from the database
        $sql = "SELECT product_image FROM products WHERE product_id = '$pid'";
        $query = mysqli_query($con, $sql);
        $pro = mysqli_fetch_array($query);

        // Delete the product image from the S3 bucket, if it exists
        if ($pro['product_image']) {
            CloudServices::deleteImageFromCloud($pro['product_image']);
        }

        // Delete the product record from the database
        mysqli_query($con, "DELETE FROM products WHERE product_id='$pid'");
        echo 1; // Indicate success
    } catch (Throwable $e) {
        echo $e->getMessage(); // Output error message if any exception occurs
    }
} else {
    // Handle product addition or update
    extract($_POST); // Extract POST data
    $tags = $tags ?? null; // Ensure tags have a default value if not set
    $picture_name = $_FILES['picture']['name']; // Get uploaded file name
    $picture_tmp_name = $_FILES['picture']['tmp_name']; // Get temporary file path

    try {
        // Generate a unique name for the uploaded image
        $pic_name = time() . "_" . $picture_name;

        if ($picture_tmp_name) {
            // Upload the image to the S3 bucket
            CloudServices::uploadImageToCloud($picture_tmp_name, $pic_name);
        }

        if (empty($product_id)) {
            // Insert a new product if no product ID is provided
            mysqli_query($con, "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
                                VALUES ('$category_id', '$brand_id', '$product_name', '$price', '$details', '$pic_name', '$tags')");
        } else {
            // Fetch the current product image name from the database
            $sql = "SELECT product_image FROM products WHERE product_id = '$product_id'";
            $query = mysqli_query($con, $sql);
            $pro = mysqli_fetch_array($query);

            if ($picture_tmp_name) {
                // If a new image is uploaded, delete the existing image from the S3 bucket
                if ($pro['product_image']) {
                    CloudServices::deleteImageFromCloud($pro['product_image']);
                }

                // Upload the new image to the S3 bucket
                CloudServices::uploadImageToCloud($picture_tmp_name, $pic_name);
            } else {
                // Retain the existing image if no new image is uploaded
                $pic_name = $pro['product_image'];
            }

            // Update the existing product details
            mysqli_query($con, "UPDATE products SET product_cat='$category_id', product_brand='$brand_id', product_title='$product_name', product_price='$price', product_desc='$details', product_image='$pic_name', product_keywords='$tags'
                                WHERE product_id=$product_id");
        }
        echo 1; // Indicate success
    } catch (Throwable $e) {
        echo $e->getMessage(); // Output error message if any exception occurs
    }
}

// Close the database connection
mysqli_close($con);
?>
