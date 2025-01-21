<?php
// Include database connection
include ('../db.php');

// Extract the incoming POST data
extract($_POST);

// Handle brand-related operations
if (isset($_POST['brand'])) {
    // Delete a brand
    if ($_POST['type'] == 'delete') {
        $data = mysqli_query($con, "DELETE FROM brands WHERE brand_id = " . $id);
        if ($data) {
            echo 1;
        }
    }
    // Add a new brand
    elseif ($_POST['type'] == 'add') {
        $insert = mysqli_query($con, "INSERT INTO brands SET brand_title = '$name'");
        if ($insert) {
            echo 1;
        }
    }
    // Update an existing brand
    elseif ($_POST['type'] == 'update') {
        $save = mysqli_query($con, "UPDATE brands SET brand_title = '$name' WHERE brand_id = '$id'");
        if ($save) {
            echo 1;
        }
    }
    exit();
}

// Handle category-related operations
elseif (isset($_POST['cat'])) {
    // Delete a category
    if ($_POST['type'] == 'delete') {
        $data = mysqli_query($con, "DELETE FROM categories WHERE cat_id = " . $id);
        if ($data) {
            echo 1;
        }
    }
    // Add a new category
    elseif ($_POST['type'] == 'add') {
        $insert = mysqli_query($con, "INSERT INTO categories SET cat_title = '$name'");
        if ($insert) {
            echo 1;
        }
    }
    // Update an existing category
    elseif ($_POST['type'] == 'update') {
        $save = mysqli_query($con, "UPDATE categories SET cat_title = '$name' WHERE cat_id = '$id'");
        if ($save) {
            echo 1;
        }
    }
    exit();
}

// Close the database connection
mysqli_close($con);
?>
