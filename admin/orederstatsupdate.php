<?php
// Include database connection
include '../db.php';

// Extract POST data (status and order ID)
extract($_POST);

// Update order status in the database
$update = mysqli_query($con, "UPDATE orders SET status = '$status' WHERE order_id = $id");

// Return success response if the update is successful
if ($update) {
    echo 1;
}

// Close the database connection
mysqli_close($con);
?>
